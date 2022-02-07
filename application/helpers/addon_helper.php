<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Path Helpers
 *
 * @package      CodeIgniter
 * @subpackage   Helpers
 * @category     Helpers
 * @author       Al-amin Jwel
 * @link         http://xeroneit.net
 */

/* used to load assets that stored inside your addon
type * = image/css/js
$asset_path * = yourModuleFolder/directoryPath/assetFile.ext (example)
$css_class = img-responsive (exmaple)
$css_id = thumb (exmaple)
$style = height:200px (exmaple) [only for image]
*/


if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = explode('.',$filename);
        $ext = array_pop($ext);
        $ext = strtolower($ext);
        
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}


function get_addon_asset($type='image',$asset_path="",$css_class="",$css_id="",$style) 
{
  $asset_path=str_replace('\\','/', APPPATH.'modules/'.$asset_path);
  if(!file_exists($asset_path)) return "";

  $css_class_str=$css_id_str=$style_str="";
  if($css_class!="") $css_class_str=" class='{$css_class}'";
  if($css_id!="") $css_id_str=" id='{$css_id}'";
  if($style!="") $style_str=" style='{$style}'";

  if($type=="image")
  {
    $imageData = base64_encode(file_get_contents($asset_path));
    $src = 'data: '.mime_content_type($asset_path).';base64,'.$imageData;
    return '<img src="'.$src.'"'.$css_class_str.$css_id_str.$style_str.'>';
  }
  else if($type=="css")
  {
    $cssData = file_get_contents($asset_path);
    return '<style type="text/css"'.$css_class_str.$css_id_str.'>'.$cssData.'</style>';
  }
  else if($type=="js")
  {
    $jsData = file_get_contents($asset_path);
    return '<script type="text/javascript"'.$css_class_str.$css_id_str.'>'.$jsData.'</script>';
  }
  return "";
}



function addon_exist($module_id=0,$addon_unique_name=""){
    
    if($module_id==0 || $addon_unique_name=="")  return FALSE;

    $ci = &get_instance();
    $ci->load->model('basic');  
    
    $is_module_access=0;  // initially no module access
    $is_addon_installed=0; // Initially ad on not installed
    
    $package_info = $ci->session->userdata("package_info");
    $module_acces= isset($package_info['module_ids']) ? $package_info['module_ids'] : "";
    $module_acces=explode(",",$module_acces);
    
    /* Check if the memeber have the module access*/
    if(in_array($module_id,$module_acces))
         $is_module_access=1; 
        
    /**Check if the addon is installed **/
    $where['where']=array("unique_name"=>$addon_unique_name);
    $addon_info = $ci->basic->get_data("add_ons", $where);
    
    if(isset($addon_info[0]['id']))
         $is_addon_installed=1; 
        
        
    /**If admin and have module installed, then return true***/
    if($ci->session->userdata("user_type")=="Admin" && $is_addon_installed==1)
        return TRUE;
    /**If member and have module installed and have module access, then true***/
    if($ci->session->userdata("user_type")=="Member" && $is_addon_installed==1 && $is_module_access==1)
        return TRUE;
    
    return FALSE;
}


function xit_load_images($path='')
{
    if($path== '')
    {
        return "";
        exit;
    }
    $path_sliced = explode('/', $path);
    $file = array_pop($path_sliced);
    $file_extension_array = explode('.', $file);
    $extension = array_pop($file_extension_array);
    $ci = &get_instance();
    $current_theme = $ci->config->item('current_theme');
    $path = "application/views/site/".$current_theme."/".$path;

    $content = base64_encode(file_get_contents($path));
    if($extension == 'svg')
        $src = 'data:image/svg+xml;base64,'.$content;
    else
        $src = 'data: '.mime_content_type($path).';base64,'.$content;


    return $src;
}

function xit_theme_thumbs($path='')
{
   if($path== '')
   {
       return "";
       exit;
   }

   $content = base64_encode(file_get_contents($path));
   $src = 'data: '.mime_content_type($path).';base64,'.$content;
   return $src; 
}


