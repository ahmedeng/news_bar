<?php

    if( ! defined('BASEPATH')) exit('No direct script access allowed');
    
    if( ! defined('News_bar_version') || ! defined('News_bar_description') || ! defined('News_bar_docs_url')) {
    
        define('News_bar_version', '2.0');
        define('News_bar_description', 'The News bar plugin adds news bar to your template.');
        define('News_bar_docs_url', '');
        
    }
        
    $plugin_info = Array(
        'pi_name' => 'News Bar',
        'pi_version' => News_bar_version,
        'pi_author' => 'Ahmed Hassan',
        'pi_author_url' => '',
        'pi_description' => News_bar_description,
        'pi_usage' => News_bar::usage()
    );
    
    /**
     * News Bar Class
     *
     * @package            ExpressionEngine
     * @category        Plugin
     * @author            Berry Timmermans
     * @copyright        Copyright (c) 2010, Berry Timmermans
     * @link            http://www.berrytimmermans.nl/
     */
    
    class News_bar {
        
        var $version = News_bar_version;
        
        var $description = News_bar_description;
        
        var $docs_url = News_bar_docs_url;
    
        var $return_data = '';

        var $table_name='ahmed_newsbar';
    
        // --------------------------------------------------------------------
    
        /**
         * HJ Social Bookmarks
         *
         * This function adds social bookmarking functionality to ExpressionEngine
         *
         * @access    public
         * @return    string
         */
    
          function News_bar() {
                  
            $this->EE =& get_instance();                            
                        
            // fetch params
            $name = $this->EE->TMPL->fetch_param('name');
            $main_class= $this->EE->TMPL->fetch_param('css_class');
            //$optional_class= $this->EE->TMPL->fetch_param('optional_class');
            $delay = ($this->EE->TMPL->fetch_param('delay')=='')? '3500':$this->EE->TMPL->fetch_param('delay');
            $fading = ($this->EE->TMPL->fetch_param('fading')=='')? 'fade' :$this->EE->TMPL->fetch_param('fading');
            
            $bar=$this->get_saved_newsbars($name);
            if($bar==FALSE)
                return '';
            
            $filename=md5($bar['bar_name']);
            $html=" 
            <script src=\"http://$_SERVER[SERVER_NAME]/news_bar/ajaxticker.js\" type=\"text/javascript\"></script>
            <script type=\"text/javascript\">

var xmlfile=\"http://\"+window.location.hostname+\"/news_bar/$filename.txt\";
new ajax_ticker(xmlfile, \"$main_class\", \"\", [$delay], \"$fading\");
</script>";       

            $this->return_data=$html;

        }

function get_saved_newsbars($bar_name)
{
    $this->EE->db->select('*');
    $this->EE->db->where('site_id', $this->EE->config->item('site_id') );
    $this->EE->db->where('bar_name', $bar_name);
    $query = $this->EE->db->get('exp_'.$this->table_name);
    if($query->num_rows())
    {
        $rows=$query->result_array();
        return $rows[0];
    }
    else
        return FALSE;
}
    
        // --------------------------------------------------------------------
    
        /**
         * Usage
         *
         * This function describes how the plugin is used.
         *
         * @access    public
         * @return    string
         */
        
          //  Make sure and use output buffering
    
      function usage() {
      
          ob_start(); 
      
          ?>
          
        =============================
        The Tag
        =============================

        Add the following code wherever you want the news bar to be displayed.
        
        {exp:news_bar name="news bar1" css_class="class1" delay="1000" fading="fade"}

        =============================
        Tag Parameters
        =============================

        name=
            
            [REQUIRED]
            The name of saved news bar.    
        
        css_class
            
            [REQUIRED]
            
            The css class used to display news bar contents.
            
        delay=
            
            [OPTIONAL]
            
            The delay between message changes in milliseconds. default 3.5 seconds.
            
        fading=
            
            [OPTIONAL]
            
             Enter "fade" to enable the effect, or "remove" it to disable it. default fade.
    
        <?php
      
              $buffer = ob_get_contents();
        
              ob_end_clean(); 
    
              return $buffer;
          
          }
      
         // END
    
    }
    
    /* End of file pi.News_bar.php */ 
    /* Location: ./system/expressionengine/third_party/memberlist/pi.News_bar.php */ 