<?php 

require_once("Home.php"); // loading home controller

class Multi_language extends Home
{

    public function __construct()
    {

        parent::__construct();
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login_page', 'location');    

        if($this->session->userdata('user_type') != 'Admin')
        redirect('home/login_page', 'location');

        if($this->is_demo == '1')
        {
            if($this->uri->segment(2)!="index")
             if($this->is_demo == '1')
              {
                 $response['status'] = 0;
                 $response['message'] = "This feature has been disbaled in this demo.";
                 echo json_encode($response);
                 exit();
              }
      
        }

        set_time_limit(0);
        $this->important_feature();
        $this->member_validity();
    }


    public function index()
    {
        // Root application language
        $language_list = array();
        $dir           = FCPATH.'application/language/';
        $fileList      = scandir($dir, 0);
        $fileCount     = count($fileList);
        $single_file   = array();

        for($i = 2; $i < $fileCount; $i++) 
        { 
            array_push( $single_file, $fileList[$i] );
        }
        $data['lang']  = $single_file;

        // Plugin languages
        $plugin_dir       = FCPATH."assets/modules/datatables/language/";
        $plugins_dir_scan = scandir($plugin_dir,0);
        $plugin_files     = array();

        for ($i = 2; $i < count($plugins_dir_scan); $i++) 
        {
            array_push($plugin_files, $plugins_dir_scan[$i]);
        }

        $data['plugins_files'] = $plugin_files;

        // Addon Language
        $addon_directory = FCPATH."application/modules/";
        $scan_directory  = array_diff(scandir($addon_directory),array('.','..'));

        $addon_folder_scanner = array();
        for ($i = 2; $i < count($scan_directory)+2; $i++) 
        {
            $addon_folders = $addon_directory.$scan_directory[$i]."/language/";
            if(file_exists($addon_folders))
            {    
                $scan_addon_folders = array_diff(scandir($addon_folders),array('.','..'));
                if(!empty($scan_addon_folders))
                {
                    $addOns             = $scan_directory[$i];
                    array_push($addon_folder_scanner,$addOns);
                }
            }
        }

        $data['addons']     = $addon_folder_scanner;
        $data['body']       = 'admin/multi_language/language_list';
        $data['page_title'] =  $this->lang->line("Language Editor");
        $this->_viewcontroller($data);

    }


    public function create_new_lang()
    {  
        // sending language directory name to view
        $root_directory      = FCPATH."application/language/";
        $scan_root_directory = array_diff(scandir($root_directory),array('.','..'));
        $data['root_dir']    = $scan_root_directory;

        // Application Languages
        $directory        = FCPATH."application/language/english";
        $file_lists       = scandir($directory,0);
        $total_file       = count($file_lists);
        $language_Files   = array();

        for($i = 2; $i< $total_file; $i++) 
        {
            array_push($language_Files, $file_lists[$i]);
        }

        for ($i = 0; $i < count($language_Files); $i++) 
        {
            $file_name = $language_Files[$i];
            include FCPATH."application/language/english/".$file_name;
            $data['file_name'][$i] = $file_name;
        }

        // datatables plugins language
        $directory2           = FCPATH."assets/modules/datatables/language/english.json";
        $plugin_file          = file_get_contents($directory2);
        $plugin_file_contents = json_decode($plugin_file,TRUE);

        $lang = array();

        foreach ($plugin_file_contents as $key => $value) 
        {
            if($key == "oPaginate")
            {
                foreach ($value as $key1 => $value1) 
                {
                    $lang[$key1] = $value1;
                }

            } else if ($key =='oAria') {

                foreach ($value as $key1 => $value1) 
                {
                    $lang[$key1] = $value1;
                }
            } else {
                $lang[$key] = $value;

            }   
        }


        // Addon Language
        $addon_directory = FCPATH."application/modules/";
        $scan_directory  = scandir($addon_directory,0);
        $addon_files     = array();

        for ($i = 2; $i < count($scan_directory) ; $i++) 
        {
            array_push($addon_files, $scan_directory[$i]);
        }

        $addon_lang_folder = array();
        for ($i = 0; $i < count($addon_files); $i++) 
        {
            $module_directory = FCPATH."application/modules/".$addon_files[$i]."/language";
            if(file_exists($module_directory))
            {
                $scan_module_directories = array_diff(scandir($module_directory),array('.','..'));
                if(!empty($scan_module_directories))
                {
                    $addon_name = $addon_directory.$addon_files[$i];
                    array_push($addon_lang_folder,$addon_name);
                }
            }
        }


        $addon_dir_arr = array();
        for ($i = 0; $i < count($addon_lang_folder); $i++) 
        {
            $addon_lang_file            = $addon_lang_folder[$i];
            $addon_lang_file_dir        = $addon_lang_file."/language/english/";
            $addon_lang_file_dir_scan[] = scandir($addon_lang_file_dir,1);
            array_push($addon_dir_arr,$addon_lang_file_dir_scan[$i][0]);
        }
        $data['addons'] = $addon_dir_arr;

        $data['body']      = 'admin/multi_language/add_language';
        $data['page_title']     = $this->lang->line("New Language");
        $this->_viewcontroller($data);
    }


