<?php if (isset($socials->platforms)) : ?>
  <div class="social-share">
    <h2 class="social-share--heading" name="social-share"><?php _e("Share on:", 'swellsocial'); ?></h2>
    <ul aria-labelledby="social-share" class="list-none ml-0 flex gap-4">
      <?php foreach ($socials->platforms as $social) : ?>
        <li>
          <button
            class="social-share--button"
            data-sharer="<?php echo $social['name'] == "x" ? "twitter" : $social['name']; ?>"
            <?php if ( in_array($social['name'], $socials->options['title']) && isset($social['copy']) ): ?>
              data-title="<?php echo $social['copy'] ?: get_the_title(); ?>"
            <?php endif; ?>
            <?php if ( in_array($social['name'], $socials->options['hashtag']) && $socials->hashtags ): ?>
              data-hashtag="<?php echo $socials->hashtags[0]; ?>"
            <?php endif; ?>
            <?php if ( in_array($social['name'], $socials->options['hashtags']) && $socials->hashtags ): ?>
              data-hashtags="<?php echo implode(",", $socials->hashtags); ?>"
            <?php endif; ?>
            <?php if ( in_array($social['name'], $socials->options['subject']) && isset($social['copy']) ): ?>
              data-subject="<?php echo $social['copy'] ?: get_the_title(); ?>"
            <?php endif; ?>
            <?php if ( in_array($social['name'], $socials->options['via']) && isset($social['via']) ): ?>
              data-via="<?php echo $social['via']; ?>"
            <?php endif; ?>
            data-url="<?php echo get_permalink(); ?>"
          >
            <span class="social-share--icon" aria-label="Share on <?php echo $social['name']; ?>">
              <?php echo SwellSocialGetFAIcon($social['name']); ?>
            </span>
          </button>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>