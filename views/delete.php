<?php echo form_open( 'C=addons_extensions'.AMP.'M=extension_settings'.AMP.'file=news_bar'.AMP.'action=delete' ); ?>

<p class="shun">Are you sure you want to delete this news bar?</p>

<p class="notice">This action cannot be undone</p>

<p><input type="submit" name ="submit" value="Delete" class="submit" /></p>

<?php echo $settings['id'];?>

<?php echo form_close(); ?>