    public function save_language_name()
    {
        $name = strip_tags(trim($this->input->post("languageName",true)));

        if (!preg_match("/^([a-zA-Z_])+$/i", $name)) 
        {
            echo "3";

        } else 
        {
            // getting plugin directory
            $plugin_dir = FCPATH."assets/modules/datatables/language/".$name.'.json';

            //creating plugin language file with english.php files contents
            if(!file_exists($plugin_dir)) 
            {
                fopen($plugin_dir,'w');
                $pluginFile = FCPATH."assets/modules/datatables/language/english.json";
                $plugin_contents = file_get_contents($pluginFile);
                $plugin_contents_ele = json_decode($plugin_contents,true);

                // creating array of language file
                $plugin_languages ='{'."\n";
                $j = count($plugin_contents_ele);
                foreach ($plugin_contents_ele as $key => $value) 
                {
                    if($key == 'oPaginate')
                    {
                        $plugin_languages .='    '.'"' . $key. '"' . ' ' . ':' . ' ' . '{' ."\n";
                        $i = count($value);
                        foreach($value as $key1 => $value1)
                        {
                            if($i != 1)
                                $plugin_languages .='    '."\t".'"' . $key1. '"' . ' ' . ':' . ' ' . '"' . '",' . "\n";
                            else
                                $plugin_languages .='    '."\t".'"' . $key1. '"' . ' ' . ':' . ' ' . '"' . '"' . "\n";

                            $i--;
                        }

                        $plugin_languages .= '    '.'},'."\n";

                    } else if ($key=='oAria')
                    {
                        $plugin_languages .='    '.'"' . $key. '"' . ' ' . ':' . ' ' . '{' ."\n";
                        $i = count($value);
                        foreach($value as $key2 => $value2)
                        {
                            if($i != 1)
                                $plugin_languages .='    '."\t".'"' . $key2. '"' . ' ' . ':' . ' ' . '"' . '",' ."\n";
                            else
                                $plugin_languages .='    '."\t".'"' . $key2. '"' . ' ' . ':' . ' ' . '"' . '"' ."\n";

                            $i--;
                        }
                        $plugin_languages .= '    '.'},'."\n";

                    } else {
                        if($j != 1)
                            $plugin_languages .= '    '.'"' . $key. '"' . ' ' . ':' . ' ' . '"' . '",' . "\n";
                        else
                            $plugin_languages .= '    '.'"' . $key. '"' . ' ' . ':' . ' ' . '"' . '"' . "\n";
                    }

                    $j--;
                }

                $plugin_languages .= '}';

                file_put_contents($plugin_dir,$plugin_languages,LOCK_EX);
                $plugin_success = "1";
            }

            // creating language folder into main application language folder
            $add_file_dir      = FCPATH."application/language/english";
            $scan_add_file_dir = array_diff(scandir($add_file_dir),array('.','..'));
            $new_dir           = FCPATH."application/language/".$name;
            $main_languages = '';
            
            // making new language directory with all files from english folder
            if(!file_exists($new_dir)) 
            {
                mkdir($new_dir,0777,true);

                foreach ($scan_add_file_dir as $file) 
                {
                    // new directory files
                    $dir = $new_dir."/".$file;

                    // creating files in new language directory
                    fopen($dir,"w");

                    // including the english folders file to put contents
                    $existing_languages = array();
                    $lang = array();
                    include $add_file_dir."/".$file;
                    $existing_languages = $lang;

                    // creating array of language file
                    $main_languages  = "<?php"."\n";
                    $main_languages .='$lang = '; 
                    $main_languages .= 'array('."\n";

                    foreach ($existing_languages as $key => $value) 
                    {
                        $main_languages .='    '.'"' . $key. '"' . ' ' . '=>' . ' ' . '"' . '",' . "\n";
                    }

                    $main_languages .= ')';
                    $main_languages .= ';';

                    file_put_contents($dir,$main_languages,LOCK_EX);
                    $main_success = "1";
                }

            }


            // // addon directory
            // $addon_dir         = FCPATH."application/modules/";
            // $scan_addon_dir    = array_diff(scandir($addon_dir),array('.','..'));

            // // creating addon language folder name with language file name and contents
            // foreach ($scan_addon_dir as $direc) 
            // {
            //     $isexist   = $addon_dir.$direc."/language/";

            //     if(file_exists($isexist))
            //     {
            //         $scan_isexist = array_diff(scandir($isexist),array('.','..'));
            //         if(!empty($scan_isexist))
            //         {
            //             $root_dir  = $addon_dir.$direc."/language/english/";
            //             $file_name = array_diff(scandir($root_dir),array('.','..'));

            //             // search directory if exists or not
            //             $searchDir = $addon_dir.$direc."/language/".$name;

            //             if(!file_exists($searchDir)) 
            //             {
            //                 mkdir($searchDir,0777,true);

            //                 $creating_lang_file = $searchDir."/".$file_name[2];

            //                 if(!file_exists($creating_lang_file)) 
            //                 {
            //                     fopen($creating_lang_file,"w");

            //                     include $root_dir.$file_name[2];
            //                     $file_langs = $lang;

            //                     // creating array of language file
            //                     $addon_languages  = "<?php"."\n";
            //                     $addon_languages .='$lang = '; 
            //                     $addon_languages .= 'array('."\n";

            //                     foreach ($file_langs as $key => $value) 
            //                     {
            //                         $addon_languages .='    '.'"' . $key. '"' . ' ' . '=>' . ' ' . '"' . '",' . "\n";
            //                     }

            //                     $addon_languages .= ')';
            //                     $addon_languages .= ';';

            //                     file_put_contents($creating_lang_file,$addon_languages,LOCK_EX);
            //                 }

            //             }
            //         }
            //     }

            // }

            if($main_success == "1" && $plugin_success == "1") echo "1";
            else echo "0";

        }

    }

