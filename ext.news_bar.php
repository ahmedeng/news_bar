<?php

    if( ! defined('BASEPATH')) exit('No direct script access allowed');

  class News_bar_ext {

    var $name        = 'News Bar';
    var $version         = '2.0';
    var $description    = 'News Bar';
    var $settings_exist    = 'y';
    var $docs_url        = ''; // 'http://expressionengine.com/user_guide/';

    var $settings        = array();

    var $table_name='ahmed_newsbar';
    
    function __construct($settings = '')
    {
        $this->EE =& get_instance();     
        $this->settings = $settings;   
    }
    
    
    /**
 * Activate Extension
 *
 * This function enters the extension into the exp_extensions table
 *
 * @see http://codeigniter.com/user_guide/database/index.html for
 * more information on the db class.
 *
 * @return void
 */
function activate_extension()
{
    
    
    $data = array(
        'class'        => __CLASS__,
        'method'    => 'add_to_news_bar',
        'hook'        => 'entry_submission_end',
        'settings'    => serialize($this->settings),
        'priority'    => 10,
        'version'    => $this->version,
        'enabled'    => 'y'
    );
    
    $this->EE->db->insert('extensions', $data);
    
    $data = array(
        'class'        => __CLASS__,
        'method'    => 'delete_from_news_bar',
        'hook'        => 'delete_entries_loop',
        'settings'    => '',
        'priority'    => 10,
        'version'    => $this->version,
        'enabled'    => 'y'
    );
    
    $this->EE->db->insert('extensions', $data);
    
        $fields = array(
        'bar_id'    => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
        'site_id'    => array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE),
        'bar_name'    => array('type' => 'varchar', 'constraint' => '250'),
        'channels'    => array('type' => 'varchar', 'constraint' => '250'),
        'fields'    => array('type' => 'varchar', 'constraint' => '250'),
        'values'    => array('type' => 'varchar', 'constraint' => '250')
        );

    $this->EE->load->dbforge();
    $this->EE->dbforge->add_field($fields);
    $this->EE->dbforge->add_key('bar_id', TRUE);    
    $this->EE->dbforge->create_table($this->table_name,TRUE);
    
    mkdir(getcwd()."/news_bar",0755);
    copy(PATH_THIRD."news_bar/js/ajaxticker.js",getcwd()."/news_bar/ajaxticker.js");
}


/**
 * Update Extension
 *
 * This function performs any necessary db updates when the extension
 * page is visited
 *
 * @return     mixed    void on update / false if none
 */
function update_extension($current = '')
{
    if ($current == '' OR $current == $this->version)
    {
        return FALSE;
    }
    
    if ($current < '2.0')
    {
        // Update to version 1.0
    }
    
    $this->EE->db->where('class', __CLASS__);
    $this->EE->db->update(
                'extensions', 
                array('version' => $this->version)
    );
}

/**
 * Disable Extension
 *
 * This method removes information from the exp_extensions table
 *
 * @return void
 */
function disable_extension()
{
    $this->EE->db->where('class', __CLASS__);
    $this->EE->db->delete('extensions');
}


function get_div_entry_id($div_string)
{
    $pos1=strpos($div_string,"/#");
    $pos2=strpos($div_string,"\"",$pos1);
    return (int)$entry_id=substr($div_string,$pos1+2,$pos2-$pos1-2);    
}


function remove($div_string)
{   
    $entry_id=$this->get_div_entry_id($div_string);
    $data = array('field_id_211' => 'لا');

    $sql = $this->EE->db->update_string('exp_channel_data', $data, "entry_id = '$entry_id'");

    $this->EE->db->query($sql);
    
}


function get_saved_newsbars($channel_id)
{
    $this->EE->db->select('*');
    $this->EE->db->where('site_id', $this->EE->config->item('site_id') );
    $query = $this->EE->db->get('exp_'.$this->table_name);
    $saved_newsbars = array();
    foreach($query->result_array() as $row) 
    {                                    
        $channels=unserialize($row['channels']);
        if(in_array($channel_id,$channels))
        {
            $saved_newsbars[]=$row;
        }
    }   
    return $saved_newsbars;     
}

function get_field_db_name($field,$channel_id)
{    
    $this->EE->db->select("field_group");
    $this->EE->db->where( 'channel_id', $channel_id );
    $query = $this->EE->db->get( 'exp_channels' );
    $row = $query->row_array();
    $field_group = $row["field_group"];

    $this->EE->db->select( 'field_id' );
    $this->EE->db->where( 'group_id', $field_group );
    $this->EE->db->where( 'field_name', $field );
    $this->EE->db->order_by( 'field_order' );
    $query = $this->EE->db->get( 'exp_channel_fields' );
    if($query->num_rows())
        return 'field_id_'.$query->row(field_id);
    else
        return $field;
}


