<?php global $wpalchemy_media_access; ?>
<div class="my_meta_control">

  <fieldset id="event-files">
    <legend>File Uploads</legend>

    <?php $mb->the_field('flyer'); ?>
    <?php $wpalchemy_media_access->setGroupName('flyer-n')->setInsertButtonLabel('Insert Flyer')->setTab('type'); ?>

    <label for="<?php $mb->get_the_name(); ?>">Attach Flyer</label>
    <p>
      <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
      <?php echo $wpalchemy_media_access->getButton(array('label' => 'Upload PDF')); ?>
    </p>
  </fieldset>


</div>