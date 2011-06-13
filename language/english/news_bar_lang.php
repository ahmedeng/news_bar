<?php
$lang = array(
            'btn-add'               =>      'Add Language',
            'btn-add-row'           =>      'Add New Text',
            'btn-delete'            =>      'Remove Language',
            'btn-save'              =>      'Save',
            'error-fill-all'        =>      'You need to fill all the visible fields.',
            'lbl-def-lang'          =>      'Default Language',
            'lbl-def-lang-mode'     =>      'Default Language Mode',
            'lbl-r-append'          =>      'Append Language Code',
            'lbl-r-browser'         =>      'From Browser',
            'lbl-r-cookie'          =>      'From Cookies',
            'lbl-r-engine-db'       =>      'Database',
            'lbl-r-engine-file'     =>      'File',
            'lbl-r-keep'            =>      'Keep Untouched',
            'lbl-r-none'            =>      'None',
            'lbl-r-system'          =>      'From System',
            'lbl-t-definelanguages' =>      'Define Static Text and Translations',
            'lbl-t-engines-available'=>     'Choose Translation Storage Engine',
            'lbl-t-language'        =>      'Language',
            'lbl-t-language_code'   =>      'Language Code',
            'lbl-t-languages-a'     =>      'Available Languages',
            'lbl-t-key'             =>      'Key',
            'lbl-t-siteurlmode'     =>      '{site_url} Mode',
            'hdn-options'           =>      'Options',
            'hdn-settings'          =>      'Settings',
            'inf-def-lang'          =>      'This defines your site\'s default language. If your visitors\'s language cannot be <br /> 
                                            identified by any of the default language modes your site will be shown with this language.',
            'inf-def-lang-mode'     =>      'Defines the auto language selection mode. <br />
                                            For not logged-in users; browser option checks the browser language of the visitor; 
                                            system option sets the language to default language selected; cookie checks if there is a 
                                            cookie already set defining the language of the visitor.',
            'inf-t-engines-available'=>     'There are two storage engines available for you to save your translations.
                                            <br /><br />
                                            <strong>Database</strong><br />
                                            This engine stores your translations into extension settings space within your database. This approach requires more database storage space; however if you have file permission problem you need to select this option. 
                                            <br /><br /><strong>File</strong><br />
                                            This engine stores your translations into a file in your server. This is a more convenient and standard method; however if you get file permission errors, we suggest you NOT to chose this method.',                                           
            'inf-t-language_code'   =>      'Language code can have a maximum of three letters. It can accept only the characters from 
                                            English alphabet, and it cannot contain any special characters or punctuations. It must be 
                                            a single word. Underscore and dash characters can be used.',
            'inf-t-keys'            =>      'In the first column, you must enter an alpha-numeric only key. The key can contain dash and underscore characters but spaces and other special characters are not allowed. In the remaining columns you must enter the text and its corresponding translations.',
            'inf-t-siteurl'         =>      'Append Language Code option appends current selected language code to {site_url} and creates a new variable called {site_url_pure} to output original site url
                                            <br /><br />
                                            Keep Untouched option keeps {site_url} untouched and creates a new variable called {site_url_wlc} that outputs the site url with the language code appended to it
                                            <br /><br />
                                            None option does not touch to {site_url} and it does not create a new variable. So you need to use {site_url}{language_code} if you need to append language code to {site_url}',
            'msg-fail-save'         =>      'Unable to save your settings.',
            'msg-success-save'      =>      'Your settings have been saved successfully.'
        );
/* End of file lang.bbr_multilanguagesupport.php */
/* Location: ./system/language/english/lang.bbr_multilanguagesupport.php */