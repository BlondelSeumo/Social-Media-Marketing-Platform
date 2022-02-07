<?php require_once('phpwhois-4.2.2/whois.main.php'); // including 

	/**
	* @category library
	* class scraper
	*/
    class Scraper
    {    
        public $scrape_url;
        public $page_content;
        public $scrapped_url=array();
        public $qued_url=array();
        public $scrapped_email=array();
        public $domain;
        public $http;
        public $email_writer;
        public $url_writer;
        public $full_site_scrape=1;
        public $user_id;
        public $domain_id;
        public $download_id;


	    /**
	    * load constructor
	    * @access public
	    * @return void
	    * @param array
	    */
        public function __construct($config=array('url'=>''))
        {
			
            $url=$config['url'];
            $this->scrape_url=$url;
            $this->domain= $this->get_domain_only($url);
                
                /**Get the site is https or http***/
                if (strpos($url, "https")!== false) {
                    $this->http="https";
                } else {
                    $this->http="http";
                }
                    
                    
            $this->CI =& get_instance();
            $this->CI->load->database();
            $this->CI->load->library('session');
            $this->user_id=$this->CI->session->userdata('user_id');
            $this->download_id=$this->CI->session->userdata('download_id');
        
                /**Create download file here with unique name . Download id is the time() function which store during login**/
                
                if ($url) {
                    $this->email_writer=fopen("download/website/email_{$this->user_id}_{$this->download_id}.csv", "w");
                    $this->url_writer=fopen("download/website/url_{$this->user_id}_{$this->download_id}.csv", "w");
                }               
            ignore_user_abort(FALSE);
        }       
        
        
        /**
    	* load constructor
    	* @access public
    	* @param string
    	* @return void
    	*/
        public function extract_css_urls($text)
        {
            $urls = array( );
 
            $url_pattern     = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
            $urlfunc_pattern = 'url\(\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
            $pattern         = '/(' .
         '(@import\s*[\'"]' . $url_pattern     . '[\'"])' .
        '|(@import\s*'      . $urlfunc_pattern . ')'      .
        '|('                . $urlfunc_pattern . ')'      .  ')/iu';
            if (!preg_match_all($pattern, $text, $matches)) {
                return $urls;
            }
 
    // @import '...'
    // @import "..."
    foreach ($matches[3] as $match) {
        if (!empty($match)) {
            $urls['import'][] =
                preg_replace('/\\\\(.)/u', '\\1', $match);
        }
    }
 
    // @import url(...)
    // @import url('...')
    // @import url("...")
    foreach ($matches[7] as $match) {
        if (!empty($match)) {
            $urls['import'][] =
                preg_replace('/\\\\(.)/u', '\\1', $match);
        }
    }
 
    // url(...)
    // url('...')
    // url("...")
    foreach ($matches[11] as $match) {
        if (!empty($match)) {
            $urls['property'][] =
                preg_replace('/\\\\(.)/u', '\\1', $match);
        }
    }
 
            return $urls;
        }

        /**
    	* method to extract html urls
    	* @access public
    	* @param string
    	* @return array
    	*/
        public function extract_html_urls($text)
        {
            $match_elements = array(
                        // HTML
                        array('element'=>'a',       'attribute'=>'href'),       // 2.0
                        array('element'=>'a',       'attribute'=>'urn'),        // 2.0
                        array('element'=>'base',    'attribute'=>'href'),       // 2.0
                        array('element'=>'form',    'attribute'=>'action'),     // 2.0
                        array('element'=>'img',     'attribute'=>'src'),        // 2.0
                        array('element'=>'link',    'attribute'=>'href'),       // 2.0

                        array('element'=>'applet',  'attribute'=>'code'),       // 3.2
                        array('element'=>'applet',  'attribute'=>'codebase'),   // 3.2
                        array('element'=>'area',    'attribute'=>'href'),       // 3.2
                        array('element'=>'body',    'attribute'=>'background'), // 3.2
                        array('element'=>'img',     'attribute'=>'usemap'),     // 3.2
                        array('element'=>'input',   'attribute'=>'src'),        // 3.2

                        array('element'=>'applet',  'attribute'=>'archive'),    // 4.01
                        array('element'=>'applet',  'attribute'=>'object'),     // 4.01
                        array('element'=>'blockquote','attribute'=>'cite'),     // 4.01
                        array('element'=>'del',     'attribute'=>'cite'),       // 4.01
                        array('element'=>'frame',   'attribute'=>'longdesc'),   // 4.01
                        array('element'=>'frame',   'attribute'=>'src'),        // 4.01
                        array('element'=>'head',    'attribute'=>'profile'),    // 4.01
                        array('element'=>'iframe',  'attribute'=>'longdesc'),   // 4.01
                        array('element'=>'iframe',  'attribute'=>'src'),        // 4.01
                        array('element'=>'img',     'attribute'=>'longdesc'),   // 4.01
                        array('element'=>'input',   'attribute'=>'usemap'),     // 4.01
                        array('element'=>'ins',     'attribute'=>'cite'),       // 4.01
                        array('element'=>'object',  'attribute'=>'archive'),    // 4.01
                        array('element'=>'object',  'attribute'=>'classid'),    // 4.01
                        array('element'=>'object',  'attribute'=>'codebase'),   // 4.01
                        array('element'=>'object',  'attribute'=>'data'),       // 4.01
                        array('element'=>'object',  'attribute'=>'usemap'),     // 4.01
                        array('element'=>'q',       'attribute'=>'cite'),       // 4.01
                        array('element'=>'script',  'attribute'=>'src'),        // 4.01

                        array('element'=>'audio',   'attribute'=>'src'),        // 5.0
                        array('element'=>'command', 'attribute'=>'icon'),       // 5.0
                        array('element'=>'embed',   'attribute'=>'src'),        // 5.0
                        array('element'=>'event-source','attribute'=>'src'),    // 5.0
                        array('element'=>'html',    'attribute'=>'manifest'),   // 5.0
                        array('element'=>'source',  'attribute'=>'src'),        // 5.0
                        array('element'=>'video',   'attribute'=>'src'),        // 5.0
                        array('element'=>'video',   'attribute'=>'poster'),     // 5.0

                        array('element'=>'bgsound', 'attribute'=>'src'),        // Extension
                        array('element'=>'body',    'attribute'=>'credits'),    // Extension
                        array('element'=>'body',    'attribute'=>'instructions'),//Extension
                        array('element'=>'body',    'attribute'=>'logo'),       // Extension
                        array('element'=>'div',     'attribute'=>'href'),       // Extension
                        array('element'=>'div',     'attribute'=>'src'),        // Extension
                        array('element'=>'embed',   'attribute'=>'code'),       // Extension
                        array('element'=>'embed',   'attribute'=>'pluginspage'),// Extension
                        array('element'=>'html',    'attribute'=>'background'), // Extension
                        array('element'=>'ilayer',  'attribute'=>'src'),        // Extension
                        array('element'=>'img',     'attribute'=>'dynsrc'),     // Extension
                        array('element'=>'img',     'attribute'=>'lowsrc'),     // Extension
                        array('element'=>'input',   'attribute'=>'dynsrc'),     // Extension
                        array('element'=>'input',   'attribute'=>'lowsrc'),     // Extension
                        array('element'=>'table',   'attribute'=>'background'), // Extension
                        array('element'=>'td',      'attribute'=>'background'), // Extension
                        array('element'=>'th',      'attribute'=>'background'), // Extension
                        array('element'=>'layer',   'attribute'=>'src'),        // Extension
                        array('element'=>'xml',     'attribute'=>'src'),        // Extension

                        array('element'=>'button',  'attribute'=>'action'),     // Forms 2.0
                        array('element'=>'datalist','attribute'=>'data'),       // Forms 2.0
                        array('element'=>'form',    'attribute'=>'data'),       // Forms 2.0
                        array('element'=>'input',   'attribute'=>'action'),     // Forms 2.0
                        array('element'=>'select',  'attribute'=>'data'),       // Forms 2.0

                        // XHTML
                        array('element'=>'html',    'attribute'=>'xmlns'),
                 
                        // WML
                        array('element'=>'access',  'attribute'=>'path'),       // 1.3
                        array('element'=>'card',    'attribute'=>'onenterforward'),// 1.3
                        array('element'=>'card',    'attribute'=>'onenterbackward'),// 1.3
                        array('element'=>'card',    'attribute'=>'ontimer'),    // 1.3
                        array('element'=>'go',      'attribute'=>'href'),       // 1.3
                        array('element'=>'option',  'attribute'=>'onpick'),     // 1.3
                        array('element'=>'template','attribute'=>'onenterforward'),// 1.3
                        array('element'=>'template','attribute'=>'onenterbackward'),// 1.3
                        array('element'=>'template','attribute'=>'ontimer'),    // 1.3
                        array('element'=>'wml',     'attribute'=>'xmlns'),      // 2.0
                    );
                 
            $match_metas = array(
                        'content-base',
                        'content-location',
                        'referer',
                        'location',
                        'refresh',
                    );
                 
                    // Extract all elements
                    if (!preg_match_all('/<([a-z][^>]*)>/iu', $text, $matches)) {
                        return array( );
                    }
            $elements = $matches[1];
            $value_pattern = '=(("([^"]*)")|([^\s]*))';
                 
                    // Match elements and attributes
                    foreach ($match_elements as $match_element) {
                        $name = $match_element['element'];
                        $attr = $match_element['attribute'];
                        $pattern = '/^' . $name . '\s.*' . $attr . $value_pattern . '/iu';
                        if ($name == 'object') {
                            $split_pattern = '/\s*/u';
                        }  // Space-separated URL list
                        elseif ($name == 'archive') {
                            $split_pattern = '/,\s*/u';
                        } // Comma-separated URL list
                        else {
                            unset($split_pattern);
                        }    // Single URL
                        foreach ($elements as $element) {
                            if (!preg_match($pattern, $element, $match)) {
                                continue;
                            }
                            $m = empty($match[3]) ? $match[4] : $match[3];
                            if (!isset($split_pattern)) {
                                $urls[$name][$attr][] = $m;
                            } else {
                                $msplit = preg_split($split_pattern, $m);
                                foreach ($msplit as $ms) {
                                    $urls[$name][$attr][] = $ms;
                                }
                            }
                        }
                    }
                 
                    // Match meta http-equiv elements
                    foreach ($match_metas as $match_meta) {
                        $attr_pattern    = '/http-equiv="?' . $match_meta . '"?/iu';
                        $content_pattern = '/content'  . $value_pattern . '/iu';
                        $refresh_pattern = '/\d*;\s*(url=)?(.*)$/iu';
                        foreach ($elements as $element) {
                            if (!preg_match('/^meta/iu', $element) ||
                                !preg_match($attr_pattern, $element) ||
                                !preg_match($content_pattern, $element, $match)) {
                                continue;
                            }
                            $m = empty($match[3]) ? $match[4] : $match[3];
                            if ($match_meta != 'refresh') {
                                $urls['meta']['http-equiv'][] = $m;
                            } elseif (preg_match($refresh_pattern, $m, $match)) {
                                $urls['meta']['http-equiv'][] = $match[2];
                            }
                        }
                    }
                 
                    // Match style attributes
                    $urls['style'] = array( );
            $style_pattern = '/style' . $value_pattern . '/iu';
            foreach ($elements as $element) {
                if (!preg_match($style_pattern, $element, $match)) {
                    continue;
                }
                $m = empty($match[3]) ? $match[4] : $match[3];
                $style_urls =$this->extract_css_urls($m);
                if (!empty($style_urls)) {
                    $urls['style'] = array_merge_recursive(
                                $urls['style'], $style_urls);
                }
            }
                 
                    // Match style bodies
                    if (preg_match_all('/<style[^>]*>(.*?)<\/style>/siu', $text, $style_bodies)) {
                        foreach ($style_bodies[1] as $style_body) {
                            $style_urls =$this->extract_css_urls($style_body);
                            if (!empty($style_urls)) {
                                $urls['style'] = array_merge_recursive(
                                    $urls['style'], $style_urls);
                            }
                        }
                    }
            if (empty($urls['style'])) {
                unset($urls['style']);
            }
                 
            return $urls;
        }              
                
	    /**
	    * method to add array
	    * @access public
	    * @return array
	    * @param array
	    * @param array
	    */        
        public function array_add($array1, $array2)
        {
            $array_1=$array1;
            $array_2=$array2;
                    
            $arra1_count=count($array_1);
                    
            foreach ($array_2 as $val) {
                $array_1[$arra1_count]=$val;
                $arra1_count++;
            }
                    
            return $array_1;
        }

	    /**
	    * method to get contents
	    * @access public
	    * @return string
	    * @param string
	    * @param string
	    */
        private function getContents($url, $proxy='')
        {
            $ch = curl_init(); // initialize curl handle
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method

            if ($proxy) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
            }               
            
            
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
            curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");
             
             
            $buffer = curl_exec($ch); // run the whole process
            curl_close($ch);
            return $buffer;
        }
        

        /**
    	* method to get url
    	* @access public
    	* @return string
    	* @param string
    	*/    
        public function get_url($content)
        {
            $urls=$this->extract_html_urls($content);
            return    $urls['a']['href'];
        }
        
        public function get_email($content)
        {
            preg_match_all('/([\w+\.]*\w+@[\w+\.]*\w+[\w+\-\w+]*\.\w+)/is', $content, $results);
            return $results[1];
        }
        
        public function get_domain_only($url)
        {
            /***This return domain, subdomain like (webprogrammings.net, sub.webprogrammings.net)***/
             if (!preg_match("@^https?://@i", $url) && !preg_match("@^ftps?://@i", $url)) {
                 $url = "http://" . $url;
             }
                
             /** @ because of removing warning during the string is not a valid url **/
             
             $parsed=@parse_url($url);
            $domain=str_replace("www.", "", $parsed['host']);
             
             /***if domain is not found , then default the domain is the scrapping site domain***/
             if (!$domain || strpos($domain, ".")===false) {
                 $domain=$this->domain;
             }
             
             /**if the domain like (contact.php/contact.html) , means a web file then assign domain is the current domain***/
             if ($this->is_web_page($domain)) {
                 $domain=$this->domain;
             }
             
            return $domain;
        }
            
        /**
    	* method to start scrapping
    	* @access public
    	* @return void
    	* @param string
    	*/    
        public function start_scrapping($proxy='')
        {
            $scrapping_url=$this->scrape_url;
            $content=$this->getContents($scrapping_url, $proxy);
            
            /**Get all Url in this page anchor tag***/
            $found_url=$this->get_url($content);
            if ($found_url) {
                $found_url=array_unique($found_url);
            } else {
                $found_url=array();
            }
            
            foreach ($found_url as $index=>$u) {
                /*** Get the domain of the url, if other domain then don't scrape ***/
                    /**Trim because sometime if the href is surrounded by '' then take ' . so need to remove it**/
                    $u=trim($u, "'");
                $u=trim($u, "/");
                        
                $scrapping_domain=$this->get_domain_only($u);
                if ($this->domain!=$scrapping_domain || substr($u, 0, 1) == '#') {
                    unset($found_url[$index]);
                } else {
                    /** If not full url that means contact.html then add the domain first ***/
                        if (strpos($u, $this->domain)=== false) {
                            $u=$this->http."://".$this->domain."/{$u}";
                        }
                            
                    $found_url[$index]=$u;
                }
            }
            
            /***Get all email address in this page**/
            $found_email=$this->get_email($content);
            
            
            if ($found_email) {
                $found_email=array_unique($found_email);
            } else {
                $found_email=array();
            }
            
            $found_email_str=implode(",", $found_email);
            if (!$found_email_str) {
                $found_email_str="Not Found";
            }
            echo "<script type=\"text/javascript\">parent.document.getElementById( 'url_list').innerHTML += '<li>$scrapping_url</li>';</script>";
            
            echo "<script type=\"text/javascript\">parent.document.getElementById( 'email_list').innerHTML += '<li>$found_email_str</li>';</script>";
            
            /**Write URL**/
            $write_url=array();
            $write_url[]=$scrapping_url;
            fputcsv($this->url_writer, $write_url);
            
            /**Insert url in the database  table name url **/
            $is_available=1;
            $scraped_time=date("Y-m-d H:i:s");
            
            $db_insert_scraping_url=$this->CI->db->escape($scrapping_url);
            $q="insert into url(user_id,url_name,domain_id,last_scraped_time,is_available) 
							values('$this->user_id',$db_insert_scraping_url,'$this->domain_id','$scraped_time','$is_available')";
            
            $this->CI->db->query($q);
            
            $url_id =  $this->CI->db->insert_id();
            
            
            /***Write Email***/
            foreach ($found_email as $f_email) {
                if ($f_email) {
                    $write_email=array();
                    $write_email[]=$f_email;
                    fputcsv($this->email_writer, $write_email);
                    
                    $db_insert_f_email=$this->CI->db->escape($f_email);
                    /*** insert email into database table_name email ***/
                    $q="Insert into email(user_id,domain_id,url_id,found_email)
										 values('$this->user_id','$this->domain_id','$url_id',$db_insert_f_email)";
                            
                    $this->CI->db->query($q);
                }
            }
            
            flush();
            // ob_flush();

            
            $this->scrapped_email=$this->array_add($this->scrapped_email, $found_email);
            $this->scrapped_email=array_unique($this->scrapped_email);
            
            
            $this->qued_url=$this->array_add($this->qued_url, $found_url);
            $this->qued_url=array_unique($this->qued_url);
            $this->scrapped_url[]=$scrapping_url;
            
            
            $this->qued_url=array_diff($this->qued_url, $this->scrapped_url);
            $this->qued_url=array_values($this->qued_url);
            
            $this->scrape_url=$this->qued_url[0];
                
            if ($this->scrape_url && $this->full_site_scrape==1) {
                $this->start_scrapping();
            } else {
                echo "<script type=\"text/javascript\">parent.document.getElementById('success_msg').innerHTML = '<center><h3 style=\"color:olive;\">Completed</h3></center>';  </script>";
                
                echo "<script>";
                
                $url_download_link= base_url()."download/website/url_{$this->user_id}_{$this->download_id}.csv";
                $email_download_link= base_url()."download/website/email_{$this->user_id}_{$this->download_id}.csv";
                    
                    
                echo "parent.document.getElementById('url_download_div').innerHTML='<a href=\"{$url_download_link}\" target=\"_blank\" class=\"btn btn-warning\"><i class=\"fa fa-cloud-download\"></i> <b>Download URL</b></a>';";
                    
                echo "parent.document.getElementById('email_download_div').innerHTML='<a href=\"{$email_download_link}\" target=\"_blank\" class=\"btn btn-warning\"><i class=\"fa fa-cloud-download\"></i> <b>Download Email</b></a>';";
                    
                    
                echo "</script>";
            }
        }
        
        
        public function is_web_page($domain)
        {
            $ext=explode(".", $domain);
            $extension=array_pop($ext);
            $allowed_extension=array("html","htm","php","asp","jsp","py");
            
            if (in_array($extension, $allowed_extension)) {
                return 1;
            } else {
                return 0;
            }
        }
        
        
	    /**
	    * method to sent api request
	    * @access public
	    * @return array
	    */    
        public function api_request()
        {
            $scrapping_url=$this->scrape_url;
            $content=$this->getContents($scrapping_url);
            
            /**Get all Url in this page anchor tag***/
            $found_url=$this->get_url($content);
            if ($found_url) {
                $found_url=array_unique($found_url);
            } else {
                $found_url=array();
            }
            
            foreach ($found_url as $index=>$u) {
                /*** Get the domain of the url, if other domain then don't scrape ***/
                    /**Trim because sometime if the href is surrounded by '' then take ' . so need to remove it**/
                    $u=trim($u, "'");
                $u=trim($u, "/");
                        
                $scrapping_domain=$this->get_domain_only($u);
                if ($this->domain!=$scrapping_domain || substr($u, 0, 1) == '#') {
                    unset($found_url[$index]);
                } else {
                    /** If not full url that means contact.html then add the domain first ***/
                        if (strpos($u, $this->domain)=== false) {
                            $u=$this->http."://".$this->domain."/{$u}";
                        }
                            
                    $found_url[$index]=$u;
                }
            }
            
            /***Get all email address in this page**/
            $found_email=$this->get_email($content);
            if ($found_email) {
                $found_email=array_unique($found_email);
            } else {
                $found_email=array();
            }
            
            $found_email_str=implode(",", $found_email);
            if (!$found_email_str) {
                $found_email_str="Not Found";
            }
            
            $this->scrapped_email=array_add($this->scrapped_email, $found_email);
            $this->scrapped_email=array_unique($this->scrapped_email);
            
            
            $this->qued_url=array_add($this->qued_url, $found_url);
            $this->qued_url=array_unique($this->qued_url);
            $this->scrapped_url[]=$scrapping_url;
            
            $this->qued_url=array_diff($this->qued_url, $this->scrapped_url);
            $this->qued_url=array_values($this->qued_url);
            
            $this->scrape_url=$this->qued_url[0];
            
            if ($this->scrape_url && $this->full_site_scrape==1) {
                $this->api_request();
            }            
            
            $data['url']=$this->scrapped_url;
            $data['email']=$this->scrapped_email;
            return $data;
        }        
    
	    /**
	    * method to get content from search engine
	    * @access public
	    * @return string
	    * @param string
	    * @param string
	    */
        public function getContentFromSearchEngine($url, $proxy='')
        {
            $ch = curl_init(); // initialize curl handle
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
            curl_setopt($ch, CURLOPT_AUTOREFERER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 7);
            curl_setopt($ch, CURLOPT_REFERER, 'http://'.$url);
            curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 50); // times out after 50s
            curl_setopt($ch, CURLOPT_POST, 0); // set POST method

            /***** Proxy set for google . if lot of request gone, google will stop reponding. That's why it's should use some proxy *****/
            if ($proxy) {
                curl_setopt($ch, CURLOPT_PROXY, $proxy);
            }
            
            
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");
            curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");
             
            $content = curl_exec($ch); // run the whole process

            curl_close($ch);
            
            /** Remove html tag line <br> <b> in the email **/
            $content=str_replace("<b>", "", $content);
            $content=str_replace("</b>", "", $content);
            $content=str_replace("</br>", "", $content);
            $content=str_replace("<br>", "", $content);
            $content=str_replace("<br/>", "", $content);
            
            /*** These are specially for the bing search engine ***/
            $content=str_replace("<strong>", "", $content);
            $content=str_replace("</strong>", "", $content);
            $content=str_replace(",", "", $content);
                
            return $content;
        }
        
	    /**
	    * method to google search
	    * @access public
	    * @return string
	    * @param string
	    * @param int
	    * @param string
	    */
        public function googleSearch($keyword, $page_number=0, $proxy='')
        {
            $keyword=urlencode($keyword);
            if ($page_number) {
                $page_str="&start={$page_number}";
            } else {
                $page_str="";
            }
                
            $url="https://www.google.com/search?q={$keyword}&num=100&ie=utf-8&oe=utf-8{$page_str}";
            $content=$this->getContentFromSearchEngine($url, $proxy);
            $found_email=$this->get_email($content);
            
            return $found_email;
        }
        
        /**
	    * method to bing search
	    * @access public
	    * @return string
	    * @param string
	    * @param int
	    * @param string
	    */
        public function bingSearch($keyword, $page_number=0, $proxy='')
        {
            $keyword=urlencode($keyword);
            if ($page_number) {
                $start=$page_number*10+1;
                $page_str="&first={$start}";
            } else {
                $page_str="";
            }
                
            $url="https://www.bing.com/search?q={$keyword}&ie=utf-8&oe=utf-8{$page_str}";
            $content=$this->getContentFromSearchEngine($url, $proxy);
            $found_email=$this->get_email($content);
            return $found_email;
        }
        
        /**
	    * method to make keyword
	    * @access public
	    * @return string
	    * @param string
	    * @param string
	    * @param array
	    */
        public function make_keyword($searh_keyword, $domain, $email_provider=array("gmail.com", "yahoo.com"))
        {
            $keyword = 'site:'.$domain.' ';
            foreach ($email_provider as $provider) {
                $keyword.= '"'.$provider.'" or ';
            }
            $keyword.='"'.$searh_keyword.'"';
            return $keyword;
        }
        
        
        /**
	    * method to whois email
	    * @access public
	    * @return array
	    * @param string
	    */
        public function whois_email($domain='')
        {
            $tech_email="";
            $admin_email="";
            $name_server_str="";
            $created_at="";
            $sponsor="";
            $expire_at="";
            $changed_at="";
            
            $domain=trim($domain);
            
            $whois = new Whois();
            $query = $domain;
            $whois->deep_whois=true;
            $result = $whois->Lookup($query, false);
            $rawdata = $result['rawdata'];
            
            $regrinfo = $result['regrinfo'];
            $is_registered=trim($regrinfo['registered']);
            
            if ($is_registered=='yes') {
                $name_servers=$regrinfo['domain']['nserver'];
                foreach ($name_servers as $n_server=>$ip) {
                    $name_server_str.=$n_server.", ";
                }
            
                $name_server_str=trim($name_server_str);
                $name_server_str=trim($name_server_str, ",");
            
                if (isset($regrinfo['domain']['created'])) {
                    $created_at=$regrinfo['domain']['created'];
                }
            
                if (isset($regrinfo['domain']['sponsor'])) {
                    $sponsor= $regrinfo['domain']['sponsor'];
                }
            
                if (isset($regrinfo['domain']['changed'])) {
                    $changed_at= $regrinfo['domain']['changed'];
                }
                
                if (isset($regrinfo['domain']['expires'])) {
                    $expire_at=$regrinfo['domain']['expires'];
                }
            
            
                    
                foreach ($rawdata as $info) {
                    /**Get technical email**/
                $pos=strpos($info, "Tech Email: ");
                    if ($pos!==false) {
                        $tech_email= trim(str_replace("Tech Email: ", "", $info));
                    }
                
                /**get admin email**/
                $pos=strpos($info, "Admin Email: ");
                    if ($pos!==false) {
                        $admin_email=trim(str_replace("Admin Email: ", "", $info));
                    }
                
                    if ($tech_email!='' && $admin_email!='') {
                        break;
                    }
                }
            }
        
            $response['is_registered']=$is_registered;
            $response['tech_email']=$tech_email;
            $response['admin_email']=$admin_email;
            
            $response['name_servers']=$name_server_str;
            $response['created_at']=$created_at;
            $response['sponsor']=$sponsor;
            $response['changed_at']=$changed_at;
            $response['expire_at']=$expire_at;

            return $response;
        }
        
        /**
	    * method to get email from url
	    * @access public
	    * @return string
	    * @param string
	    * @param string
	    */
        public function get_email_from_url($url, $proxy='')
        {
            $content=$this->getContents($url, $proxy);
            /***Get all email address in this page**/
            $found_email=$this->get_email($content);
            
            
            if ($found_email) {
                $found_email=array_unique($found_email);
            } else {
                $found_email=array();
            }
                
            return $found_email;
        }
    	
    	/**
	    * method to get random value from array
	    * @access public
	    * @return string
	    * @param array
	    * @param string
	    */
        public function random_value_from_array($array, $default=null)
        {
            $k = mt_rand(0, count($array) - 1);
            return isset($array[$k])? $array[$k]: $default;
        }
        

        /**
	    * method to validate email
	    * @access public
	    * @return array
	    * @param string
	    */
        public function email_validate($email)
        {
            $email=trim($email);
            $is_valid=0;
            $is_exists=0;
            
            /***Validation check***/
            $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';

            if (preg_match($pattern, $email) === 1) {
                $is_valid=1;
            }
            
            /*** MX record check ***/
            @list($name, $domain)=explode('@', $email);
            if (!checkdnsrr($domain, 'MX')) {
                $is_exists=0;
            } else {
                $is_exists=1;
            }
                        
            $result['is_valid']=$is_valid;
            $result['is_exists']=$is_exists;
            return $result;
        }
        
    
    /*
        $get_info variable return an array like :
        
        Array ( [url] => http://xeroneitdddd.net 
        [content_type] => 
        [http_code] => 0 
        [header_size] => 0 
        [request_size] => 0 
        [filetime] => -1 
        [ssl_verify_result] => 0 
        [redirect_count] => 0 
        [total_time] => 0 
        [namelookup_time] => 0 
        [connect_time] => 0 
        [pretransfer_time] => 0 
        [size_upload] => 0 
        [size_download] => 0 
        [speed_download] => 0 
        [speed_upload] => 0 
        [download_content_length] => -1 
        [upload_content_length] => -1 
        [starttransfer_time] => 0 
        [redirect_time] => 0 ) 
        
    
    */
        /**
	    * method to page status check
	    * @access public
	    * @return string
	    * @param string	    
	    */
        public function page_status_check($url)
        {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $options = array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER         => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_USERAGENT      => $useragent,
                    CURLOPT_AUTOREFERER    => true,
                    CURLOPT_CONNECTTIMEOUT => 30,
                    CURLOPT_TIMEOUT        => 30,
                    CURLOPT_MAXREDIRS      => 10,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
            );
            $ch = curl_init($url);
            curl_setopt_array($ch, $options);
            curl_exec($ch);
            $get_info = curl_getinfo($ch);
            $httpcode=$get_info['http_code'];
            curl_close($ch);
            return $get_info;
        }
    }