function check_rules($obj,$prefs,$fields,$values)
{
    if($obj['status']=='closed')    
        return FALSE;
        
    $fields=unserialize($fields);
    $values=unserialize($values);
    if($fields["$obj[channel_id]"]=='Choose Field')
        return TRUE;
    $key_field=$this->get_field_db_name($fields["$obj[channel_id]"],$obj['channel_id']);
    $key_value=$values["$obj[channel_id]"];
    
    if(array_key_exists($key_field,$obj))
    {            
        $words=explode(' +',$obj["$key_field"]);
        if(in_array($key_value,$words))
            return TRUE;
        else
            return FALSE;
    }
    else
    {
        if(array_key_exists($key_field,$prefs))
        {                             
            $words=explode(' +',$prefs["$key_field"]);
            if(in_array($key_value,$words))
                return TRUE;
            else
                return FALSE;
        }                
    }
    return FALSE;    
}
                    

function add_to_news_bar($id, $obj, $prefs)
{                                        
    ////////////////////
    $saved_newsbars=$this->get_saved_newsbars($obj['channel_id']);
    
    foreach($saved_newsbars as $row) 
    {
            
        
    ////////////////////
//        if($obj['channel_id']==1)    
//        {
           $site_url = $this->EE->functions->fetch_site_index(1,0);
           
           $news_bar_entry="<div class=\"message\"><a href=\"".$site_url."news_articles/hot_news/#$id\">$obj[title].</div>";
           
           $filename=md5($row['bar_name']);
           $filename=getcwd()."/news_bar/$filename.txt";
               
           $news_bar_array=file($filename,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
           if($news_bar_array==FALSE)
           {
                $news_bar_array=array();
                $news_bar_array[]="<div>";
                $news_bar_array[]="</div>";
           }
           
           
           
           if(!$this->check_rules($obj,$prefs,$row['fields'],$row['values']))
           {
               for($i=0;$i<count($news_bar_array);$i++)
               {
                   if($this->get_div_entry_id($news_bar_array[$i])==$id)
                   {
                       unset($news_bar_array[$i]);
                       if($obj['status']=='closed')
                            $this->remove($news_bar_array[$i]);
                       break;
                   }
               }        
           }                             
           else        
           {
               unset($news_bar_array[0]);
               unset($news_bar_array[count($news_bar_array)]);       
               array_unshift($news_bar_array,$news_bar_entry);
               
               if(count($news_bar_array)>10)
               {
                   $this->remove($news_bar_array[count($news_bar_array)-1]);
                   array_pop($news_bar_array);
               }
               
               array_unshift($news_bar_array,"<div>");
               $news_bar_array[count($news_bar_array)+1]="</div>";
           }
           $news_bar_content=implode("\r\n",$news_bar_array);
           $f=fopen($filename,"w");
           fwrite($f,$news_bar_content);
           fclose($f); 
//        }
    }
}


function delete_from_news_bar($entry_id,$channel_id)
{
    if($channel_id==1)    
    {
       $filename=getcwd()."/news_bar/tickercontent.txt";
       $news_bar_array=file($filename,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      
       for($i=0;$i<count($news_bar_array);$i++)
       {    
           if($this->get_div_entry_id($news_bar_array[$i])==$entry_id)
           {
               unset($news_bar_array[$i]);
               break;
           }
       }
      
       $news_bar_content=implode("\r\n",$news_bar_array);
       $f=fopen($filename,"w");
       fwrite($f,$news_bar_content);
       fclose($f); 
    }
}

// --------------------------------
//  Settings
// --------------------------------  
/*
function settings()
{
    $settings = array();
    
    $sql ="SELECT settings FROM exp_extensions where class='".__CLASS__."' AND method='add_to_news_bar'";
    $query = $this->EE->db->query($sql);
    $old_settings=unserialize($query->row('settings'));
    
    $settings['NewsBar']    = "";
    
    $query = $this->EE->db->query("SELECT channel_id,channel_title FROM exp_channels");

    $channels=array();
    if ($query->num_rows() > 0)
    {               
        foreach($query->result_array() as $row)
        {              
              $channels[$row['channel_id']]=$row['channel_title'];
        }
    }
    $settings['Channels']   = array('ms', $channels);
    
    // Complex:
    // [variable_name] => array(type, values, default value)
    // variable_name => short name for setting and used as the key for language file variable
    // type:  t - textarea, r - radio buttons, s - select, ms - multiselect, f - function calls
    // values:  can be array (r, s, ms), string (t), function name (f)
    // default:  name of array member, string, nothing
    //
    // Simple:
    // [variable_name] => 'Butter'
    // Text input, with 'Butter' as the default.
    
    return $settings;
}
// END
  */

/**
 * Settings Form
 *
 * @param    Array    Settings
 * @return     void
 */
function create($a='')
{
    
    if($a=='edit')
    {
        $bar_id= $this->EE->input->get('id',TRUE);
        $this->EE->db->select('bar_id,bar_name,channels');
        $this->EE->db->where('bar_id', $bar_id );
        $query = $this->EE->db->get('exp_'.$this->table_name);            
        $edit_name=$query->row('bar_name');
        $edit_channels=unserialize($query->row('channels'));
    }
    $this->EE->load->helper('form');
    $this->EE->load->library('table');

    $vars = array();
    
    $settings = array();
                                         
    $settings['NewsBar']    = form_input('NewsBar', $edit_name);
        
    
    $query = $this->EE->db->query("SELECT channel_id,channel_title FROM exp_channels");

    $channels=array();
    if ($query->num_rows() > 0)
    {               
        foreach($query->result_array() as $row)
        {              
              $channels[$row['channel_id']]=$row['channel_title'];
        }
    }
    $settings['Channels']   = form_multiselect('Channels[]',$channels,$edit_channels);
     
    $vars['settings'] = $settings;
    
    
    return $this->EE->load->view('add', $vars, TRUE);            
    
} 

function get_fields($channel_id)
{
    // Get custom fields for the selected channel
        
        $this->EE->db->select("field_group, cat_group,channel_title");
        $this->EE->db->where( 'channel_id', $channel_id );
        $query = $this->EE->db->get( 'exp_channels' );
        $row = $query->row_array();
        $field_group = $row["field_group"];
        $cat_group = $row["cat_group"];
        $channel_title = $row["channel_title"];
    
        $this->EE->db->select( 'field_name, field_label' );
        $this->EE->db->where( 'group_id', $field_group );
        $this->EE->db->order_by( 'field_order' );
        $query = $this->EE->db->get( 'exp_channel_fields' );
        
        $data["custom_fields"] = array();
        $data["unique_fields"] = array();
        $data["unique_fields"][ "channel_title" ] = $channel_title;
        $data["unique_fields"][ "Choose Field" ] = "Choose Field";
        $data["unique_fields"][ "title" ] = "Title";
        $data["unique_fields"][ "status" ] = "Status";

        if( $query->num_rows() > 0 ) {
            foreach( $query->result_array() as $row ) {
                $data["custom_fields"][ $row["field_name"] ] = $row["field_label"];
                $data["unique_fields"][ $row["field_name"] ] = $row["field_label"];
            }
        }

        return $data["unique_fields"];
}


function choose_fields()
{
    $name= $this->EE->input->post('NewsBar',TRUE);
    $Channels= $this->EE->input->post('Channels',TRUE);

    $bar_id= $this->EE->input->get('id',TRUE);
    if(is_numeric($bar_id))
    {
        $this->EE->db->select('bar_id,fields,values');
        $this->EE->db->where('bar_id', $bar_id );
        $query = $this->EE->db->get('exp_'.$this->table_name);            
        $edit_fields=unserialize($query->row('fields'));
        $edit_values=unserialize($query->row('values'));
        
        $this->EE->db->select('*');
        $this->EE->db->where('bar_id', $bar_id);
        $query = $this->EE->db->get('exp_'.$this->table_name);
        $old_name=md5($query->row('bar_name')).'.txt';
        
        
        $data = array('bar_name' => $name, 'channels' => serialize($Channels),'site_id' => $this->EE->config->item('site_id'));                
        $sql = $this->EE->db->update_string($this->table_name, $data,"bar_id = '".$bar_id."' and site_id = '".$this->EE->config->item('site_id')."'");
        $this->EE->db->query($sql);                
        
        $new_name=md5($name).'.txt';
        rename(getcwd()."/news_bar/".$old_name,getcwd()."/news_bar/".$new_name);
    }
    else    
    {
        $data = array('bar_name' => $name, 'channels' => serialize($Channels),'site_id' => $this->EE->config->item('site_id'));                
        $sql = $this->EE->db->insert_string($this->table_name, $data);        
        $this->EE->db->query($sql);        
        $bar_id = $this->EE->db->insert_id();                    
    }

    $this->EE->db->select('*');
    $this->EE->db->where('site_id', $this->EE->config->item('site_id') );
    $this->EE->db->where('bar_name', $name);
    $query = $this->EE->db->get('exp_'.$this->table_name);
    if($query->num_rows()>1)
    {
        $data = array('bar_name' => $name.'_'.$bar_id,'site_id' => $this->EE->config->item('site_id'));                
        $sql = $this->EE->db->update_string($this->table_name, $data,"bar_id = '".$bar_id."' and site_id = '".$this->EE->config->item('site_id')."'");
        $this->EE->db->query($sql);                            
    }
        
    
    $this->EE->load->helper('form');
    $this->EE->load->library('table');

    $view=form_open('C=addons_extensions'.AMP.'M=extension_settings'.AMP.'file=news_bar'.AMP.'action=save'.AMP.'id='.$bar_id);

    $h=0;  
    foreach($Channels as $channel)    
    {   
        $vars = array();
        $settings = array();
                
        $i=$channel;
        $fields=$this->get_fields($channel);
        $head=$fields['channel_title'];
        unset($fields['channel_title']);
        $settings['Key Field']    = form_dropdown('Key_Field_'.$i, $fields,$edit_fields["$channel"]);
        $settings['Key Value']    = form_input('Key_Value_'.$i,$edit_values["$channel"]);
        $h++;
        
        $vars['settings'] = $settings;
        $vars['head'] = $head;
        $view=$view.$this->EE->load->view('fields', $vars, TRUE);                 
    }
    
    $view=$view."<p>".form_submit('submit', lang('submit'), 'class="submit"')."</p>".form_close();
    return $view; 
    
} 


function save()     
{
    $bar_id= $this->EE->input->get('id',TRUE);
    
    $this->EE->db->select("channels");
    $this->EE->db->where( 'bar_id', $bar_id );
    $query = $this->EE->db->get( "exp_$this->table_name");
    $row = $query->row_array();
    $channels=unserialize($row['channels']);
         
    $fields=array();
    $values=array();
    $i=0;
    foreach($channels as $channel)    
    {
        $i=$channel;
        $key_field= $this->EE->input->post('Key_Field_'.$channel,TRUE);
        $fields["$i"]=$key_field;
        
        $key_value= $this->EE->input->post('Key_Value_'.$channel,TRUE);
        $values["$i"]=$key_value;                
        
    }
    
    $data = array('fields' => serialize($fields), 'values' => serialize($values));

    $sql = $this->EE->db->update_string("exp_$this->table_name", $data, "bar_id = '".$bar_id."' and site_id = '".$this->EE->config->item('site_id')."'");
                                     
    $this->EE->db->query($sql);    
    
    $this->EE->functions->redirect(BASE.AMP.'C=addons_extensions'.AMP.'M=extension_settings'.AMP.'file=news_bar');
}


function delete()
{
  $submit= $this->EE->input->post('submit',TRUE);
  
  if($submit=='Delete')
  {      
    $bar_id= $this->EE->input->post('id',TRUE);
  
    $this->EE->db->query("DELETE FROM exp_".$this->table_name." WHERE bar_id= $bar_id ");
    $this->EE->functions->redirect(BASE.AMP.'C=addons_extensions'.AMP.'M=extension_settings'.AMP.'file=news_bar');
  }
  else
  {
    $vars = array();
    $bar_id= $this->EE->input->get('id',TRUE);
    
    $settings = array();
                                         
    $settings['id']    = form_hidden('id', $bar_id);     
    $vars['settings'] = $settings;    
    
    return $this->EE->load->view('delete', $vars, TRUE);         
  } 
}

function show_bars()
{
    $this->EE->load->helper('form');
    $this->EE->load->library('table');


    $this->EE->cp->set_right_nav(array(
    'Create News Bar' => 
        BASE.AMP.'C=addons_extensions'.AMP.'M=extension_settings'.AMP.'file=news_bar'.AMP.'action=create')
        );

    $vars = array();
    
    $settings = array();
    
    $this->EE->db->select('bar_id,bar_name');
    $this->EE->db->where('site_id', $this->EE->config->item('site_id') );
    $query = $this->EE->db->get('exp_'.$this->table_name);
    $vars["saved_newsbars"] = array();
    foreach($query->result_array() as $row) {
        $id = $row["bar_id"];
        $row1["name"] = $row["bar_name"];
        $row1[] = '<a href="'.BASE.AMP.'C=addons_extensions'.AMP.'M=extension_settings'.AMP.'file=news_bar'.AMP.'action=edit'.AMP.'id='.$id.'">Configure</a>';
        $row1[] = '<a href="'.BASE.AMP.'C=addons_extensions'.AMP.'M=extension_settings'.AMP.'file=news_bar'.AMP.'action=delete'.AMP.'id='.$id.'">Delete</a>';
        $vars["saved_newsbars"][ $id ] = $row1;
        unset($row1);
    }

    
    
    return $this->EE->load->view('index', $vars, TRUE);            
    
} 


function settings_form($current)
{
    
    $action= $this->EE->input->get('action',TRUE);
    if($action=='create')
        return $this->create();
    else if($action=='fields')
        return $this->choose_fields();
    else if($action=='save')
        return $this->save();
    else if($action=='edit')
        return $this->create('edit');
    else if($action=='delete')
        return $this->delete();
    else
        return $this->show_bars();
    
}
}
// END CLASS
?>
