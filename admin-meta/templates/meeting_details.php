<?php
global $post;
$meetingType = get_post_meta($post->ID, 'meeting-type', TRUE);
?>
<div class="my_meta_control">

  <fieldset id="meeting-type">
    <legend>Category</legend>
    <label for="meeting-type">Meeting Type:</label>
    <p>
      <?php
      $args = array(
          'show_option_all' => '',
          'show_option_none' => '',
          'orderby' => 'ID',
          'order' => 'ASC',
          'show_count' => 0,
          'hide_empty' => 0,
          'child_of' => 0,
          'exclude' => '',
          'echo' => 1,
          'selected' => $meetingType,
          'hierarchical' => 0,
          'name' => 'meeting-type',
          'id' => 'meeting-type',
          'depth' => 0,
          'taxonomy' => 'meeting_types',
          'hide_if_empty' => false
      );
      wp_dropdown_categories($args);
      ?>
    </p>
  </fieldset>
  <fieldset id="event-date">
    <legend>Date / Time</legend>
      <p><span class="field-description">All entered times will be saved in timezone: "<?php echo get_option('timezone_string') ?>" <br />(This option can be changed on the <a href="<?php bloginfo('wpurl') ?>/wp-admin/options-general.php" target="_blank">General settings</a> page.)</span></p>

    <?php
      //we have to set timezone to WordPress' set option
      date_default_timezone_set(get_option('timezone_string'));
    $metabox->the_field('meetingDate');
    $meetingDate = ($metabox->get_the_value() ? date('l, F jS, Y', $metabox->get_the_value()) : '');
    ?>
    <label for="<?php $metabox->the_name(); ?>">Meeting Day:</label>
    <p>
      <input class="event-date" type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php echo $meetingDate; ?>"/>
    </p>

      <?php
      $metabox->the_field('meetingOpen');
      $meetingOpen = ($metabox->get_the_value() ? date('g:iA', $metabox->get_the_value()) : '');
      ?>
    <label for="<?php $metabox->the_name(); ?>">Doors Open:</label>
    <p>
      <input class="event-time" type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php echo $meetingOpen; ?>"/>
    </p>

      <?php
      $metabox->the_field('meetingStart');
      $meetingStart = ($metabox->get_the_value() ? date('g:iA', $metabox->get_the_value()) : '');
      ?>
    <label for="<?php $metabox->the_name(); ?>">Meeting Begins:</label>
    <p>
      <input class="event-time" type="text" name="<?php $metabox->the_name(); ?>" id="<?php $metabox->the_name(); ?>" value="<?php echo $meetingStart; ?>"/>
    </p>
  </fieldset>

  <fieldset id="event-description">
    <legend>Description</legend>
    <label>Brief Description</label>
    <p class="address">
      <input type="text" name="<?php $metabox->the_name('desc'); ?>" value="<?php $metabox->the_value('desc'); ?>" />
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

</div>