    public function ajax_get_language_details() 
    {
        $fType     = $this->input->post('fileType');
        $finalType = explode('_', $fType);
        
        // Starting of modal form
        $startform = '<script>
                    $(".all_lang").mCustomScrollbar({
                      autoHideScrollbar:true,
                      theme:"dark-3"
                    });
                </script>
                <form id="language_creating_form" method="post">
                    <input type="hidden" name="language_folder_name" id="language_folder_name" value="">
                    <input type="hidden" name="save_lang_name" id="language_file_id" value="'.$fType.'">
                    <div class="row">
                        <div class="col-12">
                            <div class="langForm">
                                <div class="all_lang" style="overflow: auto; max-height: 450px;">
                                    <table class="table table-condensed" id="add_language_form_table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">'.$this->lang->line("Index (English)").'</th>
                                                <th class="text-center">'.$this->lang->line("Translation").'</th>
                                            </tr>
                                        </thead>
                                        <tbody>';

                        $endform = '    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';

        $return = array();

        // if the file is of main application
        if($finalType[0] == 'main-application') // main application section
        {
            $languagename  = trim($this->input->post("languageName"));
            $language_name = $finalType[1];

            // search if the folder exists
            $search_dir = FCPATH."application/language/".$languagename;

            if(!file_exists($search_dir))
            {
                $return['result'] = 0;
                echo json_encode($return);

            } 
            else 
            {
                if(file_exists($search_dir))
                {  
                    $lang_file  = "";
                    $file_lists = scandir($search_dir,0);
                    if(isset($file_lists[$language_name + 2])) 
                    {
                        $lang_file  = $file_lists[$language_name + 2];
                        include $search_dir."/".$lang_file;
                        $alldata = $lang;
                        $yes = "1";
                    }
                    else 
                    {
                        $directory  = FCPATH."application/language/english/";
                        $file_lists = scandir($directory,0);
                        $lang_file  = $file_lists[$language_name + 2];

                        include FCPATH."application/language/english/".$lang_file;
                        $alldata = $lang;
                    } 
                }

                $langForm = $startform;
                ksort($alldata);
                foreach($alldata as $key=>$value) 
                {
                    $langForm .='<tr role="row" width="100%">
                                <td style="width:50%;">
                                    <textarea class="form-control text_key" type="text" name="lang_index[]" class="form-control" readonly>' .$key. '</textarea>
                                </td>
                                <td style="width:50%;">
                                    <textarea type="text" class="form-control text_value" name="lang_data[]" class="form-control">';
                                    if(!empty($value) && isset($yes))
                                        $langForm.=$value;
                                    else 
                                        $langForm .= "";
                    $langForm .= '</textarea>
                                </td>
                            </tr>';
                }
                                
                $langForm .= $endform;

                $return['result'] = 1;
                $return['langForm'] = $langForm;

                echo json_encode($return);
            }
        } 
        else if($finalType[0] == "plugin") // plugin section
        {
            $language_name = $finalType[1];
            $languagename  = trim($this->input->post("languageName"));

            $directory2 = FCPATH."assets/modules/datatables/language/";
            $files      = scandir($directory2,0);
            $thefile    = $directory2."english.json";

            $new_file_existance = $directory2.$languagename.".json";

            // checking the new file exists or not
            if(!file_exists($new_file_existance)) 
            {
                $return['result'] = "0";
                echo json_encode($return);
            } else 
            {
                if(file_exists($new_file_existance))
                {
                    $fileContents = file_get_contents($new_file_existance);
                    $fileContents_decode = json_decode($fileContents,true);

                    $lang = array();

                    foreach ($fileContents_decode as $key => $value) 
                    {
                        if($key == "oPaginate")
                        {
                            foreach ($value as $key1 => $value1) 
                            {
                                $lang[$key1] = $value1;
                            }

                        } else if ($key =='oAria') {

                            foreach ($value as $key1 => $value1) 
                            {
                                $lang[$key1] = $value1;
                            }
                        } else {
                            $lang[$key] = $value;

                        }   
                    }
                    $alldata = $lang;
                    
                    $yes = "1";
                } 

                $langForm = $startform;
                ksort($alldata);
                foreach ($alldata as $key=>$value) 
                {
                    $langForm .='<tr role="row">
                                <td>
                                    <textarea class="form-control text_key" type="text" name="lang_index[]" class="form-control" readonly>' .$key. '</textarea>
                                </td>
                                <td>
                                    <textarea type="text" class="form-control text_value" name="lang_data[]" class="form-control">';
                                        if(!empty($value) && isset($yes))
                                            $langForm.=$value;
                                        else 
                                            $langForm .= "";
                    $langForm .='</textarea>
                                </td>
                            </tr>';
                }

                $langForm .= $endform;

                $return['result'] = "1";
                $return['langForm'] = $langForm;

                echo json_encode($return);
            }

        } 
        // else if($finalType[0] == 'add-on') // addon section
        // {
        //     $language_name   = $finalType[1];
        //     $languagename    = trim($this->input->post("languageName"));
        //     $addon_directory = FCPATH."application/modules/";
        //     $scan_directory  = array_diff(scandir($addon_directory),array('.','..'));
        //     $module_name     = $scan_directory[$language_name+2];

        //     // check if the new language is exists or not
        //     $new_language_existance = $addon_directory.$module_name."/language/".$languagename;
        //     if(!file_exists($new_language_existance))
        //     {
        //         $return['result'] = "0";
        //         echo json_encode($return);

        //     } else
        //     {
        //         if(file_exists($new_language_existance)) 
        //         {
        //             $theFolder = $new_language_existance."/";
        //             $scan_theFolder = array_diff(scandir($theFolder),array('.','..'));
        //             include $theFolder.$scan_theFolder[2];
        //             $module_languages = $lang;
        //             $yes = "1";

        //         } else 
        //         {
        //             $module_lang_file = FCPATH."application/modules/".$module_name."/language/english/";
        //             $module_lang_dir = scandir($module_lang_file,1);

        //             include $module_lang_file.$module_lang_dir[0];
        //             $module_languages = $lang;
        //         }

        //         $langForm = $startform;
        //         ksort($module_languages);
        //         foreach($module_languages as $key=>$value) 
        //         {
        //             $langForm .='<tr role="row">
        //                         <td>
        //                             <textarea class="form-control text_key" type="text" name="lang_index[]" class="form-control" readonly>' .$key. '</textarea>
        //                         </td>
        //                         <td>
        //                             <textarea type="text" class="form-control text_value" name="lang_data[]" class="form-control">';
        //                             if(!empty($value) && isset($yes))
        //                                 $langForm.=$value;
        //                             else 
        //                                 $langForm .= "";
        //             $langForm.='</textarea>
        //                         </td>
        //                     </tr>';
        //         }

        //         $langForm .= $endform;

        //         $return['result'] = "1";
        //         $return['langForm'] = $langForm;

        //         echo json_encode($return);
        //     }
        // }
    }


