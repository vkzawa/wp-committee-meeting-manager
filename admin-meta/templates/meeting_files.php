<?php global $wpalchemy_media_access; ?>
<div class="my_meta_control">

  <fieldset id="meeting-files">
    <legend>File Uploads</legend>

    <?php $mb->the_field('agenda'); ?>
    <?php $wpalchemy_media_access->setGroupName('agenda-n')->setInsertButtonLabel('Insert Agenda')->setTab('type'); ?>

    <label for="<?php $mb->get_the_name(); ?>">Attach Agenda</label>
    <p>
      <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
      <?php echo $wpalchemy_media_access->getButton(array('label' => 'Upload PDF')); ?>
    </p>

    <?php $mb->the_field('minutes'); ?>
    <?php $wpalchemy_media_access->setGroupName('minutes-n')->setInsertButtonLabel('Insert Minutes')->setTab('type'); ?>

    <label for="<?php $mb->get_the_name(); ?>">Attach Minutes</label>
    <p>
      <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
      <?php echo $wpalchemy_media_access->getButton(array('label' => 'Upload PDF')); ?>
    </p>
    
  </fieldset>


</div>