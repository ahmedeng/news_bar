<?php if ( count( $saved_newsbars ) ){?>

<h3><?=$newsbar_name?> entries</h3>


<?php

$this->table->set_template($cp_table_template);
$this->table->set_heading('Title', 'Editing', 'Delete');

echo $this->table->generate($saved_newsbars);


?>


<?php }
else
echo 'No entites'?>