    public function ajax_language_file_saving()
    {
        if($_POST) {

            $lang_file    = explode("_",$this->input->post("save_lang_name",true));
            $folder_name  = trim($this->input->post('language_folder_name',true));
            $lang_index   = $this->input->post('lang_index[]',true);
            $lang_data    = $this->input->post('lang_data[]',true);
            $combined_arr = array_combine($lang_index, $lang_data);

            // put the key as value if any element value is empty
            foreach ($combined_arr as $key => $value) 
            {
                if(empty($value)) 
                {
                    $combined_arr[$key] = $key;
                }
            }

            
            if($lang_file[0] == "main-application") 
            {
                $file_id = $lang_file[1];

                // include the directory path and scan the direcotry
                $dir      = FCPATH."application/language/english/";
                $scan_dir = scandir($dir,0);

                // getting file name to create the file name in new language
                $file_name           = $scan_dir[$file_id+2];
                $return['file_name'] = str_replace(".php",'',$file_name);

                // getting folder path and create language folder
                $folder_path = FCPATH."application/language/".$folder_name;
                if(!file_exists($folder_path)) {
                    mkdir($folder_path, 0777, true);
                }

                // getting file path with file name and if not exist then create the file
                $file_path = $folder_path."/".$file_name;
                if(!file_exists($file_path)) 
                {
                    fopen($file_path,'w');
                }


                // creating array of language file
                $languages  = "<?php"."\n";
                $languages .='$lang = '; 
                $languages .= 'array('."\n";
                foreach ($combined_arr as $key => $value) {
                    $value = str_replace(array("'",'"'), array('`','`'), $value);
                    $languages .='    '.'"' . $key. '"' . ' ' . '=>' . ' ' . '"' . addslashes($value). '",' . "\n";
                }
                $languages .= ')';
                $languages .= ';';

                // put the translated languages into the file
                file_put_contents($file_path, $languages,LOCK_EX);

                $return['status']  = 1;
                $return['message'] = $this->lang->line("Your data has been successfully saved.");

            }
            else if($lang_file[0] == "plugin") 
            {
                $file_type = $lang_file[1];

                // search the language folder is exists or not, if not then create.
                $folder_dir = FCPATH."application/language/".$folder_name;
                if(!file_exists($folder_dir))
                {
                    mkdir($folder_dir,0777,true);
                }

                $dir     = FCPATH."assets/modules/datatables/language/";
                $files   = scandir($dir,0);
                $thefile = $folder_name.".json";

                // getting file path with file name and if not exist then create the file
                $file_path = $dir.$thefile;
                if(!file_exists($file_path)) 
                {
                    fopen($file_path,'w');
                }

                $languages = '{'."\n";
                $i = count($combined_arr);
                foreach ($combined_arr as $langIndex => $langIndex_val) 
                {
                    $langIndex_val = str_replace(array("'",'"'), array('`','`'), $langIndex_val);
                    if($langIndex == 'sFirst') 
                    {
                        $languages .= '    '.'"'.'oPaginate'.'"'.':'.' '.'{'."\n";
                        $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'",'."\n";

                    } else if($langIndex == 'sLast')
                    {
                        $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'",'."\n";

                    } else if($langIndex == 'sNext')
                    {
                        $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'",'."\n";

                    } else if($langIndex == 'sPrevious')
                    {
                        $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'"'."\n";
                        $languages .= "\t".'},'."\n";

                    }
                    else if($langIndex == 'sSortAscending')
                    {
                        $languages .= '    '.'"'.'oAria'.'"'.':'.' '.'{'."\n";
                        $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'",'."\n";

                    } else if($langIndex == 'sSortDescending')
                    {
                        $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'"'."\n";
                        $languages .= "\t".'},'."\n";

                    }
                    else 
                    {
                        if($i != 1)
                            $languages .='    '.'"' . $langIndex. '"' . ' ' . ':' . ' ' . '"' . addslashes($langIndex_val). '",' . "\n";
                        else
                            $languages .='    '.'"' . $langIndex. '"' . ' ' . ':' . ' ' . '"' . addslashes($langIndex_val). '"' . "\n";
                    }

                    $i--;
                }

                $languages .= '}';

                // put the translated languages into the file
                file_put_contents($file_path, $languages,LOCK_EX);

                $return['status']   = 1;
                $return['message']  = $this->lang->line("Your data has been successfully saved.");
                $return['file_name'] = "plugins1";

            }
            else if($lang_file[0] == "add-on") 
            {
                $file_type      = $lang_file[1];
                $dir            = FCPATH."application/modules/";
                $addon_dir_scan = scandir($dir,0);
                $addons_list    = array();

                for ($i = 2; $i < count($addon_dir_scan) ; $i++) 
                {
                    array_push($addons_list,$addon_dir_scan[$i]);
                }

                // creating addon language folder name
                $addon_folder_path = $dir.$addons_list[$file_type]."/language/".$folder_name;
                if(!file_exists($addon_folder_path)) 
                {
                    mkdir($addon_folder_path,0777,true);
                }

                // generate the file name
                $addon_language_file_folder      = $dir.$addons_list[$file_type]."/language/english/";
                $addon_language_file_folder_scan = scandir($addon_language_file_folder,0);
                $addon_language_file_name        = $addon_language_file_folder_scan[2];

                $language_file_path = $addon_folder_path."/".$addon_language_file_name;
                if(!file_exists($language_file_path))
                {
                    fopen($language_file_path,'w');
                }

                // creating array of language file
                $languages  = "<?php"."\n";
                $languages .='$lang = '; 
                $languages .= 'array('."\n";
                foreach ($combined_arr as $key => $value) {
                    $value = str_replace(array("'",'"'), array('`','`'), $value);
                    $languages .='    '.'"' . $key. '"' . ' ' . '=>' . ' ' . '"' . addslashes($value). '",' . "\n";
                }
                $languages .= ')';
                $languages .= ';';

                // put the translated languages into the file
                file_put_contents($language_file_path, $languages,LOCK_EX);

                $return['status']    = 1;
                $return['message']   = $this->lang->line("Your data has been successfully saved.");
                $return['file_name'] = str_replace("_",'',$addons_list[$file_type]);

            } else 
            {
                $return['status']  = 0;
                $return['message'] = $this->lang->line("Something went wrong, please try again.");
            }

            echo json_encode($return);

        }
    }


