<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author konok, cse,Ru.
 * @mobile 01722977459
 * @emial  konok_pepsi@yahoo.com
 * @category file
 * @copyright 2012
 */
?>
<?php
class File {
    public static $file;  // it is the name of the file as a array $file=$_FILES['name']
    public static $extension;  //array which extension file we want to upload
    public static $directory;  //in which directory we want to uploaded. 
    public static $new_file_name='ok';  // what is the modified name of the file
    public static $table_name;     //table name for in which table csv file will be uploaded
    public static $attribute=array();       //table attributr name for csv upload
	public static $show='no';
    public static $comma="";
	public static $line="";
	public static $file_size='';
	public $CI;
	
	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->database();
	}
	
    public function upload_file(){
			 
			 if(self::$file){
                $file_name=stripslashes(self::$file['name']);
                $ext=self::getExtension($file_name);
				$size=self::$file['size'];
     		 	$path=$_SERVER['DOCUMENT_ROOT']."/".$this->CI->config->item('root_folder')."/";
			 	$actual_path=$path.self::$directory.self::$new_file_name;
				if(self::$file_size!='' && $size>self::$file_size){
					$kb_size=self::$file_size/(1024);
					echo "File size must be in ".$kb_size." KB";
				}
                else if(self::chek_extension($ext)){
                    $actual_path=$path.self::$directory.self::$new_file_name.".".$ext;
           	    	$copied=copy(self::$file['tmp_name'], $path.self::$directory.self::$new_file_name.".".$ext);		    
                }
                else
                {
                 echo "sorry unknown file extension";
                }
               return $copied;                
            }     
    }
    
    public static function getExtension($file_name) {
         $i = strrpos($file_name,".");
         if (!$i) { return ""; }
         $l = strlen($file_name) - $i;
         $ext = substr($file_name,$i+1,$l);   
         return $ext;
 }
 
    private static function chek_extension($ext){
        return in_array($ext,self::$extension);    
    }

public function csv_upload(){
    self::$extension=array('csv');
   $copy=self::upload_file();
   if($copy){
    $row=implode(",",self::$attribute);
	
    $upload_path= $_SERVER['DOCUMENT_ROOT']."/".$this->CI->config->item('root_folder')."/".self::$directory.self::$new_file_name.'.csv';

  $table=self::$table_name; 
 $query="LOAD DATA LOCAL INFILE '$upload_path'
  INTO TABLE $table 
  FIELDS TERMINATED BY ',' 
  LINES TERMINATED BY '\n' 
  ($row);";
  
 $this->CI->db->query($query);
  
  echo "Insert Successfully";
  }
  else{
  	 echo " Sorry Couldn't upload";
  }
    
}

	/*Csv Download*/
	public static function make_head($header=array()){
		self::$comma="";
		foreach($header as $head){
			self::make_line($head);
		}
		self::end_line();
	}
		
	public static function make_line($string){
		 self::$line .= self::$comma . '"' . str_replace('"', '""', $string) . '"';
         self::$comma = ",";
		}
		
	public static function end_line(){
		 self::$line .="\n";
		}

	public static function make_csv($file_name){
		$fp = fopen($file_name, 'w');
 		fputs($fp,self::$line);
 		fclose($fp);
	}
	
	/*End of Download*/
}


?>