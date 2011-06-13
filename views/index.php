<?php if ( count( $saved_newsbars ) ){?>

<h3>Avaliable News Bars</h3>

<?php echo form_open( $form_action ); ?>

<?php

$this->table->set_template($cp_table_template);
$this->table->set_heading('Name', 'Configure', 'Delete');

echo $this->table->generate($saved_newsbars);


?>

<?php echo form_close(); ?>

<?php }
else
echo 'No newsbar saved!'?>