    public function edit_language($lang_name)
    {
        $type          = $this->uri->segment(4);
        $language_name = $lang_name;

        if($type == "main_app") 
        {
            $dir          = FCPATH."application/language/".$language_name;
            $scan_dir     = scandir($dir,0);
            $folder_files = array();

            for ($i = 2; $i < count($scan_dir) ; $i++) 
            {
                array_push($folder_files, $scan_dir[$i]);
            }
            $data['folderFiles'] = $folder_files;

        } else if($type == 'plugin') 
        {
            $dir                 = FCPATH."assets/modules/datatables/language/".$language_name;
            $data['plugin_file'] = $language_name;
        
        } else if($type == 'addon')
        {
            $module_dir      = FCPATH."application/modules/".$language_name."/language/";
            if(file_exists($module_dir))
            {
                $scan_module_dir = scandir($module_dir,0);
                if(!empty($scan_module_dir))
                {
                    $folder_file     = array();

                    for ($i = 2; $i < count($scan_module_dir); $i++) 
                    {
                        array_push($folder_file,$scan_module_dir[$i]);
                    }

                    $data['module_language_folders'] = $folder_file;
                }
            }
        }

        $data['languagename'] = $language_name;
        $data['languageName'] = $type;
        $data['page_title']   = $this->lang->line("Update Language");
        $data['body']         = 'admin/multi_language/update_language';
        $this->_viewcontroller($data);
    }

    public function updating_language_name()
    {   
        $result = array();
        $updated_language_name = strip_tags(trim(strtolower($this->input->post("languagename",true))));
        
        if(!preg_match("/^([a-zA-Z_])+$/i",$updated_language_name))
        {
            $result['status'] = 3;
            echo json_encode($result);

        }
        else 
        {
            $pre_language = trim($this->input->post('pre_value',true));
            
            // checking if the language is alrady exist,return false 
            $dir              = FCPATH."application/language/";
            $scan_dir         = array_diff(scandir($dir), array('.','..'));
            $search_existance = array_search($updated_language_name, $scan_dir);

            if($search_existance != '')
            {
                $result['status'] = 0;

            } else
            {
                $prev_dir    = $dir.$pre_language;
                $updated_dir = $dir.$updated_language_name;

                if(file_exists($prev_dir))
                {
                    rename($prev_dir,$updated_dir);
                }

                $plugin_dir  = FCPATH."assets/modules/datatables/language/".$pre_language.".json";
                $updated_dir = FCPATH."assets/modules/datatables/language/".$updated_language_name.".json";

                if(file_exists($plugin_dir))
                {
                    rename($plugin_dir,$updated_dir);
                }

                // $addon_dir      = FCPATH."application/modules/";
                // $scan_addon_dir = array_diff(scandir($addon_dir), array('.','..'));
                // foreach ($scan_addon_dir as $addon) 
                // {
                //     $isexist = $addon_dir.$addon."/language";
                //     if(file_exists($isexist))
                //     {
                //         $scanIsexist = array_diff(scandir($isexist),array('.','..'));
                //         if(!empty($scanIsexist))
                //         {
                //             $addon_lang_dir = $addon_dir.$addon."/language/".$pre_language;
                //             $update_lang_name = $addon_dir.$addon."/language/".$updated_language_name;
                //             if(file_exists($addon_lang_dir))
                //             {
                //                 rename($addon_lang_dir,$update_lang_name);
                //             }
                //         }
                //     }
                // }

                $result['status'] = 1;
                $result['new_name'] = $updated_language_name;
            }

            echo json_encode($result);
        } 
    }


