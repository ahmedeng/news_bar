<?php 
$this->table->set_template($cp_pad_table_template);
$this->table->set_heading(
    array('data' => $head, 'style' => 'width:50%;'),'');
     
foreach ($settings as $key => $val)
{
    $this->table->add_row(lang($key, $key), $val);
}

echo $this->table->generate();
$this->table->clear();
    
?>

<?php
/* End of file index.php */
/* Location: ./system/expressionengine/third_party/link_truncator/views/index.php */
?>