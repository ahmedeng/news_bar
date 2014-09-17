<?php if ( count( $saved_newsbars ) ){?>

<h3>Avaliable News Bars</h3>


<?php

$this->table->set_template($cp_table_template);
$this->table->set_heading('Name', 'Configure','Listing', 'Delete');

echo $this->table->generate($saved_newsbars);


?>


<?php }
else
echo 'No newsbar saved!'?>