    public function ajax_get_lang_file_data_update()
    {
        $fType          = $this->input->post('fileType');
        $updated_or_not = trim($this->input->post("langname_existance",true));
        $search_dir     = FCPATH."application/language/".$updated_or_not;
        $finalType      = explode('_', $fType);
        $result = array();

        // Starting of modal form
        $startform = '<script>
                    $(".all_lang").mCustomScrollbar({
                      autoHideScrollbar:true,
                      theme:"dark-3"
                    });
                </script>
                <form id="language_creating_form" method="post">
                    <input type="hidden" name="language_folder_name" id="language_folder_name" value="'.$this->input->post('languageName').'">
                    <input type="hidden" name="save_lang_name" id="language_file_id" value="'.$fType.'">
                    <div class="row">
                        <div class="col-lg-12 col-xs-12 col-md-12">
                            <div class="langForm"box-shadow: 3px 3px 3px #cccccc, -2px 0px 3px #cccccc">
                                <div class="all_lang" style="overflow: auto; max-height: 450px;"">
                                    <table class="table table-condensed" id="update_language_form_table">
                                        <thead>
                                            <tr>
                                                <th class="text-center">'.$this->lang->line("Index (English)").'</th>
                                                <th class="text-center">'.$this->lang->line("Translation").'</th>
                                            </tr>
                                        </thead>
                                        <tbody>';

                            $endform = '</tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';


        // if the file is of main application
        if($finalType[0] == 'main-application') 
        {
            if(!file_exists($search_dir))
            {
                $result['status'] = 3;
                echo json_encode($result);

            } else
            {
                $language_file_id = $finalType[1];
                $folderName       = $this->input->post('languageName');
                $directory        = FCPATH."application/language/".$folderName;
                $file_lists       = scandir($directory,0);
                $lang_file        = $file_lists[$language_file_id + 2];

                include $directory."/".$lang_file;
                $alldata = $lang;

                $langForm = $startform;
                ksort($alldata);
                foreach($alldata as $key=>$value) 
                {
                    $langForm .='<tr role="row" width="100%"><td style="width:50%"><textarea class="form-control text_key" type="text" name="lang_index[]" class="form-control" readonly>' .$key. '</textarea></td><td style="width:50%"><textarea type="text" class="form-control text_value" name="lang_data[]" class="form-control">' .$value. '</textarea></td></tr>';
                }
                                
                $langForm .= $endform;

                $result['status'] = 1;
                $result['langForm'] = $langForm;
                echo json_encode($result);
            }

        } 
        else if($finalType[0] == "plugin") 
        {
            $fileName         = $this->input->post('languageName');
            $language_file_id = $finalType[1];

            $plugindir = FCPATH."assets/modules/datatables/language/".$fileName.".json";
            $fileContents = file_get_contents($plugindir);
            $fileContents_decode = json_decode($fileContents,true);

            $lang = array();

            foreach ($fileContents_decode as $key => $value) 
            {
                if($key == "oPaginate")
                {
                    foreach ($value as $key1 => $value1) 
                    {
                        $lang[$key1] = $value1;
                    }

                } else if ($key =='oAria') {

                    foreach ($value as $key1 => $value1) 
                    {
                        $lang[$key1] = $value1;
                    }

                } else {

                    $lang[$key] = $value;

                }   
            }

            $alldata = $lang;

            $langForm = $startform;
            ksort($alldata);
            foreach ($alldata as $key=>$value) 
            {
                $langForm .='<tr role="row">
                            <td>
                                <textarea class="form-control text_key" type="text" name="lang_index[]" class="form-control" readonly>' .$key. '</textarea>
                            </td>
                            <td>
                                <textarea type="text" class="form-control text_value" name="lang_data[]" class="form-control">' .$value. '</textarea>
                            </td>
                        </tr>';
            }

            $langForm .= $endform;

            $result['status'] = 1;
            $result['langForm'] = $langForm;
            echo json_encode($result);

        } 
        else if($finalType[0] == 'add-on') 
        {
            $language_file_id       = $finalType[1];
            $addonName              = $this->input->post('languageName');
            $addon_directory        = FCPATH."application/modules/".$addonName."/language/";
            if(file_exists($addon_directory))
            {
                $scan_directory         = scandir($addon_directory,0);
                $module_language_folder = $scan_directory[$language_file_id + 2];
                $module_lang_file       = FCPATH."application/modules/".$addonName."/language/".$module_language_folder;
                $module_lang_dir        = scandir($module_lang_file,0);

                include $module_lang_file."/".$module_lang_dir[2];
                $module_languages = $lang;

                $langForm = $startform;
                ksort($module_languages);
                foreach($module_languages as $key=>$value) 
                {
                    $langForm .='<tr role="row">
                                <td>
                                    <textarea class="form-control text_key" type="text" name="lang_index[]" class="form-control" readonly>' .$key. '</textarea>
                                </td>
                                <td>
                                    <textarea type="text" class="form-control text_value" name="lang_data[]" class="form-control">' .$value. '</textarea>
                                </td>
                            </tr>';
                }

                $langForm .= $endform;

                $result['status'] = 1;
                $result['langForm'] = $langForm;
                echo json_encode($result);
            }
        }

    }

