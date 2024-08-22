<?php if (isset($socials->platforms)) : ?>
  <x-subhead size="2" id="social-share" class="mt-narrow"><?php _e("Share on:", 'swellsocial'); ?></x-subhead>
  <ul aria-labelledby="social-share" class="list-none ml-0 flex gap-4">
    <?php foreach ($socials->platforms as $social) : ?>
      <li>
        <button
          class="text-3xl hover:text-primary"
          data-sharer="<?php echo $social['name'] == "x" ? "twitter" : $social['name']; ?>"
          <?php if ( in_array($social['name'], $socials->options['title']) ): ?>
            data-title="<?php echo $social['copy'] ?: get_the_title(); ?>" 
          <?php endif; ?>
          <?php if ( in_array($social['name'], $socials->options['hashtag']) && $socials->hashtags ): ?>
            data-hashtag="<?php echo $socials->hashtags[0]; ?>"
          <?php endif; ?>
          <?php if ( in_array($social['name'], $socials->options['hashtags']) ): ?>
            data-hashtags="<?php echo implode(",", $socials->hashtags); ?>"
          <?php endif; ?>
          <?php if ( in_array($social['name'], $socials->options['subject']) ): ?>
            data-subject="<?php echo $social['copy'] ?: get_the_title(); ?>"
          <?php endif; ?>
          <?php if ( in_array($social['name'], $socials->options['web']) ): ?>
            data-web="true"
          <?php endif; ?>
          <?php if ( in_array($social['name'], $socials->options['via']) ): ?>
            data-via="<?php echo $social['via']; ?>"
          <?php endif; ?>
          data-url="<?php echo get_permalink(); ?>"
        >
          <span class="<?php echo SwellSocialGetFAIcon($social['name']); ?>" class="text-white" aria-label="Share on <?php echo $social['name']; ?>"></span>
        </button>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>