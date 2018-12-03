<?php
global $post;
?>
<div class="my_meta_control">

  <fieldset id="event-date">
    <legend>Date / Time</legend>
      <p><span class="field-description">All entered times will be saved in timezone: "<?php echo get_option('timezone_string') ?>" <br />(This option can be changed on the <a href="<?php bloginfo('wpurl') ?>/wp-admin/options-general.php" target="_blank">General settings</a> page.)</span></p>

    <?php
      //we have to set timezone to WordPress' set option
      date_default_timezone_set(get_option('timezone_string'));
    $metabox->the_field('eventDate');
    $eventDate = ($metabox->get_the_value() ? date('l, F jS, Y', $metabox->get_the_value()) : '');
    ?>
    <label for="<?php $metabox->the_name(); ?>">Event Day:</label>
    <p>
      <input class="event-date" type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php echo $eventDate; ?>"/>
    </p>

      <?php
      $metabox->the_field('eventOpen');
      $eventOpen = ($metabox->get_the_value() ? date('g:iA', $metabox->get_the_value()) : '');
      ?>
    <label for="<?php $metabox->the_name(); ?>">Doors Open:</label>
    <p>
      <input class="event-time" type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php echo $eventOpen; ?>"/>
    </p>

      <?php
      $metabox->the_field('eventStart');
      $eventStart = ($metabox->get_the_value() ? date('g:iA', $metabox->get_the_value()) : '');
      ?>
    <label for="<?php $metabox->the_name(); ?>">Event Begins:</label>
    <p>
      <input class="event-time" type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php echo $eventStart; ?>"/>
    </p>
  </fieldset>

  <fieldset id="event-location">
    <legend>Location</legend>
    <label>Venue Name</label>
    <p class="address">
      <input type="text" name="<?php $metabox->the_name('venue'); ?>" value="<?php $metabox->the_value('venue'); ?>" />
    </p>
    <label>Address</label>
    <p class="address">
      <input type="text" name="<?php $metabox->the_name('address1'); ?>" value="<?php $metabox->the_value('address1'); ?>" class="address1" /><br />
      <input type="text" name="<?php $metabox->the_name('address2'); ?>" value="<?php $metabox->the_value('address2'); ?>" class="address2" />
    </p>
    <div class="city">
      <label>City</label>
      <input type="text" name="<?php $metabox->the_name('city'); ?>" value="<?php $metabox->the_value('city'); ?>"/>
    </div>
    <div class="state">
      <label>State</label>
      <input type="text" name="<?php $metabox->the_name('state'); ?>" value="<?php $metabox->the_value('state'); ?>"/>
    </div>
    <div class="zip">
      <label>Zip</label>
      <input type="text" name="<?php $metabox->the_name('zip'); ?>" value="<?php $metabox->the_value('zip'); ?>"/>
    </div>
    <p style="clear: both; height: 1px;"></p>
  </fieldset>

  <fieldset id="event-description">
    <legend>Event Description</legend>

    <label>Brief Summary</label>
    <p>
      <input type="text" name="<?php $metabox->the_name('desc'); ?>" value="<?php $metabox->the_value('desc'); ?>" />
      <span class="field-description">A small one line description of the event.</span>
    </p>

<!--     <p>
      <label>Full Description</label>
      <span class="field-description" style="clear:left">Enter the full details of this event in the editor below.</span>
    </p>
 -->
    <?php
    // $content = html_entity_decode($metabox->get_the_value('description'));
    // $id = $metabox->get_the_name('description');
    // $settings = array(
    //     'media_buttons' => true,
    //     'teeny' => true,
    //     'tinymce' => array(
    //         'theme_advanced_buttons1' => 'formatselect, bold, italic, underline, blockquote, separator, bullist, numlist, separator, justifyleft, justifycenter, justifyright, justifyfull, wp_adv',
    //         'theme_advanced_buttons2' => 'undo, redo, pastetext, pasteword, separator, link, unlink, separator, charmap, removeformat, wp_help',
    //         'theme_advanced_buttons3' => '',
    //         'theme_advanced_buttons4' => '',
    //         'theme_advanced_blockformats' => 'p,h2,h3,h4'
    //     )
    // );
    // wp_editor($content, $id, $settings);
    ?>   
  </fieldset>

</div>