    public function ajax_updating_lang_file_data()
    {
        if($_POST)
        {
            $clicked_type = explode("_",$this->input->post("save_lang_name",true));
            $lang_index   = $this->input->post('lang_index[]',true);
            $lang_data    = $this->input->post('lang_data[]',true);
            $combined_arr = array_combine($lang_index, $lang_data);

            // put the key as value if any element value is empty
            foreach ($combined_arr as $key => $value) 
            {
                if(empty($value)) 
                {
                    $combined_arr[$key] = $key;
                }
            }


            if($clicked_type[0] == "main-application")
            {
                $language_folder      = $clicked_type[1];
                $language_folder_name = trim($this->input->post("language_folder_name",true));
                $directory            = FCPATH."application/language/".$language_folder_name;
                $file_path            = $directory."/";
                $scan_file_path       = scandir($file_path,0);
                $updated_file_name    = $scan_file_path[$language_folder+2];
                $file_path            = FCPATH."application/language/".$language_folder_name."/".$updated_file_name;

                // file doesnot exist in the folder then return false
                if(!file_exists($file_path)) 
                {
                    $return['status']   = 0;
                    $return['message']  = $this->lang->line("Something went wrong, please try again.");
                } else
                {
                    // creating array of language file
                    $languages  = "<?php"."\n";
                    $languages .='$lang = '; 
                    $languages .= 'array('."\n";
                    foreach ($combined_arr as $key => $value) 
                    {
                        $value = str_replace(array("'",'"'), array('`','`'), trim($value));
                        $languages .='    '.'"' . $key. '"' . ' ' . '=>' . ' ' . '"' . addslashes($value). '",' . "\n";
                    }
                    $languages .= ')';
                    $languages .= ';';

                    // updating the file
                    file_put_contents($file_path,$languages,LOCK_EX);

                    $return['status']   = 1;
                    $return['message']  = $this->lang->line("Your data has been updated successfully.");
                    $return['fileName'] = str_replace(".php","",$updated_file_name);
                }
                
                echo json_encode($return);

            } else if($clicked_type[0] == "plugin")
            {
                $language_folder = $clicked_type[1];
                $language_folder_name = $this->input->post("language_folder_name",true);

                $dir = FCPATH."assets/modules/datatables/language/".$language_folder_name.".json";
                if(!file_exists($dir))
                {
                    $return['status']   = 0;
                    $return['message']  = $this->lang->line("Something went wrong, please try again.");
                } else
                {

                    $languages = '{'."\n";
                    $i = count($combined_arr);
                    foreach ($combined_arr as $langIndex => $langIndex_val) 
                    {
                        $langIndex_val = str_replace(array("'",'"'), array('`','`'), $langIndex_val);

                        if($langIndex == 'sFirst') 
                        {
                            $languages .= '    '.'"'.'oPaginate'.'"'.':'.' '.'{'."\n";
                            $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'",'."\n";

                        } else if($langIndex == 'sLast')
                        {
                            $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'",'."\n";

                        } else if($langIndex == 'sNext')
                        {
                            $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'",'."\n";

                        } else if($langIndex == 'sPrevious')
                        {
                            $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'"'."\n";
                            $languages .= "\t".'},'."\n";

                        }
                        else if($langIndex == 'sSortAscending')
                        {
                            $languages .= '    '.'"'.'oAria'.'"'.':'.' '.'{'."\n";
                            $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'",'."\n";

                        } else if($langIndex == 'sSortDescending')
                        {
                            $languages .= "\t".'    '.'"'.$langIndex.'"'.' '.':'.' '.'"'.addslashes($langIndex_val).'"'."\n";
                            $languages .= "\t".'},'."\n";

                        }
                        else 
                        {
                            if($i != 1)
                                $languages .='    '.'"' . $langIndex. '"' . ' ' . ':' . ' ' . '"' . addslashes($langIndex_val). '",' . "\n";
                            else
                                $languages .='    '.'"' . $langIndex. '"' . ' ' . ':' . ' ' . '"' . addslashes($langIndex_val). '"' . "\n";
                        }

                        $i--;
                    }

                    $languages .= '}';

                    // putting updated translation into the file
                    file_put_contents($dir,$languages,LOCK_EX);

                    $return['status']   = 1;
                    $return['message']  = $this->lang->line("Your data has been updated successfully.");
                    $return['fileName'] = "plugins1";
                }

                echo json_encode($return);

            } else if($clicked_type[0] == "add-on")
            {
                $language_folder        = $clicked_type[1];
                $module_name            = $this->input->post("language_folder_name",true);
                $module_dir             = FCPATH."application/modules/".$module_name."/language/";
                if(file_exists($module_dir))
                {
                    $scan_module_dir        = scandir($module_dir,0);
                    $module_lang_folder     = $scan_module_dir[$language_folder+2];
                    $module_lang_folder_dir = $module_dir.$module_lang_folder;
                    $scan_module_lang_file  = scandir($module_lang_folder_dir,0);
                    $theFile                = $module_lang_folder_dir."/".$scan_module_lang_file[2];

                    if(!file_exists($theFile))
                    {
                        $return['status']  = 0;
                        $return['message'] = $this->lang->line("Something went wrong, please try again.");
                    } else
                    {
                        // creating array of language file
                        $languages  = "<?php"."\n";
                        $languages .='$lang = '; 
                        $languages .= 'array('."\n";
                        foreach ($combined_arr as $key => $value) 
                        {
                            $value = str_replace(array("'",'"'), array('`','`'), $value);
                            $languages .='    '.'"' . $key. '"' . ' ' . '=>' . ' ' . '"' . addslashes($value). '",' . "\n";
                        }
                        $languages .= ')';
                        $languages .= ';';

                        //putting file into the file
                        file_put_contents($theFile,$languages,LOCK_EX);

                        $return['status'] = 1;
                        $return['message'] = $this->lang->line("Your data has been updated successfully.");
                        $return['fileName'] = $module_lang_folder;
                    }

                    echo json_encode($return);
                }
            }
        }
    }


