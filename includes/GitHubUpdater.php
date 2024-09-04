<?php
/**
 * h/t https://ryansechrest.com/2024/04/how-to-enable-wordpress-to-update-your-custom-plugin-hosted-on-github/
 */

class GitHubUpdater
{
  private string $file = '';

  private string $gitHubUrl = '';

  private string $gitHubPath = '';
  private string $gitHubOrg = '';
  private string $gitHubRepo = '';
  private string $gitHubBranch = 'main';
  private string $gitHubAccessToken = '';

  private string $pluginFile = '';
  private string $pluginDir = '';
  private string $pluginFilename = '';
  private string $pluginSlug = '';
  private string $pluginUrl = '';
  private string $pluginVersion = '';

  private string $testedWpVersion = '';

  public function __construct(string $file)
  {
    $this->file = $file;

    $this->load();
  }

  private function load(): void
  {
    $pluginData = get_file_data(
      $this->file,
      [
        'PluginURI' => 'Plugin URI',
        'Version' => 'Version',
        'UpdateURI' => 'Update URI',
      ]
    );

    $pluginUri = $pluginData['PluginURI'] ?? '';
    $updateUri = $pluginData['UpdateURI'] ?? '';
    $version = $pluginData['Version'] ?? '';

    if (!$pluginUri || !$updateUri || !$version) {
      $this->addAdminNotice('Plugin <b>%s</b> is missing one or more required header fields: <b>Plugin URI</b>, <b>Version</b>, and/or <b>Update URI</b>.');
      return;
    };

    $this->gitHubUrl = $updateUri;
    $this->gitHubPath = trim(
      parse_url($updateUri, PHP_URL_PATH),
      '/'
    );
    list($this->gitHubOrg, $this->gitHubRepo) = explode(
      '/', $this->gitHubPath
    );
    $this->pluginFile = str_replace(
      WP_PLUGIN_DIR . '/', '', $this->file
    );
    list($this->pluginDir, $this->pluginFilename) = explode(
      '/', $this->pluginFile
    );
    $this->pluginSlug = sprintf(
      '%s-%s', $this->gitHubOrg, $this->gitHubRepo
    );
    $this->pluginUrl = $pluginUri;
    $this->pluginVersion = $version;
  }

  public function add(): void
  {
    $this->updatePluginDetailsUrl();
    $this->checkPluginUpdates();
    $this->prepareHttpRequestArgs();
    $this->moveUpdatedPlugin();
  }

  private function addAdminNotice(string $message): void
  {
    add_action('admin_notices', function () use ($message) {
      $pluginFile = str_replace(
        WP_PLUGIN_DIR . '/', '', $this->file
      );
      echo '<div class="notice notice-error">';
      echo '<p>' . sprintf($message, $pluginFile) . '</p>';
      echo '</div>';
    });
  }
  
  private function updatePluginDetailsUrl(): void
  {
    add_filter(
      'admin_url',
      [$this, '_updatePluginDetailsUrl'],
      10,
      4
    );
  }

  public function _updatePluginDetailsUrl(string $url, string $path): string
  {
    $query = 'plugin=' . $this->pluginSlug;

    if (!str_contains($path, $query)) return $url;

    return sprintf(
      '%s?TB_iframe=true&width=600&height=550',
      $this->pluginUrl
    );
  }

  private function checkPluginUpdates(): void
  {
    add_filter(
      'update_plugins_github.com',
      [$this, '_checkPluginUpdates'],
      10,
      3
    );
  }

  public function _checkPluginUpdates(
      array|false $update, array $data, string $file
  ): array|false
  {
    $updateUri = $data['UpdateURI'] ?? '';

    $gitHubPath = trim(
      parse_url($updateUri, PHP_URL_PATH),
      '/'
    );

    if ($gitHubPath !== $this->gitHubPath) return false;

    $fileContents = $this->getRemotePluginFileContents();

    preg_match_all(
        '/\s+\*\s+Version:\s+(\d+(\.\d+){0,2})/',
        $fileContents,
        $matches
    );

    $newVersion = $matches[1][0] ?? '';

    if (!$newVersion) return false;

    return [
      'id' => $this->gitHubUrl,
      'slug' => $this->pluginSlug,
      'plugin' => $this->pluginFile,
      'version' => $newVersion,
      'url' => $this->pluginUrl,
      'package' => $this->getRemotePluginZipFile(),
      'icons' => [
          '2x' => $this->pluginUrl . '/icon-256x256.png',
          '1x' => $this->pluginUrl . '/icon-128x128.png',
      ],
      'tested' => $this->testedWpVersion,
    ];
  }