    public function downloading_language_folder_zip()
    {
        $this->load->library('zip');
        $this->load->helper('download');

        $download_type          = $this->uri->segment(5);
        $download_language      = $this->uri->segment(3);
        $download_language_type = $this->uri->segment(4);

        if($download_language_type == "main_app") 
        {   
            $path = FCPATH.'application/language/'.$download_language;
            $this->zip->read_dir($path,FALSE);

            // Download
            $this->zip->download($download_language);
        }
        else if($download_language_type == "plugin")   
        {
            $path  = FCPATH."assets/modules/datatables/language/".$download_language.".json";
            $this->zip->read_file($path,FALSE);

            // Download
            $this->zip->download(str_replace(".json","",$download_language));
        }
        // else if($download_type == "addons")
        // {
        //     $addon_Name = $download_language;
        //     $folder = $download_language_type;

        //     $path = FCPATH."application/modules/".$addon_Name."/language/".$folder;
        //     $this->zip->read_dir($path,FALSE);

        //     // Download
        //     $this->zip->download($folder);
        // }
    }

    public function get_addon_folders_to_download()
    {
        if($_POST)
        {
            $addon_name = $this->input->post("addon");
            $addon_dir  = FCPATH."application/modules/".$addon_name."/language";
            $scan_addon_dir = scandir($addon_dir,0);

            $rmv = array(".","..");

            $addon_language_folders = array_diff($scan_addon_dir,$rmv);
            $the_div                = '<div class="row">';

            foreach ($addon_language_folders as $value) 
            {
                $the_div.='
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <a target="_blank" href="'.base_url().'Multi_language/downloading_language_folder_zip/'.$addon_name.'/'.$value.'/addons" class="no_hover">
                                <div class="download_folder"><i class="fa fa-download"></i> &nbsp;'.$value.'</div>
                            </a>
                        </div>
                    </div>
                </div>';
            }

            $the_div .= '<div>';

            echo $the_div;

        } else
        {
            echo "0";
        }
    }


    public function get_all_languages_to_delete()
    {
        $directory      = FCPATH."application/language/";
        $scan_directory = array_diff(scandir($directory), array('.','..'));

        if(!empty($scan_directory))
        {
            $the_div = '<div class="row">';

            foreach ($scan_directory as $value) 
            {
                $the_div.='
                <div class="col-md-3 col-12">
                    <div class="card">
                      <div class="card-body">
                        <a href="#" class="no_hover" title="'.$this->lang->line('Click to delete').'"><div class="delete_language">'.$value.'</div></a>
                      </div>
                    </div>                 
                </div>';
            }

            $the_div .= '<div>';
            echo $the_div;
        } else
        {
            echo "0";
        }
    }

    public function delete_language_from_all()
    {
        $delete_language    = $this->input->post("langname");
        $main_dir           = FCPATH."application/language/";
        $scan_main_dir      = array_diff(scandir($main_dir), array('.','..'));

        // delete from the main language folder
        if(in_array($delete_language, $scan_main_dir))
        {
            $dir = $main_dir.$delete_language;
            $main_success = '1';
            $this->delete_directory($dir);
        }

        // delete from plugin folder
        $plugin_dir = FCPATH."assets/modules/datatables/language/".$delete_language.".json";
        if(file_exists($plugin_dir)) 
        {
            $plugin_success = '1';
            unlink($plugin_dir);

        } else 
        {
            $plugin_success = '0';
        }

        // delete from all modules language folder
        // $addon_dir      = FCPATH."application/modules/";
        // $scan_addon_dir = array_diff(scandir($addon_dir), array('.','..'));

        // foreach ($scan_addon_dir as $modules) 
        // {
        //     $isexist = $addon_dir.$modules."/language";
        //     if(file_exists($isexist))
        //     {
        //        $src_dir = $addon_dir.$modules."/language/".$delete_language;
        //        if(file_exists($src_dir))
        //        {
        //            $addon_success = '1';
        //            $this->delete_directory($src_dir);
        //        } 
        //     }
        // }

        if(($main_success =='1') && ($plugin_success=='1')) echo '1';
        else echo '0';
    }

}