  private function getRemotePluginFileContents(): string
  {
    return $this->gitHubAccessToken
      ? $this->getPrivateRemotePluginFileContents()
      : $this->getPublicRemotePluginFileContents();
  }

  private function getPublicRemotePluginFileContents(): string
  {
    $remoteFile = $this->getPublicRemotePluginFile($this->pluginFilename);

    return wp_remote_retrieve_body(
      wp_remote_get($remoteFile)
    );
  }

  private function getPublicRemotePluginFile(string $filename): string
  {
    return sprintf(
      'https://raw.githubusercontent.com/%s/%s/%s',
      $this->gitHubPath,
      $this->gitHubBranch,
      $filename
    );
  }

  private function getPrivateRemotePluginFileContents(): string
  {
    $remoteFile = $this->getPrivateRemotePluginFile($this->pluginFilename);

    return wp_remote_retrieve_body(
      wp_remote_get(
        $remoteFile,
        [
          'headers' => [
            'Authorization' => 'Bearer ' . $this->gitHubAccessToken,
            'Accept' => 'application/vnd.github.raw+json',
          ]
        ]
      )
    );
  }

  private function getPrivateRemotePluginFile(string $filename): string
  {
      // Generate URL to private remote plugin file.
      return sprintf(
          'https://api.github.com/repos/%s/contents/%s?ref=%s',
          $this->gitHubPath,
          $filename,
          $this->gitHubBranch
      );
  }

  private function getRemotePluginZipFile(): string
  {
      return $this->gitHubAccessToken
          ? $this->getPrivateRemotePluginZipFile()
          : $this->getPublicRemotePluginZipFile();
  }

  private function getPublicRemotePluginZipFile(): string
  {
      return sprintf(
          'https://github.com/%s/archive/refs/heads/%s.zip',
          $this->gitHubPath,
          $this->gitHubBranch
      );
  }

  private function getPrivateRemotePluginZipFile(): string
  {
      return sprintf(
          'https://api.github.com/repos/%s/zipball/%s',
          $this->gitHubPath,
          $this->gitHubBranch
      );
  }

  private function prepareHttpRequestArgs(): void
  {
    add_filter(
      'http_request_args',
      [$this, '_prepareHttpRequestArgs'],
      10,
      2
    );
  }

  public function _prepareHttpRequestArgs(array $args, string $url): array
  {
    if ($url !== $this->getPrivateRemotePluginZipFile()) return $args;

    $args['headers']['Authorization'] = 'Bearer ' . $this->gitHubAccessToken;
    $args['headers']['Accept'] = 'application/vnd.github+json';

    return $args;
  }

  private function moveUpdatedPlugin(): void
  {
    add_filter(
        'upgrader_install_package_result',
        [$this, '_moveUpdatedPlugin']
    );
  }

  public function _moveUpdatedPlugin(array $result): array
  {
    $newPluginPath = $result['destination'] ?? '';

    if (!$newPluginPath) return $result;

    $pluginRootPath = $result['local_destination'] ?? WP_PLUGIN_DIR;

    $oldPluginPath = $pluginRootPath . '/' . $this->pluginDir;

    move_dir($newPluginPath, $oldPluginPath);

    $result['destination'] = $oldPluginPath;
    $result['destination_name'] = $this->pluginDir;
    $result['remote_destination'] = $oldPluginPath;

    return $result;
  }

  public function setBranch(string $branch): self
  {
    $this->gitHubBranch = $branch;

    return $this;
  }

  public function setAccessToken(string $accessToken): self
  {
    $this->gitHubAccessToken = $accessToken;

    return $this;
  }

  public function setTestedWpVersion(string $version): self
  {
    $this->testedWpVersion = $version;

    return $this;
  }



}
