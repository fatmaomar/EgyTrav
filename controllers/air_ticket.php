<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class air_ticket extends CI_Controller {

	public $template_name ; 
	public $theme_helper;
	
	public $concept  = "air_ticket" ; 
	
	public $controller = "airticket/air_ticket"; //controller path 	
		
	public $class_name = "bi_air_ticket" ; 
	public $class_path =  "airticket/bi_air_ticket" ; //model path
 
 	public $view_folder = "airticket"; //view path
	
	public $lang_file = "business/air_ticket_main" ;
	
	public $id_field  = "TKT_ID"; 
	
	
function __construct()
{
	parent::__construct();
	
}	
public function index()
{
	$this->master()  ; 
}

//_________________________________________________________________________________________________			

	// main public loader & rights validator 		
public function _top_function($component_code,$second_time='no')
{
	$this->my_output->nocache(); 		
	$this->load->model("admin/admin_public") ; 	
	// start with the public items always 	
	 
	$this->lang->load("config/config",$this->admin_public->DATA["system_lang"]) ;


//--------------------------------LOGIN AUTO--------------------------
	$let_me_in = "no" ;      
    if ($second_time == $this->config->item("SecretKey"))
    	$let_me_in="let_me_in" ; 

//----------------------------------------------------------------------

	if ($this->template_name=="") $this->template_name=r_langline("admin_template_name");
	if ($this->theme_helper=="") $this->theme_helper = r_langline("admin_template_helper"); 
	  
	$this->load->helper($this->theme_helper)	;    		
	$this->admin_public->DATA["template_folder"] =  "_templates/".$this->template_name."/" ;	
	
//-------------------------------LOGIN AUTO---------------------------------------
	
	if (!$this->admin_public->load($component_code,$let_me_in))
	{return false;} else {}
	
//-------------------------------turn OFF LOGIN AUTO---------------------------------------
	//if (!$this->admin_public->load($component_code)) return false;  

//----------------------------------------------------------------------
	
		 
	// needed in all the controllers functions,  
	// loaded any way ..............$this->load->model("admin/bi_user");

	$this->load->model($this->class_path);
	$this->load->model("airticket/bi_air_passenger") ;
	$this->load->model("airticket/bi_air_segment") ;
	$this->load->model("airticket/bi_air_tax") ;  
	return true;
	 
}	

//_________________________________________________________________________________________________

public function master()
{	
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  
		
	
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		
		$data["public_data"] = $this->admin_public->DATA;
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);		// takes care of login / header loading
		return false; 
	}
	
	//to load the all things in the page (table - edit - delete ......)
	$data["public_data"] = $this->admin_public->DATA;
	$data["this_concept"] = $this->concept ; 
	$data["this_controller"] = $this->controller ; 		
	$data["this_lang_file"] = $this->lang_file ; 
		
	$this->load->view( '_general/concept_master_aj',$data);	
				
	return ; 				
}

//_____________________________________________________________________________________________

public function info()
{
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	$this_view_file = "amd_addedit"	; 
	
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  			
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);		; // takes care of login / header loading 
	}
 
 	$this_view = $this->view_folder.'/'.$this_view_file ; 
 	$incoming_id = $this->uri->segment(4, 0); 
	
	
	$data["public_data"] = $this->admin_public->DATA;
	$data["TKT_ID"] = $incoming_id; 
	
	$data["this_concept"] = $this->concept ; 
	$data["this_controller"] = $this->controller ; 
	//$data["this_lang_folder"] = "trans"	;
	$data["this_id_field"] = $this->id_field ; 
	
			
	$this->load->view( $this_view , $data );	
	
}

//_____________________________________________________________________________________________
public function test_read_files()
{
	$path = $this->config->item("air_files_path");
			
	$dir = opendir($path);
	
	
	//----------------------------------- Method 1 -----------------------------
	$files = glob($path."*.{air,AIR}" , GLOB_BRACE);
	// Sort files by modified time, latest to earliest
	// Use SORT_ASC in place of SORT_DESC for earliest to latest
	array_multisort(array_map( 'filemtime', $files ),SORT_NUMERIC,SORT_ASC,$files);
	
	echo"files in date ASC order<br><pre>";
	print_r($files)  ;
	return;
	foreach($files as $key => $value)
	{
		$file_name = substr($value , -13);
		//echo"file name is >> $file_name <br>";
	}
	
	//return;
	
	//----------------------------------- Method 2 -----------------------------
	
	$list = array();
	while($file = readdir($dir))
	{
		//echo"file name is >> $file <br>";
		if ($file != '.' and $file != '..')
		{
			
			// add the filename, to be sure not to
			// overwrite a array key
			$ctime = filemtime($path . $file) . ',' . $file;
			$list[$ctime] = $file;
		}
	}
	closedir($dir);
	krsort($list);
	echo"all files in araaaaaaaaay <br><pre>";
	print_r ($list);
	
	return ;

	
	$count = 0 ;//consider first two files( . / .. )
	
	if ( (is_dir($dir)) && ($dh = opendir($dir)) )
	{
		// Grab all files from the desired folder
		$files = glob( $dir.'*.*' );
		// Sort files by modified time, latest to earliest
		// Use SORT_ASC in place of SORT_DESC for earliest to latest
		array_multisort(array_map( 'filemtime', $files ),SORT_NUMERIC,SORT_ASC,$files);
		
		echo"files in date ASC order<br><pre>";
		print_r($files)  ;
		//return;
		
		//process 80 files 
	 	while (	($count<=82)	&&	(($file = readdir($dh)) !== false)	)//The readdir() returns the name of the next entry in a directory.
	    {
	    		
			$file_arr[] = $file;
			
			$count++;
		}//end while
		
		echo"all files<br><pre>";
		print_r($file_arr);
		
	closedir($dh);
	}
	
}

//_______________________________________________________________________________________________________

public function main_process()
{
	echo "Starting Time : ". date("h:i:sa") . "<br> ";
	
	$this_item = & $this->bi_air_ticket;      
 	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
//-------------------------------- to LOGIN AUTO use first line--------------------------
//------------- remember to Uncomment the LOGOUT AUTO line in the end of function -------------------

	if (!$this->_top_function($access_component_name,$this->input->get("SecretKey"))) return ;
	//if (!$this->_top_function($access_component_name)) return ; 

//---------------------------------------------------------------------
	
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  
			
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		$data["public_data"] = $this->admin_public->DATA;
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);		// takes care of login / header loading
		return false; 
	}
	
	//---------------------------------------------
	// Open a directory, and read its contents	
	$dir = $this->config->item("air_files_path");//$dir = "D:\\process tkt\\airline\\Air_back\\new\\";
	$dest = $this->config->item("air_backup_path");//$dest = "D:\\process tkt\\airline\\copy2\\";
	$errors_dest = $this->config->item("errors_path");//$errors_dest = "D:\\process tkt\\airline\\errors_folder\\";
	
	$failure_file ;//string to check if failure file or success
	
	
	//---------------------- Sort files in folder by Date -------------------------
	
	$files = glob( $dir.'*.{air,AIR}' , GLOB_BRACE);
	// Sort files by modified time, earliest to latest
	// Use SORT_DESC in place of SORT_ASC for latest to earliest
	array_multisort(array_map( 'filemtime', $files ),SORT_NUMERIC,SORT_ASC,$files);
	
	//echo"files in date ASC order<br><pre>";
	//print_r($files)  ;
	
	//-- END of Sorting ------------------------------------------------------------
	
	//------------ if source folder is empty , logout
	//------------ else , make the _LOG_FILE 
	if(empty($files))
	{
		goto logout_now;
	}
	else
	{
		$this->bi_log->start();
	}
	
	
	if ( (is_dir($dir)) && ($dh = opendir($dir)) )
	{
		foreach($files as $key => $value)
		{
			//to get file name
			$file = substr($value , -13);
						
			$fullpath = $dir. $file ;
		
			//------------------ Begin of Process ------------------------------
	
			$main_array = array();//clear main_array every iterate

			//---------GET CONTENT------
	    	$file_content = file_get_contents( $fullpath );
		    $main_array = explode("\n", $file_content);
		  	echo "the content of filename: " . $file . "<br>";
		    echo "<pre>";
		    print_r($main_array) ;
			//return;
			
//--at first -- check tkt type --------------------

			echo"<br>****************** Ticket Main Info **********************<br>";
			$ticket_type_string = $this->check_type_tkt($main_array);
			
			//------------ if not returning string , there's an error
			//----------- and skip this failure file 
			if($ticket_type_string == false)
			{
				echo"<br>ERROR: Can't Detect Ticket Type<br>";
				$failure_file = "<br>NOT defined file-type = $file<br>";
				$this->bi_log->logthis($failure_file,1);
				
				//check filename if not exist before copying process
				//if exist , don't copy it but start process directly
				if (file_exists($errors_dest.$file))
				{
					echo"the file ".$file." is already exist in errors folder<br>";
					
				}
				else
				{
					//----------COPY file to the third folder (errors_folder)------------				
					$new_path = $errors_dest.$file;
					copy ($fullpath,$new_path); 
					echo "<br>the file ".$file." is copied to errors folder<br>";
				}	
				//-----------delete file-----------------
				if($delete = unlink($fullpath) == true )//returns TRUE on success
				echo "that file is deleted from errors folder = $file <br>"; 
				else
				echo "that file NOT deleted from errors folder = $file<br>";					
				//---------------------------------------
				echo"<br><br>*************************************************************************    NXT File    *********************************************************************************<br><br>";
				continue;
			}
							
			else
			{
				//check filename if not exist before copying process
				//if exist , don't copy it but start process directly
				if (file_exists($dest.$file))
				{
					echo"the file ".$file." is already exist <br>";
					
				}
				else
				{
					//----------COPY file------------
				
					$new_path = $dest.$file;
					copy ($fullpath,$new_path); 
					echo "<br>the file ".$file." is copied <br>";
				}
			}

//------------------------ if VOID ticket				
			if( $ticket_type_string == 'VOID')
			{
				echo"<br>****************** for VOID tkt ************************<br>";
				$total_fare_and_id_array = $this->void_main_tkt_info($main_array,$file) ;//void tkt

				echo"<br>****************** Passengers VOID INFO ***********************<br>";
				$ticket_id = $this->pass_name_tkt_number($main_array ,$total_fare_and_id_array , $ticket_type_string) ;//will call pass_name()
				
				//---call function to get the ORIGINAL_TKT of void tkt-------
				//$this_item->void_id($ticket_id);
				
			}

//------------------------ if Refund
			
			elseif($ticket_type_string == 'Refund')
			{
				echo"<br>****************** and Main tkt info and fare refund calculation ************************<br>";
				$total_fare_and_id_array = $this->main_tkt_info($main_array , $ticket_type_string , $file) ;//refund tkt
									
				echo"<br>****************** Passengers Refund INFO ***********************<br>";
				$ticket_id = $this->pass_name_tkt_number($main_array , $total_fare_and_id_array ,$ticket_type_string) ;
				
				echo"<br>****************** TAX details ****************************<br>";
				$this->air_ticket_tax($main_array , $ticket_id ) ;
				
				//---call function to get the ORIGINAL_TKT of refund tkt-------
				//$this_item->refund_id($ticket_id);
				
				
				//echo"refund tkt from main process";
			}

//------------------------ if Reissue
							
			elseif($ticket_type_string == 'Reissue')
			{
				echo"<br>****************** and Main tkt info and fare reissue calculation ************************<br>";
				$total_fare_and_id_array = $this->main_tkt_info($main_array , $ticket_type_string , $file) ;//reissue tkt

				echo"<br>****************** Passengers Reissue INFO ***********************<br>";
				$ticket_id = $this->pass_name_tkt_number($main_array , $total_fare_and_id_array ,$ticket_type_string) ;
				
				echo"<br>****************** SEGMENTS ****************************<br>";
				$this->segment($main_array , $ticket_id ) ;	
				
				//---call function to get the ORIGINAL_TKT of reissue tkt-------
				//$this_item->reissue_id($ticket_id);

			}
			
//------------------------ if Insurance
							
			elseif($ticket_type_string == 'Insurance')
			{
				echo"<br>****************** Insurance main tkt info from main process ***********************<br>";
				$total_fare_and_id_array = $this->main_tkt_info($main_array , $ticket_type_string , $file) ;
				
				echo"<br>****************** Passengers insurance INFO ***********************<br>";
				$ticket_id = $this->pass_name_tkt_number($main_array , $total_fare_and_id_array ,$ticket_type_string) ;
				
				echo"<br>****************** SEGMENTS ****************************<br>";
				$this->segment($main_array , $ticket_id ) ;	
								
			}

//------------------------ if Active
			
			elseif($ticket_type_string == 'Active')
			{
				
				echo"<br>****************** and fare ACTIVE calculation ************************<br>";
				$total_fare_and_id_array = $this->main_tkt_info($main_array , $ticket_type_string , $file) ;//non-void tkt

				echo"<br>****************** Passengers ACTIVE INFO ***********************<br>";
				$ticket_id = $this->pass_name_tkt_number($main_array , $total_fare_and_id_array ,$ticket_type_string) ;
				
				echo"<br>****************** SEGMENTS ****************************<br>";
				$this->segment($main_array , $ticket_id ) ;		
			
				echo"<br>****************** TAX details ****************************<br>";
				$this->air_ticket_tax($main_array , $ticket_id ) ;
				
			}

//------------------------ if INFANT insurance				
			elseif($ticket_type_string == 'INF_Insurance')
			{
				echo"NOT neeeded TKT , so skip this file";
				//-----------first delete file from source-----------------
				if($delete = unlink($fullpath) == true )//returns TRUE on success
				echo "that file is deleted " . $file . "<br>"; 
				else
				echo "that file NOT deleted " . $file . "<br>";
				continue;
			}
			
			echo"<br><br>*************************************************************************    NXT File    *********************************************************************************<br><br>";
			
			
			//-----------delete file-----------------
			if($delete = unlink($fullpath) == true )//returns TRUE on success
			echo "that file is deleted " . $file . "<br>"; 
			else
			echo "that file NOT deleted " . $file . "<br>";
			
			//---------------------------------------
			$failure_file = "<br>Success file = $file<br>";
			$this->bi_log->logthis($failure_file,1);
		

		}//end foreach
		
		echo "<br> END OF PROCESS - no files found <br>";
	    
	    closedir($dh); 

		echo "Ending Time : ". date("h:i:sa") . "<br> ";
	
	}// end if is_dir()
	
	$this->logout_auto();
	
	logout_now : 
	{
		echo "<br> no files found - so logout <br>";
		$this->logout_auto();
	}
}

//_______________________________________________________________________________________________________
public function xmain_process()
{
	echo "Starting Time : ". date("h:i:sa") . "<br> ";
	
	$this_item = & $this->bi_air_ticket;      
 	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
//-------------------------------- to LOGIN AUTO use first line--------------------------
//------------- remember to Uncomment the LOGOUT AUTO line in the end of function -------------------

	if (!$this->_top_function($access_component_name,$this->input->get("SecretKey"))) return ;
	//if (!$this->_top_function($access_component_name)) return ; 

//---------------------------------------------------------------------
	
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  
			
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		$data["public_data"] = $this->admin_public->DATA;
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);		// takes care of login / header loading
		return false; 
	}
	
	//---------------------------------------------
	// Open a directory, and read its contents	
	$dir = $this->config->item("air_files_path");//$dir = "D:\\process tkt\\airline\\Air_back\\new\\";
	$dest = $this->config->item("air_backup_path");//$dest = "D:\\process tkt\\airline\\copy2\\";
	$errors_dest = $this->config->item("errors_path");//$errors_dest = "D:\\process tkt\\airline\\errors_folder\\";
	
	$count = 1 ;//consider first two files( . / .. )
	
	$failure_file ;//string to check if failure file or success
	$this->bi_log->start();
	
	
	if ( (is_dir($dir)) && ($dh = opendir($dir)) )
	{

		//process 80 files 
	 	while (	($count<=102)	&&	(($file = readdir($dh)) !== false)	)//The readdir() returns the name of the next entry in a directory.
	    {

			$this->bi_log->logthis("the counters is ".$count , 1);
				
			//max 20 loop ;
	    	$fullpath = $dir. $file ;
		
			if($file == "." || $file =="..")
			{}
			else
			{

				//------------------ Begin of Process ------------------------------
		
				$main_array = array();//clear main_array every iterate

				//---------GET CONTENT------
		    	$file_content = file_get_contents( $fullpath );
			    $main_array = explode("\n", $file_content);
			  	echo "the content of filename: " . $file . "<br>";
			    //echo "<pre>";
			    //print_r($main_array) ;
				
//--at first -- check tkt type --------------------

				echo"<br>****************** Ticket Main Info **********************<br>";
				$ticket_type_string = $this->check_type_tkt($main_array);
				
				//------------ if not returning string , there's an error
				//----------- and skip this failure file 
				if($ticket_type_string == false)
				{
					echo"<br>ERROR: Can't Detect Ticket Type<br>";
					$failure_file = "<br>NOT defined file-type = $file<br>";
					$this->bi_log->logthis($failure_file,1);
					
					//check filename if not exist before copying process
					//if exist , don't copy it but start process directly
					if (file_exists($errors_dest.$file))
					{
						echo"the file ".$file." is already exist in errors folder<br>";
						
					}
					else
					{
						//----------COPY file to the third folder (errors_folder)------------				
						$new_path = $errors_dest.$file;
						copy ($fullpath,$new_path); 
						echo "<br>the file ".$file." is copied to errors folder<br>";
					}	
					//-----------delete file-----------------
					if($delete = unlink($fullpath) == true )//returns TRUE on success
					echo "that file is deleted from errors folder = $file <br>"; 
					else
					echo "that file NOT deleted from errors folder = $file<br>";					
					//---------------------------------------
					echo"<br><br>*************************************************************************    NXT File    *********************************************************************************<br><br>";
					continue;
				}
								
				else
				{
					//check filename if not exist before copying process
					//if exist , don't copy it but start process directly
					if (file_exists($dest.$file))
					{
						echo"the file ".$file." is already exist <br>";
						
					}
					else
					{
						//----------COPY file------------
					
						$new_path = $dest.$file;
						copy ($fullpath,$new_path); 
						echo "<br>the file ".$file." is copied <br>";
					}
				}

//------------------------ if VOID				
				if( $ticket_type_string == 'VOID')
				{
					echo"<br>****************** for VOID tkt ************************<br>";
					$total_fare_and_id_array = $this->void_main_tkt_info($main_array,$file) ;//void tkt

					echo"<br>****************** Passengers VOID INFO ***********************<br>";
					$ticket_id = $this->pass_name_tkt_number($main_array ,$total_fare_and_id_array , $ticket_type_string) ;//will call pass_name()
					
					//---call function to get the ORIGINAL_TKT of void tkt-------
					//$this_item->void_id($ticket_id);
					
				}

//------------------------ if Refund
				
				elseif($ticket_type_string == 'Refund')
				{
					echo"<br>****************** and Main tkt info and fare refund calculation ************************<br>";
					$total_fare_and_id_array = $this->main_tkt_info($main_array , $ticket_type_string , $file) ;//refund tkt
										
					echo"<br>****************** Passengers Refund INFO ***********************<br>";
					$ticket_id = $this->pass_name_tkt_number($main_array , $total_fare_and_id_array ,$ticket_type_string) ;
					
					echo"<br>****************** TAX details ****************************<br>";
					$this->air_ticket_tax($main_array , $ticket_id ) ;
					
					//---call function to get the ORIGINAL_TKT of refund tkt-------
					//$this_item->refund_id($ticket_id);
					
					
					//echo"refund tkt from main process";
				}

//------------------------ if Reissue
								
				elseif($ticket_type_string == 'Reissue')
				{
					echo"<br>****************** and Main tkt info and fare reissue calculation ************************<br>";
					$total_fare_and_id_array = $this->main_tkt_info($main_array , $ticket_type_string , $file) ;//reissue tkt

					echo"<br>****************** Passengers Reissue INFO ***********************<br>";
					$ticket_id = $this->pass_name_tkt_number($main_array , $total_fare_and_id_array ,$ticket_type_string) ;
					
					echo"<br>****************** SEGMENTS ****************************<br>";
					$this->segment($main_array , $ticket_id ) ;	
					
					//---call function to get the ORIGINAL_TKT of reissue tkt-------
					//$this_item->reissue_id($ticket_id);

				}
				
//------------------------ if Insurance
								
				elseif($ticket_type_string == 'Insurance')
				{
					echo"<br>****************** Insurance main tkt info from main process ***********************<br>";
					$total_fare_and_id_array = $this->main_tkt_info($main_array , $ticket_type_string , $file) ;
					
					echo"<br>****************** Passengers insurance INFO ***********************<br>";
					$ticket_id = $this->pass_name_tkt_number($main_array , $total_fare_and_id_array ,$ticket_type_string) ;
					
					echo"<br>****************** SEGMENTS ****************************<br>";
					$this->segment($main_array , $ticket_id ) ;	
									
				}

//------------------------ if Active
				
				elseif($ticket_type_string == 'Active')
				{
					
					echo"<br>****************** and fare ACTIVE calculation ************************<br>";
					$total_fare_and_id_array = $this->main_tkt_info($main_array , $ticket_type_string , $file) ;//non-void tkt

					echo"<br>****************** Passengers ACTIVE INFO ***********************<br>";
					$ticket_id = $this->pass_name_tkt_number($main_array , $total_fare_and_id_array ,$ticket_type_string) ;
					
					echo"<br>****************** SEGMENTS ****************************<br>";
					$this->segment($main_array , $ticket_id ) ;		
				
					echo"<br>****************** TAX details ****************************<br>";
					$this->air_ticket_tax($main_array , $ticket_id ) ;
					
				}

//------------------------ if INFANT insurance				
				elseif($ticket_type_string == 'INF_Insurance')
				{
					echo"NOT neeeded TKT , so skip this file";
					//-----------first delete file from source-----------------
					if($delete = unlink($fullpath) == true )//returns TRUE on success
					echo "that file is deleted " . $file . "<br>"; 
					else
					echo "that file NOT deleted " . $file . "<br>";
					continue;
				}
				
				echo"<br><br>*************************************************************************    NXT File    *********************************************************************************<br><br>";
				
				
				//-----------delete file-----------------
				if($delete = unlink($fullpath) == true )//returns TRUE on success
				echo "that file is deleted " . $file . "<br>"; 
				else
				echo "that file NOT deleted " . $file . "<br>";
				
				//---------------------------------------
				$failure_file = "<br>Success file = $file<br>";
				$this->bi_log->logthis($failure_file,1);
			}
			$count++;
		
		}//end while //finishing all files in the source folder

		echo "<br> END OF PROCESS - no files found <br>";
	    closedir($dh); 
	  	//}//end if opendir()

		echo "Ending Time : ". date("h:i:sa") . "<br> ";
	
	}// end if is_dir()
	
	$this->logout_auto(); 
}

//_______________________________________________________________________

//public function check_type_tkt()//to run this function only without main_process
public function check_type_tkt($new_arr)
{	
	$this_item = & $this->bi_air_ticket;
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  
			
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		$data["public_data"] = $this->admin_public->DATA;
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);		// takes care of login / header loading
		return false; 
	}
	
//--------------------to test this function only -----------------------------
//-------------------- uncomment those lines---------------------------------
	//$new_arr = $this->ticket_read();//to read specific file and return file content in array
	//echo"<pre>";
	//print_r($new_arr);
//---------------------------------------------------------------------------
	
	
//************************************ Declerations ************************************
	$tkt_type = "";	
	
	$insurance_arr = array();//---------------- to check if tkt has U- or not------------
	
	$errors_arr = array();//------------------- array to get Mandatory Lines that must be in any file
						//-------------------- if error_arr empty has element empty that's mean there's an error
	
//************************************ Start to Resolve the File **********************************
	foreach($new_arr as $row)
	{
		
		$string_amd = substr($row,0,3);
		
		$check_muc = substr($row,0,3);
		$check_tkt_number = substr($row,0,2);
		$check_icw = substr($row,0,3);
		$check_tmc = substr($row,0,3);
		$check_passenger_name = substr($row,0,2);
		
		$void_string = substr($row,19,14);
		$refund_string = substr($row,19,14);
		
		$string_emd = substr($row,0,3);
		
		$string_tkt_number = substr($row,0,2);
		$tkt_number = substr($row,2);
	
		//---------------- to check if tkt has U- or not
		$insurance_str = substr($row, 0 , 2);
		
		if($insurance_str == 'U-')
		{
			$insurance_arr[] = $insurance_str;
		}
		
		//---------------------------Errors Array-------------------------
		if($string_amd == 'AMD')
		{
			$errors_arr["AMD_found"] = "AMD is existed";
		}
		if($check_muc == 'MUC')
		{
			$errors_arr["MUC_found"] = "MUC is existed";
		}
		if($check_passenger_name == 'I-')
		{		
			$errors_arr["pass_name_found"] = "pass_name is existed";
		}
		if(($check_tkt_number == 'T-') || ($check_icw == 'ICW') || ($check_tmc == 'TMC'))
		{
			$errors_arr["tkt_number_found"] = "tkt_number is existed";
		}


		//---------------------------------- VOID or Refund tkt-----------------------
		// if (AMD and VOID) or (AMD and refund) exist
		if(($string_amd == 'AMD') && ((strpos($void_string,'VOID') !==false) || (strpos($refund_string,';')!==false)))
		{
			$amd_number = substr($row ,4, 10);//10 digit
			
			if(substr($void_string,0,4) == 'VOID')
			{
				$tkt_type = "VOID";
				echo"<br>FILE TYPE ---->".$tkt_type."<br>";
				return $tkt_type;
				
			}
			
			if(substr($refund_string,0,4) == '    ')//4 spaces 
			{
				$tkt_type = "Refund";
				echo"<br>FILE TYPE --->".$tkt_type."<br>";
				return $tkt_type;
			}
			
			break;	
		}
	
		//---------------------------------- Reissue tkt-----------------------
		//---------------------------------- if has K-R -----------------------
		
		elseif( ($string_emd == 'K-R'))
		{
			$tkt_type = "Reissue";
			echo"<br>FILE TYPE ---->".$tkt_type."<br>";
			return $tkt_type;
		}
	
		//---------------------------------- Insurance tkt-----------------------
		//----------------------- if contain T- without tkt_number ----------
		
		elseif( ($string_tkt_number == 'T-') && (strlen($tkt_number) < 3))//if there T- without tkt number
		{
			
			if(empty($insurance_arr))
			{
				echo"the file hasn't U- of insurance <br><pre>";
				print_r($insurance_arr);
				$tkt_type = "INF_Insurance";
				echo"<br>Infant Insurance $tkt_type <br>";
				return $tkt_type;
			}
			else
			{
				$tkt_type = "Insurance";
				echo"<br>FILE TYPE ---->".$tkt_type."<br>";
				return $tkt_type;
			}

		}
		
	
	}//end foreach
	
	//---------------------- if $tkt_type_error_arr empty thats mean 
	//1- there's error in AMD or AMD not found
	//2- can't find passenger name
	//3- can't find tkt number 

	//echo">>>>>> errors array <br><pre>";
	//print_r($errors_arr);
	//return;
	
	if((empty($errors_arr["AMD_found"])) || (empty($errors_arr["MUC_found"])) || (empty($errors_arr["pass_name_found"])) || (empty($errors_arr["tkt_number_found"])))
	{
		echo"<br>$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$ there's an error in resolving file $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$<br>";
		return false;
	}	
	else
	{
		$tkt_type = "Active";
		echo "<br> so the tkt type is active >>>>>>>>".$tkt_type." <br>";
		return $tkt_type;
	}	
}
//_______________________________________________________________________
//public function void_main_tkt_info()//to run this function only without main_process
public function void_main_tkt_info($new_arr , $amd_file_name)
{
	// AMD - voided by/date - IATA number
		
	$this_item = & $this->bi_air_ticket;
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  
			
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		$data["public_data"] = $this->admin_public->DATA;
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);		// takes care of login / header loading
		return false; 
	}
	
//--------------------to run this function only -----------------------------
//-------------------- uncomment those lines---------------------------------
	//$new_arr = $this->ticket_read();//to read specific file and return file content in array
	//echo"<pre>";
	//print_r($new_arr);
//---------------------------------------------------------------------------

//************************************ Declerations ************************************	

	$amd_arr = array();
	$tkt_type = "VOID";
	$IATA_number = "";
	
	
 	foreach ($new_arr as $row) 
 	{

		$string_amd = substr($row,0,3);			
				
		if($string_amd == 'AMD')       
		{		
			$full_data = substr($row,4);
			
			$amd_number = substr($full_data,0,10);
			
			$void_string = substr($row,19,14);
			
			//-----to get date in date format ---- but i havn't the year so it'll take current year
			$void_date_str = strtok(substr($void_string,4) , ";");
			$strtotime_void_date = strtotime($void_date_str);
            $void_date = date('Y-m-d',$strtotime_void_date);

			$void_by = substr($void_string, 10 , 2);
				
			$day_of_month = substr($full_data,0,2);//it's the day of AIR creation 
			
			//$amd_arr[] = array("amd_number"=>$amd_number , "day_of_month"=>$day_of_month);
			$amd_arr = array("amd_number"=>$amd_number,
							"check_void"=>$tkt_type,
							"void_by"=>$void_by,
							"void_date"=>$void_date,);
		
		}
		
		//-------------------------------------------------------------
		
		if(substr($row,0,3) == 'MUC')//IATA number of booking agancy(Sales Branch)
		{
			$full_data = substr($row,6);
			//$IATA_number = strtok($full_data , "90201403");
			
			if(strpos($full_data, '90201403') !== false)//returns "true";
			{
				//echo"IATA number >>>>>>>>> 90201403";
				$IATA_number = "90201403";
				
			}
			elseif(strpos($full_data, '90205360') !== false)
			{				
				//echo"IATA number >>>>>>>>> 90205360";
				$IATA_number = "90205360";
				
			}

		}
		
		
	 }//end foreach
	
	echo"<br>>>>>> the main void info <pre>";
	print_r($amd_arr);
	
	//---------------INSERT--------------------------
	$this_item->clear() ;
	$this_item->business_data["AMD_FILE_NAME"] = $amd_file_name;
	$this_item->business_data["TKT_AMD_NUMBER"] = $amd_arr["amd_number"];
	$this_item->business_data["TKT_VOID_BY"] = $amd_arr["void_by"];
	$this_item->business_data["DATE_AIR_CREATION"] = $amd_arr["void_date"];
	$this_item->business_data["BOOK_AGNCY_IATA_NUMBER"] = $IATA_number;
	$this_item->business_data["TKT_TYPE"] = $tkt_type;

	$this_item->update();		
	//-----------------------------------------	
	
	$id = $this_item->ID();
	
	$total_fare_array = array("base_fare"=>"" , 
							"total_price"=>"",
							"total_tax"=>"",
							"id"=>$id);			
	return $total_fare_array;
}

//_______________________________________________________________________

//public function pass_name_tkt_number()//to run this function only without main_process
public function pass_name_tkt_number($new_arr , $total_fare_tkt_info ,$tkt_type)
{
	$this_item = & $this->bi_air_passenger;
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  
			
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		$data["public_data"] = $this->admin_public->DATA;
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);		// takes care of login / header loading
		return false; 
	}
	
//--------------------to run this function only -----------------------------
//-------------------- uncomment those lines---------------------------------
	//$new_arr = $this->ticket_read();//to read specific file and return file content in array
	//echo"<pre>";
	//print_r($new_arr);
	
	//$tkt_type="Insurance"; //to test the function with specific tkt_type
//---------------------------------------------------------------------------
	

	
//************************************ Declerations ************************************	
	$group_passenger_flag = 0 ;//set it =1 if passenger is GROUP
	$insurance_flag = 0 ;
	
	$tkt_number = array();
	/*
	$emd_array = array("checker"=>"" , "emd_amount"=>"");
	$icw_array = array("checker"=>"" , "icw_tkt_number"=>"");
	$tmc_array = array("checker"=>"" , "tmc_tkt_number"=>"");
	*/
	
	$tmc_array = array();
	$emd_array = array();
	$icw_array = array();

//************************************ Start to Resolve the File **********************************
	foreach ($new_arr as $row) 
	{	
    	$string_tkt_number = substr($row,0,2);
		$string_passenger = substr($row,0,2);
		$group_string = substr($row , 2 , 3);
		$string_tkt_number = substr($row,0,2);
		//$str_emd_number = substr($row,0,3);
		
		$insurance_string = substr($row,0,2);
		$not_valid_insurance_string = substr($row,0,3);
		
		
		$emd_string = substr($row,0,3);
		$tmc_string = substr($row,0,3);
		$icw_string = substr($row,0,3);

		//------ if the passenger is GROUP , skip it 
		if($string_passenger == 'I-' && $group_string == 'GRP' )
		{
			$group_passenger_flag = 1 ;
			continue;			
		}
		
		//--------- PASSENGER NAME-----------------
		if($string_passenger == 'I-')       
		{
			$full_data = substr($row,8);
			$full_name_str = strtok($full_data , ";");
			$age_group_str = substr($full_name_str,-5);
	
			//$last_name = strtok($full_data , "/");
			$last_name = ltrim(rtrim(strtok($full_data , "/")," ")," ");
			
			$agent_info = substr($full_data, strpos($full_data, ";;") + 4);
			
			//******** Age Group / Pax Name ***************
			
			if($age_group_str == "(ADT)" || $age_group_str == "(INF)" || $age_group_str == "(CHD)")
			{
				//to remove Age-Group from the Pax-Name
				$full_name = substr($full_name_str, 0, -5);
				$pax_name[] = array("pax_name"=>$full_name);
				
				//to remove the Age-Group from First_Name
				$first_name = ltrim(rtrim(substr($full_name, (strpos($full_name, "/") + 1))," ")," ");
				
				$age_group[] = array("age_group"=>$age_group_str);	
				
				//echo "age_group_str = ".$age_group_str."<br>";
			}
			else 
			{
				//$first_name = substr($full_name, strpos($full_name, "/") + 1);
				$first_name = ltrim(rtrim(substr($full_name_str, (strpos($full_name_str, "/") + 1))," ")," ");
				
				if($group_passenger_flag == 1 )
				{
					$age_group[] = array("age_group"=>"GROUP");
					$pax_name[] = array("pax_name"=>$full_name_str);
				}
				else
				{
					$age_group[] = array("age_group"=>"ADT");
					$pax_name[] = array("pax_name"=>$full_name_str);
				}
			}
			
			$name[] =  array("first name"=>$first_name,"last name"=>$last_name);

		}
	
	
	
		//-----------------------tkt number and conj number--------------------
		//---------------------- if file is INSURANCE -----------------------
		//--------------- it hasn't EMD penalty -----------
		//--------------- it hasn't conj-number and has its own NET & TAX -----------
		if($tkt_type=="Insurance")	
		{		
			//------------------------------insurance number----------------------		
			if(($insurance_string == 'U-') && (strpos($row , "INS;") !== false))       
			{
				if(strpos($row , "CF-") === false) //if CF- not exist
				goto line_insurance_AE;//becasue U- line has carriage return , so insurance data exist in AE- line
				
				$full_data = substr($row , strpos($row , "CF-"));
				echo"<br>  full_data = ".$full_data."<br>";
				
				//------------------Insurance Number-----------------------------------------------------
				$insurance_number_full_string = substr($full_data, 3 , 8);
				//echo"<br>  insurance_number_full_string = ".$insurance_number_full_string."<br>";
				
				$insurance_code = substr($insurance_number_full_string, 0 , 2);
				//echo"<br>  insurance_code = ".$insurance_code."<br>";
				
				$insurance_no = "00".substr($insurance_number_full_string , 2);
				//echo"<br>  insurance_number = ".$insurance_no."<br>";
				
				$full_insurance_number = $insurance_code.$insurance_no;
				echo"<br>  full_insurance_number = ".$full_insurance_number."<br>";
				
				$tkt_conj_number = "/";
		
				//------------------ Net - Tax-----------------------------------------------------
				
				$str_to_get_net_and_tax = substr($row, (strpos($row , "XR-")) );
				
				$all_fare_array = explode(";", $str_to_get_net_and_tax);
				
				$insurance_net_value = $all_fare_array[1];
				$ins_net_value = substr($insurance_net_value , 3);
				
				$insurance_total_tax = $all_fare_array[4];
				$ins_total_tax = substr($insurance_total_tax , 6);
				
				$insurance_total_amount = $all_fare_array[7];
				$ins_total_amount = substr($insurance_total_amount , 3);
				
				//-----------------------------------------------
				
				$tkt_number_price[] = array("tkt_number"=>$full_insurance_number , 
											"conj_number"=>$tkt_conj_number,
											"ins_net"=>$ins_net_value , 
											"ins_total_tax"=>$ins_total_tax,
											"ins_total_amount"=>$ins_total_amount);
			}
			
			line_insurance_AE:
			{
				//------------------------------insurance number----------------------		
				if($not_valid_insurance_string == 'AE-')      
				{
					$full_data = substr($row , strpos($row , "CF-"));
					echo"<br>  full_data = ".$full_data."<br>";
					
					//------------------Insurance Number-----------------------------------------------------
					$insurance_number_full_string = substr($full_data, 3 , 8);
					//echo"<br>  insurance_number_full_string = ".$insurance_number_full_string."<br>";
					
					$insurance_code = substr($insurance_number_full_string, 0 , 2);
					//echo"<br>  insurance_code = ".$insurance_code."<br>";
					
					$insurance_no = "00".substr($insurance_number_full_string , 2);
					//echo"<br>  insurance_number = ".$insurance_no."<br>";
					
					$full_insurance_number = $insurance_code.$insurance_no;
					echo"<br>  full_insurance_number = ".$full_insurance_number."<br>";
					
					$tkt_conj_number = "/";
									
					//------------------ Net - Tax-----------------------------------------------------
					
					$str_to_get_net_and_tax = substr($row, (strpos($row , "XR-")) );
					
					$all_fare_array = explode(";", $str_to_get_net_and_tax);
					
					$insurance_net_value = $all_fare_array[1];
					$ins_net_value = substr($insurance_net_value , 3);
					
					$insurance_total_tax = $all_fare_array[4];
					$ins_total_tax = substr($insurance_total_tax , 6);
					
					$insurance_total_amount = $all_fare_array[7];
					$ins_total_amount = substr($insurance_total_amount , 3);
					
					//-----------------------------------------------
					
					$tkt_number_price[] = array("tkt_number"=>$full_insurance_number , 
												"conj_number"=>$tkt_conj_number,
												"ins_net"=>$ins_net_value , 
												"ins_total_tax"=>$ins_total_tax,
												"ins_total_amount"=>$ins_total_amount);
				}
			}//end goto
				
		}//end if tkt_type = insurance
		
		//----------------------- check if the file has Penalty (غرامة) ----------------------
		else
		{
			if($emd_string == 'EMD')
			{
				$emd_checker = strtok((substr($row, strpos($row, ";D") + 1)) , ";");    
				
				$to_get_emd_amount = substr($row, strpos($row, "CV-") + 3);
				$emd_amount = str_replace(' ', '', (substr($to_get_emd_amount, (strpos($to_get_emd_amount, "EGP") + 3) , 11)));
			
				$emd_array[] = array("checker"=>$emd_checker , "emd_amount"=>$emd_amount);
			}
			
			if($icw_string == 'ICW')
			{
				$icw_checker = strtok((substr($row, strpos($row, ";D") + 1)) , ";");     

				$icw_tkt_number = substr($row,6,10); //10 digit

				$icw_array[] = array("checker"=>$icw_checker , "icw_tkt_number"=>$icw_tkt_number);
			}
			
			if($tmc_string == 'TMC')
			{
				$tmc_checker = substr($row, strpos($row, ";D") + 1);    

				$tmc_tkt_number = substr($row,8,10); //10 digit
				$tmc_array[] = array("checker"=>$tmc_checker , "tmc_tkt_number"=>$tmc_tkt_number);
			}
			
			//-----------------------tkt number and conj number--------------------
			
			if($string_tkt_number == 'T-')       
			{
				$ticket_number = substr($row,7,10); //10 digit
				
				$str_from_tkt_no_to_end = substr($row,7);
				$string_conj = strpos($str_from_tkt_no_to_end , "-");
				
				if($string_conj !== false)//if "-" exists , there's conj segment
				{
					$tkt_conj_number = substr($str_from_tkt_no_to_end, ($string_conj+1) , 2);
					//$conj_arr[] = $tkt_conj_number;
					$tkt_number[] = array("tkt_number"=>$ticket_number , "conj_number"=>$tkt_conj_number);
					//echo"conj nuuuumer = ".$tkt_conj_number."<br>";
				}
				else
				{	
					$tkt_conj_number = "/";
					//$conj_arr[] = array("conj_tkt_number"=>$tkt_conj_number);
					//echo"no conj nuumber";
					$tkt_number[] = array("tkt_number"=>$ticket_number , "conj_number"=>$tkt_conj_number);
				}	
			}
		}
		

	}//end foreach
	
		//return;
		//-----------------------in case EMD----------------------------
		//------------ if tkt-number is empty or the file hasn't T-
		//add an empty array into it accourding to number of passengers , to prepare it to merge with other arrays
		if(empty($tkt_number))
		{
			foreach ($name as $key => $value) 
			{
				$tkt_number[$key] = array("tkt_number"=>"/" , "conj_number"=>"/");
			}
			
		}
		
		//------------------check if (tmc/emd/icw) are empty -----------------
		if(empty($tmc_array) && empty($icw_array) && empty($emd_array))
		{
			foreach ($tkt_number as $key => $value) 
			{
				$tmc_array[$key] = array("checker"=>"/" , "tmc_tkt_number"=>"0");
				$emd_array[$key] = array("checker"=>"/" , "emd_amount"=>"0");
				$icw_array[$key] = array("checker"=>"/" , "icw_tkt_number"=>"0");
				
				$all_tkt_number_data[$key] = array_merge($tkt_number[$key] , 
														$tmc_array[$key],
														$emd_array[$key],
														$icw_array[$key] );
			}
			
		}
		
		
		//-----------to merge tkt-number with emd-number , emd-amount--------
		
		//------------- VOID case (maybe has only tmc without tkt-number)-------------
		elseif(empty($icw_array) && empty($emd_array))
		{

			foreach ($tkt_number as $key => $value) 
			{
				$emd_array[$key] = array("checker"=>"/" , "emd_amount"=>"0");
				$icw_array[$key] = array("checker"=>"/" , "icw_tkt_number"=>"0");

				$all_tkt_number_data[$key] = array_merge($tkt_number[$key] , 
														$tmc_array[$key],
														$emd_array[$key],
														$icw_array[$key] );
			}
		}
		
		//------------- I-GRP case (has tmc and emd without tkt-number)-------------
		elseif(empty($icw_array))
		{
			foreach ($tkt_number as $key => $value) 
			{
				$icw_array[$key] = array("checker"=>"/" , "icw_tkt_number"=>"0");
				$all_tkt_number_data[$key] = array_merge($tkt_number[$key] , 
														$tmc_array[$key],
														$emd_array[$key],
														$icw_array[$key] );
			}
		}
		
		
		//----------------- Normal tkt WITH all data of emd/tmc/icw
		elseif((!empty($tkt_number)) && (!empty($tmc_array)) && (!empty($emd_array)) && (!empty($icw_array)))
		{

			foreach ($tkt_number as $key => $value) 
			{
				//------ if no tkt-number put icw-number instead	
				if(empty($tkt_number[$key]["tkt_number"]) || ($tkt_number[$key]["tkt_number"] == "/"))
				{
					$tkt_number[$key]["tkt_number"] = $icw_array[$key]["icw_tkt_number"];
				}
				//------ case VOID if no tkt-number and no icw-number ,
				//------ put tmc_number instead	
				elseif((empty($tkt_number[$key]["tkt_number"]) || ($tkt_number[$key]["tkt_number"] == "/")) && ($icw_array[$key]["icw_tkt_number"] =="0" || empty($icw_array[$key]["icw_tkt_number"])))
				{
					
						$tkt_number[$key]["tkt_number"] = $tmc_array[$key]["tmc_tkt_number"];
		
				}
				$all_tkt_number_data[$key] = array_merge($tkt_number[$key] , 
														$tmc_array[$key] ,
														$emd_array[$key] ,
														$icw_array[$key]
														);
			}
		}
		
		//------ case VOID if no tkt-number and no icw-number ,
		//------ put tmc_number instead	
		foreach ($tkt_number as $key => $value) 
		{
			if((empty($tkt_number[$key]["tkt_number"]) || ($tkt_number[$key]["tkt_number"] == "/")) && ($icw_array[$key]["icw_tkt_number"] =="0" || empty($icw_array[$key]["icw_tkt_number"])))
			{
				$tkt_number[$key]["tkt_number"] = $tmc_array[$key]["tmc_tkt_number"];
				$all_tkt_number_data[$key] = array_merge($tkt_number[$key] , 
														$tmc_array[$key] ,
														$emd_array[$key] ,
														$icw_array[$key]
														);
			}
			
			
		}
	
	
		/*echo">>>>>> after if condition >>>>>>>>>>>>>>> <br>";
		echo">>>>>> emd_array<br><pre>";
		print_r( $emd_array);

		echo">>>>>> icw_array<br><pre>";
		print_r($icw_array);
		echo">>>>>> tmc_array<br><pre>";
		print_r($tmc_array);
		
		echo">>>>>> tkt_number<br><pre>";
		print_r($tkt_number);
		return;
		echo">>>>>>  all_tkt_number_data<br><pre>";
		print_r($all_tkt_number_data);
		return;
	*/
	//----------------------to merge each passenger with his tkt-number--------------------------------
	if($tkt_type == "Insurance")
	{
		//----------------merge name and pax-name and age-group-----------
		foreach($name as $key => $value)
		{
			$full_name_data[$key] = array_merge($name[$key], 
										$pax_name[$key] , 
										$age_group[$key]);
		}
		//------------------------- to check if ther's an insurance INFANT passenger-------
		//------------------------- ignore it from saving in db-----------------------
		foreach($full_name_data as $key=>$value)
		{
			//---------START BLOCK 
			//--(to get the index of the INFANT passenger bcoz it hasn't Price or Ins number)-----
			
			if($value["age_group"] == "(INF)")
			{
				$index_of_INF = $key;
				echo"the key of infant = ".$index_of_INF;
				
				//call function to get the U- array to merge with name array
				$new_price_array = $this->insert_elemnt_in_arr($tkt_number_price , $index_of_INF);
			 
			 	//------to insert the new element of zeros in price array-------
			 	foreach($new_price_array as $key => $value)
				{				
					//to get the key of the Zeros element and update data with Zeros
					foreach($value as $key_2)
					{
						if($key_2 == "0")
						{
							$new_price_array[$key] = array("tkt_number"=>"0" , 
													"conj_number"=>"/",
													"ins_net"=>"0" , 
													"ins_total_tax"=>"0",
													"ins_total_amount"=>"0");		
						}
						
					}
			
				}
				
				$result[$key] = array_merge($full_name_data[$key],
											$new_price_array[$key]);			
			}//end if
			
			//END of BLOCK---------------------------------------------------------------

			else
			{
				$result[$key] = array_merge($full_name_data[$key],
											$tkt_number_price[$key]);	
			}
				
		}//end foreach
	}
	
	//------any tkt type else 
	else
	{
		foreach($name as $key => $value)
		{
    		$result[$key] = array_merge($name[$key], 
    								$all_tkt_number_data[$key] , 
    								$pax_name[$key] , 
    								$age_group[$key]);
		}
	}
			
	//---------------------- check that VOID file if contains more than one passenger-------
	//---------------------- take the last one----------------------------
	
	if($tkt_type == "VOID" && (count($result) > 1))
	{
		$temp_result_arr = $result;
		$result = array();
		$result[] = end($temp_result_arr);		
	}
	
 //-------------------insert in database--------------------------
	
	foreach($result as $key => $value)
	{
		$this_item->clear() ;
		
		if($tkt_type=="Insurance")
		{
			$this_item->business_data["PASSENGER_TKT_BASE_FARE"] = $value["ins_net"];
			$this_item->business_data["PASSENGER_TKT_TOTAL_TAX"] = $value["ins_total_tax"];
			$this_item->business_data["PASSENGER_TKT_TOTAL_PRICE"] = $value["ins_total_amount"];
			$this_item->business_data["PASSENGER_TKT_NUMBER"] = $value["tkt_number"];
		}
		else
		{
			if($group_passenger_flag == 1)//if I-GRP -> put emd-amount into base-fare 
			{
				$total_fare_tkt_info["base_fare"] = $value["emd_amount"];
				
			}	
			$this_item->business_data["PASSENGER_TKT_BASE_FARE"] = $total_fare_tkt_info["base_fare"];
			$this_item->business_data["PASSENGER_TKT_TOTAL_TAX"] = $total_fare_tkt_info["total_tax"];
			$this_item->business_data["PASSENGER_TKT_TOTAL_PRICE"] = $total_fare_tkt_info["total_price"];
			$this_item->business_data["PASSENGER_TKT_NUMBER"] = $value["tkt_number"];
			
			//becasu insurance hasn't emd 
			$this_item->business_data["EMD_AMOUNT"] = $value["emd_amount"];
			$this_item->business_data["EMD_NUMBER"] = $value["tmc_tkt_number"];
		}

		$this_item->business_data["PASSENGER_TKT_ID"] = $total_fare_tkt_info["id"];
		$this_item->business_data["PASSENGER_FIRST_NAME"] = $value["first name"];
		$this_item->business_data["PASSENGER_LAST_NAME"] = $value["last name"];
		
		$this_item->business_data["PASSENGER_PAX_NAME"] = $value["pax_name"];
		$this_item->business_data["PASSENGER_AGE_GROUP"] = $value["age_group"];
		$this_item->business_data["CONJ_NUMBER"] = $value["conj_number"];
		
		$this_item->update();
	}
	
 //---------------------------------------------------------------

	echo"<br>>>>> agent_info is : <br>";
	echo $agent_info ;
	echo"<br>>>>>> all Passengers Info : <br>";
	echo"<pre>";
	print_r($result);
	 
	//echo"<pre>";
	//print_r($name);
	
	$AMD_file_id = $total_fare_tkt_info["id"];
	echo"the id of AMD file is >>>>>>>>>>>>".$AMD_file_id;
	return $AMD_file_id ; 
}

//_______________________________________________________________________

//public function main_tkt_info()//to run this function only without main_process
public function main_tkt_info($new_arr , $tkt_type , $amd_file_name)
{
	// AMD - ORG/DEST - DATE creation - Airline info - Corporate Code
	// base_fare - total_tax - total price	
	
	$this_item = & $this->bi_air_ticket;
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  
			
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		$data["public_data"] = $this->admin_public->DATA;
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);		// takes care of login / header loading
		return false; 
	}
//--------------------to run this function only -----------------------------
//-------------------- uncomment those lines---------------------------------
	//$new_arr = $this->ticket_read();//to read specific file and return file content in array
	//echo"<pre>";
	//print_r($new_arr);
	//$tkt_type = "Reissue"; //to test the function with specific tkt_type
//---------------------------------------------------------------------------
	
	

//************************************ Declerations ************************************	
	$flag_infant = 0 ;
	
	$airline_info[] = array();
	$price_detail = array("base_fare"=>"" ,"total_price"=>"" ,"total_tax"=>"");
	
	//A-
	$airline_name = "";
	$airline_code = "";
	$airline_numeric_code = "";
	
	//D-
	$date = array("PNR_creation_date"=>"" , 
					"PNR_change_date"=>"" , 
					"AIR_creation_date"=>"");
	//AMD
	$amd_arr = array("amd_number"=>"",
					"check_void"=>"");	
	
	//MUC // IATA number
	$IATA_number="";
					
	//C-
	$pnr_airline_creator_agent = "";
	$airline_ticketing_agent = "";			
	$booking_clerk = "";	
	$executive = "";	

	//G-	
	$org_dest = array("org/dest"=>"");
	$flight_type = "";

	//commission
	$net=array("comm_amount"=>"" , "vat_amount"=>"" , "net_amount"=>"");
	
	//management fees
	$manag_fees_arr = array("manage_fees"=>"");
	$manage_fees_row_arr = array();
	
	//payment method
	$pay_method_arr = array("Payment_Method"=>"" , "Code"=>"");
	
	//Refund
	$refund_data = array("refund_number"=>"","refund_date"=>"","cancellation_fees"=>"");
	
	//Reissue
	$reissue_data = array("original_reissue"=>"" , "original_reissue_conj_number"=>"");
	$tkt_conj_number="";
	$emd_number[] = array("emd_number"=>"");


//************************************ Start to Resolve the File **********************************
		
 	foreach ($new_arr as $row) 
 	{
		$string_amd = substr($row,0,3);			
		$string_org_dest = substr($row,0,2);
		$string_date = substr($row,0,2);
		$string_airline = substr($row,0,2);
				
		$string_price = substr($row,0,2);
		$string_price_2 = substr($row,0,3);
		$string_price_3 = substr($row,0,2);
		
		$string_comm_rate = substr($row,0,2);
		
		$string_manage_fees = substr($row,0,3);
		$string_passenger_number = substr($row,0,2);
		$string_passenger = substr($row,0,2);
		
		$file_string = substr($row,0,7);
		$file_string_2 = substr($row,0,6);
		$card_string = substr($row,0,4);
		$client_string = substr($row,0,5);
		$client_string_2 = substr($row,0,4);
		$payment_method;
		
		$str_price_reissue = substr($row,0,3);
		$str_tax_reissue = substr($row,0,4);
		//$string_price_reissue = substr($row,0,3);
		$total_tax = 0 ;
		$string_reissue = substr($row,0,2);
				
		$string_price_refund = substr($row,0,3);
		$string_refund = substr($row,0,2);
		$string_comm_rate_refund = substr($row,0,2);
		
		$string_price_insurance = substr($row,0,2);
		
		//-----------------------------------------to check if INFANT passenger to get his management fees -----------------------------
		//----------------------------------------- Set the flag into 1 --------------------------
		
		if($string_passenger == 'I-' && (strpos($row, '(INF)') !== false)) // found INF
		{
			$flag_infant = 1 ;
			//return;
		}
		
		//--------------------------------------- Get Normal Data that common at all tkts----------------
		if($string_amd == 'AMD')       
		{		
			$full_data = substr($row,4);
			$amd_number = substr($full_data,0,10);
			
				
			$day_of_month = substr($full_data,0,2);//it's the day of AIR creation 
			
			//$amd_arr[] = array("amd_number"=>$amd_number , "day_of_month"=>$day_of_month);
			$amd_arr = array("amd_number"=>$amd_number,
							"check_void"=>$tkt_type);
		
		}
		
		//-------------------------------------------------------------
		
		if(substr($row,0,3) == 'MUC')//IATA number of booking agancy(Sales Branch)
		{
			$full_data = substr($row,6);
			//$IATA_number = strtok($full_data , "90201403");
			
			if(strpos($full_data, '90201403') !== false)//returns "true";
			{
				//echo"IATA number >>>>>>>>> 90201403";
				$IATA_number = "90201403";
				
			}
			elseif(strpos($full_data, '90205360') !== false)
			{				
				//echo"IATA number >>>>>>>>> 90205360";
				$IATA_number = "90205360";
				
			}

		}
	
		//--------------------AIRline info--------------------
		
		if($string_airline == 'A-') //airline info(airline code - numeric coed - name)      
		{
			$full_data = substr($row,2);
			$airline_name = strtok($full_data , ";");
			//$airline_code = substr($full_data, strpos($full_data, ";")+1 , 3);
			$airline_code = ltrim(rtrim(substr($full_data, strpos($full_data, ";")+1 , 3)," ")," ");
			
			$airline_numeric_code = substr($full_data, strpos($full_data, ";") + 4 , 3);
			
		}
		
		if($string_airline == 'C-')//booking clerk - PNR_creator_agent_sine - ticketing_agent_sine
		{	
			$creatorstr = substr($row,12);
			$ticketingstr = substr($row,21);
			
			$pnr_airline_creator_agent = strtok($creatorstr,'-');//$pnr_creator_agent_sine			
			$airline_ticketing_agent = strtok($ticketingstr,'-');//$ticketing_agent_sine
											
			$booking_clerk = substr($pnr_airline_creator_agent,0,2);			
			$executive = substr($airline_ticketing_agent,0,2);				
		}
			
		if($string_airline == 'G-')//airline type - org/destination 
		{	
			// org - destination 
			$origin_destination = substr(substr($row,7) ,0, 6);
			$origin = substr($origin_destination ,0, 3);
			$destination = substr($origin_destination, 3);
			
			$org_dest = array("org/dest"=>$origin."/".$destination);
			
			//--------flight type---------------
			
			$flight_type = substr($row,2,3);   // to skipe the first two char 'A-' and return the string
			
			if($flight_type == "X  ")
			{	
				$flight_type = "International";
			
			}
			elseif($flight_type == " /S")
			{	
				$flight_type = "Self Sale";	
			
			}
			elseif($flight_type == "   ")
			{	
				$flight_type = "local";
				
			}
			elseif($flight_type == "X/S")
			{	
				$flight_type = "Special Case";
			}  
		
		}
		//-------------------------------------------------
		if($string_date == 'D-')//PNR creation and change DATE - AIR creation DATE //YYMMDD     
		{			
			$PNR_creation =substr($row,2,6);
			$PNR_creation_date = "20" . substr($PNR_creation,0,2)."-". substr($PNR_creation,2,2)."-". substr($PNR_creation,4,2) ;
			$strtotime_PNR = strtotime($PNR_creation_date);
            $date_PNR = date('Y-m-d',$strtotime_PNR);
			$final_date_PNR = orcl_to_date($date_PNR);
            
			$PNR_change = substr($row,9,6);
			$PNR_change_date = "20" . substr($PNR_change,0,2)."-". substr($PNR_change,2,2)."-". substr($PNR_change,4,2) ;
			$strtotime_PNR_change = strtotime($PNR_change_date);
            $date_PNR_change = date('Y-m-d',$strtotime_PNR_change);
			$final_date_PNR_change = orcl_to_date($date_PNR_change);
			
			$AIR_creation = substr($row,16);
			$AIR_creation_date = "20" . substr($AIR_creation,0,2)."-". substr($AIR_creation,2,2)."-". substr($AIR_creation,4,2) ;	
			$strtotime_AIR = strtotime($AIR_creation_date);
            $date_AIR = date('Y-m-d',$strtotime_AIR);
			$final_date_AIR = orcl_to_date($date_AIR);
			
			$date = array("PNR_creation_date"=>$final_date_PNR , 
						"PNR_change_date"=>$final_date_PNR_change , 
						"AIR_creation_date"=>$final_date_AIR);
		
		}
			
	
//------------------------------ fare calculations ----------------------------------			


switch ($tkt_type) 
{
	//-------(Active tkt)Prices (total price - base fare - total tax) ------------------------------
    case "Active":
		
		//to get total price - total tax - base fare        
		//maybe K- is existed without any data or not exist
		if($string_price == 'K-')
		{
			$full_data = substr($row,3);
			$currency = substr($full_data , 0 , 3);
			
			if($currency == 'EGP')	
			{
				$str_base_fare = strtok($full_data , ";");
				$str_total_price = strtok(substr($full_data,26),";");
				//--------------to get total tax----------
				$base_fare = substr($str_base_fare,3);
				$total_price = substr($str_total_price,3);
				$total_tax = $total_price - $base_fare ;
				//$total_tax_arr[] = array("total tax"=>$total_tax);
				//---------------------------------------------------------
				$price_detail = array("base_fare"=>$base_fare , 
									"total_price"=>$total_price , 
									"total_tax"=>$total_tax);
			}
			else
			{	
				$str_base_fare = strtok($full_data , ";");			
				$eq_base_fare = strtok(substr($full_data,15),";");//equivalent amount of base fare			
				$numeric_base_fare = substr($eq_base_fare,3);
				$str_total_price = strtok(substr($full_data,40),";");//14 base fare , 12=simicolon , 14=equivalent amount
				
				//--------------to get total tax (just for check)----------
				$base_fare = substr($eq_base_fare,3);
				$total_price = substr($str_total_price,3);
				$total_tax = $total_price - $base_fare ;
				//$total_tax_arr[] = array("total tax"=>$total_tax);
				//---------------------------------------------------------
				$price_detail = array("base_fare"=>$base_fare , 
									"total_price"=>$total_price , 
									"total_tax"=>$total_tax);
			}
				
		}
		
		if( (empty($price_detail["base_fare"])) && (empty($price_detail["total_price"])) && (($string_price_2 == 'KN-') || ($string_price_3 == 'KS-')))//to get total price - total tax - base fare        
		{
			$full_data = substr($row,4);
			$currency = substr($full_data , 0 , 3);
			
			if($currency == 'EGP')	
			{
				$str_base_fare = strtok($full_data , ";");
				$str_total_price = strtok(substr($full_data,26),";");
				//--------------to get total tax----------
				$base_fare = substr($str_base_fare,3);
				$total_price = substr($str_total_price,3);
				$total_tax = $total_price - $base_fare ;
				//$total_tax_arr[] = array("total tax"=>$total_tax);	
				//---------------------------------------------------------
				$price_detail = array("base_fare"=>$base_fare , 
									"total_price"=>$total_price , 
									"total_tax"=>$total_tax);
			}
			else
			{	
				$str_base_fare = strtok($full_data , ";");			
				$eq_base_fare = strtok(substr($full_data,15),";");//equivalent amount of base fare			
				$numeric_base_fare = substr($eq_base_fare,3);
				$str_total_price = strtok(substr($full_data,40),";");//14 base fare , 12=simicolon , 14=equivalent amount
				
				//--------------to get total tax (just for check)----------
				$base_fare = substr($eq_base_fare,3);
				$total_price = substr($str_total_price,3);
				$total_tax = $total_price - $base_fare ;
				//$total_tax_arr[] = array("total tax"=>$total_tax);
				//---------------------------------------------------------
				$price_detail = array("base_fare"=>$base_fare , 
									"total_price"=>$total_price , 
									"total_tax"=>$total_tax);
			}
				
		}
		
        break;
	//-------------------------------- R-refund_data and (Base fare - total Tax)--------------------------------
    case "Refund":
        if($string_refund == 'R-')       
		{		
			$refund_number = substr($row,6,10);
			
			$refund_date = substr($row,17,7);
			$strtotime_refund_date = strtotime($refund_date);
            $date_refund = date('Y-m-d',$strtotime_refund_date);
			
			$refund_data = array("refund_number"=>$refund_number,
							"refund_date"=>$date_refund,
							"cancellation_fees"=>$cancel_fee);

		}
		
		// in case of Refund-Reissue we will find FO keyword to get original reissue ticket
		if($string_refund == 'FO')       
		{
			$refund_number = substr(str_replace(' ', '', $row),5,10); //clean string from spaces
			
			$refund_data = array("refund_number"=>$refund_number,
							"refund_date"=>$date_refund,
							"cancellation_fees"=>$cancel_fee);

		}
		
		if($string_price_refund == 'RFD')//to get total price , total tax , base fare        
		{
			$full_data = substr($row,5);
			
			$all_fare = substr($row,18);
			$all_fare_array = explode(";", $all_fare);
			
			$fare_paid = $all_fare_array[0];
			
			$fare_refund = $all_fare_array[2];//base fare refund
			
			$cancel_fee = $all_fare_array[5];//cancelation fees refund
			
			$tax_refund = $all_fare_array[8];
			$total_tax_refund = substr($tax_refund , 2);
			
			$total_refund = $all_fare_array[9];
			
			$date_refund = $all_fare_array[10];//Departure Date of the first segment refunded
		
			
			$price_detail = array("base_fare"=>(-$fare_refund) , 
									"total_price"=>(-$total_refund) , 
									"total_tax"=>(-$total_tax_refund),
									"cancellation_fees"=>($cancel_fee),);
			
			$refund_data = array("cancellation_fees"=>$cancel_fee);
				
		}
        break;
      
	//-------------------------------- reissue data and (Base fare - total Tax)--------------------------------
    case "Reissue":
    
    	if($str_price_reissue == 'K-R')//skip 14 take 11
		{
			$full_data = substr($row,3);
			$currency = substr($full_data , 0 , 3);
			
			//K-REGP1479.00    ;;;;;;;;;;;;EGP35.00      ;;; => (35 is total price)
			if($currency == 'EGP')	
			{
				$str_total_price = strtok(substr($full_data,26),";");
				$total_price = substr($str_total_price,3);
				echo" total_price >>>> $total_price <br>";
			}
			
			//K-RSDG1633.00    ;EGP           ;;;;;;;;;;;EGP0.00       ;1.294855   ;; => (EGP0.00 is total price)
			//K-RSAR2578.00    ;EGP           ;;;;;;;;;;;EGP3875.00    ;4.756708   ;;
			//K-RMAD1925.00    ;;;;;;;;;;;;EGP851.00     ;;; => (851 is total price)
			//K-REUR315.00     ;;;;;;;;;;;;EGP565.00     ;8.6476     ;;
			//K-RSAR1185.00    ;;;;;;;;;;;;EGP605.00     ;2.090636   ;;
			else
			{
				
				$total_price = substr($full_data,29,11);//check at line above like EGP851
				if(empty($total_price) || (strpos($total_price, ';') !== false))
				{
					//then get total price from line above like EGP3875 
					$total_price = substr($full_data,43,11);
					
				}
				
				echo" total_price >>>> $total_price <br>";
			}
		}
		if($str_tax_reissue == 'KFTR')
		{		
			$full_data = substr($row,5);
			$tax_array = explode(";", $full_data);
			
			foreach ($tax_array as $key=>$value) 
			{
				if(!empty($value) && (substr($value,0,1) != "O"))
				{
					$total_tax = $total_tax + (substr($value , 4 , 9));	
				}	
				
			}
						
			$base_fare = $total_price - $total_tax;
	
			$price_detail = array("base_fare"=>($base_fare) , 
									"total_tax"=>($total_tax),
									"total_price"=>($total_price)
									);
		}
		
		//-------------------- get Original tkt number-----------------------------

		if($string_reissue == 'FO')       
		{		
			$original_reissue = substr($row,6,10);
			
			$str_from_original_reissue_to_end = substr($row,6,13);
			$string_conj = strpos($str_from_original_reissue_to_end , "-");
				
			if($string_conj !== false)//if "-" exists , there's conj segment
			{
				$tkt_conj_number = substr($str_from_original_reissue_to_end, ($string_conj+1) , 2);
			}
			if(strlen($original_reissue) == 10)
			{
				$reissue_data = array("original_reissue"=>$original_reissue, "original_reissue_conj_number"=>$tkt_conj_number);
			}
			else
			{
				$original_reissue = "0";
				$reissue_data = array("original_reissue"=>$original_reissue, "original_reissue_conj_number"=>$tkt_conj_number);
			}
			
			
			//echo">>>>>>  original reissue tkt number  >>>>>>><br>";
			//echo"<pre>";
			//print_r($reissue_data);

		}
		
        break;
	
	
	//------------------ U- (Net - Tax)-----------------------------------------------------
	case "Insurance":
 		//other info from pass_name_tkt_number function
        break;
     
    default:
        echo "cannot detect tkt type";
}


//----------------commission to get VAT - NET (commission precentage or AMOUNT)----------------------		
		
		$total_tax = $price_detail["total_tax"];
		
		//-----------------refund NET - VAT valued-------------------
				
		if($tkt_type == "Refund") // refund commission
		{
			if($string_comm_rate == 'FM') // percent commission
			{
				//----------get the string-----------------
				$comm_full_string = strtok($row,";");
				
				//ectract number in array $comm_arr -----------------			
				preg_match_all('/\d+.\d{1,10}/', $comm_full_string, $comm_arr);
				
				//echo">>>>>>>>>>>>>>>>>...<pre>";
				//print_r($comm_arr);
				
				$comm_rate = $comm_arr[0][0];
							
				//$comm_rate = strtok(substr($row,2) , "P");
				
				$numeric_comm_rate = $comm_rate / 100 ;
				
				$comm_amount =  ($numeric_comm_rate * $fare_refund) ;//positive as it's refund
				$round_comm_amount = round($comm_amount,2);
				//echo"<br>comm_amount >>>>".$round_comm_amount;	
				
				$vat_amount = 0.05 * (-$comm_amount);//negative 
				$round_vat_amount = round($vat_amount,2);
				//echo"<br>vat_amount >>>>".$round_vat_amount;
				
				$net_amount = (-$fare_refund) + (-$total_tax_refund) + $comm_amount + $vat_amount + $cancel_fee;
				$round_net_amount = round($net_amount,2);
				echo"<br>net_amount >>>>".$round_net_amount;
				echo"<br> cancel_fee >>>>".$cancel_fee;
				
				$net = array("comm_amount"=>$round_comm_amount , 
							"vat_amount"=>$round_vat_amount , 
							"net_amount"=>$round_net_amount,
							"cancellation_fees"=>$cancel_fee);
			}
		
			// A for Amount
			if($string_comm_rate == 'FM' && (strpos($row, 'A') !== false)) // found AMOUNT
			{
				$full_data = substr($row,5);
				$comm_amount = strtok($full_data , "A");
				//echo"<br>comm_amount >>>>".$comm_amount;	
				
				$vat_amount = 0.05 * (-$comm_amount);//negative 
				$round_vat_amount = round($vat_amount,2);
				//echo"<br>vat_amount >>>>".$round_vat_amount;
				
				$net_amount = (-$fare_refund) + (-$total_tax_refund) + $comm_amount + $vat_amount + $cancel_fee;
				$round_net_amount = round($net_amount,2);
				//echo"<br>net_amount >>>>".$round_net_amount;
				
				$net = array("comm_amount"=>$round_comm_amount , 
							"vat_amount"=>$round_vat_amount , 
							"net_amount"=>$round_net_amount,
							"cancellation_fees"=>$cancel_fee);
			}

		}

		else
		{
			//-------------------check for FM and extract the string untill finding the first ; ------------------
			//--------------extract the number from this string-------------------------------
			
			if($string_comm_rate == 'FM') // percent commission
			{
				//----------get the string-----------------
				$comm_full_string = strtok($row,";");
				
				//check if the string has dot then exctract number in array $comm_arr -----------------			
				if(strpos($row, '.') !== false)
				{
					preg_match_all('/\d+.\d{1,10}/', $comm_full_string, $comm_arr);
					
					$comm_rate = $comm_arr[0][0];
					
					//echo">>>>>>>>>>>>>>>>>== $comm_rate <br> >>>>>>>comm_arr=<pre>";
					//print_r($comm_arr);
					
				}
				
				else
				{
					//extract number only
					preg_match_all('/\d+/', $comm_full_string, $comm_arr);
					
					//echo">>>>>>>>>>>>>>>>>==<pre>";
					//print_r($comm_arr);
					$comm_rate = $comm_arr[0][0];
				}
				
				//-------------------------------
				//or
				//$comm_rate = implode('',$numbers_arr[0]);
				//-------------------------------
				
				//echo"the commission is ====".$comm_rate;
				
				$numeric_comm_rate = $comm_rate / 100 ;
				
				$comm_amount = - ($numeric_comm_rate * $base_fare) ;//negative as it's active (normal) tkt
				$round_comm_amount = round($comm_amount,2);
				//echo"<br>comm_amount >>>>".$comm_amount;	
				
				$vat_amount = 0.05 * (-$comm_amount);
				$round_vat_amount = round($vat_amount,2);
				//echo"<br>vat_amount >>>>".$round_vat_amount;
				
				$net_amount = $base_fare + $total_tax + /*minus_value*/$comm_amount + $vat_amount ;
				$round_net_amount = round($net_amount,2);
				//echo"<br>net_amount >>>>".$round_net_amount;
				//echo"<br> total_tax >>>>".$total_tax;
				
				$net = array("comm_amount"=>$round_comm_amount , 
							"vat_amount"=>$round_vat_amount , 
							"net_amount"=>$round_net_amount);
				
			}
			
			// A for Amount
			if($string_comm_rate == 'FM' && (strpos($row, 'A') !== false)) // found AMOUNT
			{
				$full_data = substr($row,5);
				$comm_amount = strtok($full_data , "A");
				//echo"<br>comm_amount >>>>".$comm_amount;	
				
				$vat_amount = 0.05 * (-$comm_amount);
				$round_vat_amount = round($vat_amount,2);
				//echo"<br>vat_amount >>>>".$round_vat_amount;
				
				$net_amount = $base_fare + $total_tax + $comm_amount + $vat_amount ;
				$round_net_amount = round($net_amount,2);
				//echo"<br>net_amount >>>>".$round_net_amount;
				
				$net = array("comm_amount"=>$comm_amount , 
							"vat_amount"=>$round_vat_amount , 
							"net_amount"=>$round_net_amount);
			}
		}


//-----------------------------------------to get management fees-----------------------------
		
		//to get passenger number to know which service-fees will be used		
		if($string_passenger_number == 'FV')
		{
			$full_data = substr($row, 2);
			$P_checker = substr($row, (strpos($row, ";P") + 1) , 2) ; 
			$S_checker = substr($row, (strpos($row, ";S") + 1) , 2);    
			
			
			//echo"<br>full data of FV = ".$full_data."<br>";
			echo">>>>>>>> P_checker = ".$P_checker."<br>";
			echo">>>>>>>> S_checker = ".$S_checker."<br>";
		}
				
		$full_data = substr($row, 3);
		
		
		if($string_manage_fees == 'RIS' && (strpos($full_data , "SERVICE FEES") != false) )//to get management fees
		{
			/*
			$management_string = substr($full_data , 4);
			$passenger_str_number = strpos($management_string , ";P");
			$passenger_string = substr($management_string , ($passenger_str_number + 1) , 2);
			
			
			echo">>>>>>>> management_string = ".$management_string."<br>";
			echo">>>>>>>> passenger_str_number = ".$passenger_str_number."<br>";
			echo">>>>>>>> passenger_string = ".$passenger_string."<br>";
			*/
			
			// to get all lines that containt manage_fees and later we will decide which line wil be taken
			$manage_fees_row_arr[] = $row;
		
		/*
			if(($passenger_str_number !== false) && ($passenger_string == $P_checker) && ($flag_infant == 0 ))//if ;P exists
			{
					
				echo"<br> passenger_number = ".$P_checker."<br>";
				$management_fees_amount = strtok($management_string , ";");
				$manag_fees_arr = array("manage_fees"=>$management_fees_amount);
				//echo"<br> management_fees_amount >>>>>>= ".$management_fees_amount."<br>";
				break;
			}
			
						
			elseif(($passenger_str_number === false) && ($flag_infant == 0 ))//if ;P not exist
			{
				echo"<br> passenger_number = ".$P_checker."<br>";
				$management_fees_amount = strtok($management_string , ";");
				$manag_fees_arr = array("manage_fees"=>$management_fees_amount);
				//echo"<br> management_fees_amount *****= ".$management_fees_amount."<br>";
			}
			
			elseif((strpos($management_string , "/INF")!== false) && ($flag_infant == 1 ))//if /INF exist
			{
				$management_fees_amount = strtok($management_string , ";");
				$manag_fees_arr = array("manage_fees"=>$management_fees_amount);
				//echo"<br> management_fees_amount = ".$management_fees_amount."<br>";
			}
		*/			
			
			/*echo"<br> full data = ".$full_data."<br>";
			echo"<br> management_string = ".$management_string."<br>";
			
			echo"<br> passenger_string = ".$passenger_string."<br>";
			 */
			
		}
		
					
		elseif((strpos($full_data , "DISCOUNT") !== false))
		{
			//according to EgyTrav , there's no Discount 
			$management_fees_amount = 0 ;
			$manag_fees_arr = array("manage_fees"=>$management_fees_amount);
			//echo" management_fees_amount = ".$management_fees_amount;
		}

//-----------------------------------------to get Payment Method-----------------------------

		//----------------------- FILE ----------------------

		if($file_string == 'RIFFILE' || $file_string_2 == 'RIFILE')//
		{
						
			$file_code = substr($row, 7 );
			//echo"<br> >>>>>>>  file_code =".$file_code."<br>";
			
			$payment_method = "File";
			$pay_method_arr = array("Payment_Method"=>$payment_method , "Code"=>$file_code);
			
			//echo"<br> >>>>>>>  payment_method =".$payment_method."<br>";
		}
				
		//--------------------------- Card -------------------
			
		if($card_string == 'FPCC') //passenger name      
		{
			$full_data = substr($row, 4);
			echo"<br> >>>>>>>   full_data =".$full_data."<br>";
			
			$card_number = strtok($full_data, "/");
			//echo"<br> >>>>>>>   card_number =".$card_number."<br>";
	
			$payment_method = "Card";
			$pay_method_arr = array("Payment_Method"=>$payment_method , "Code"=>$card_number);
			
			//echo"<br> >>>>>>>  payment_method =".$payment_method."<br>";
		}

		//--------------------------- Client -------------------
		
		if($client_string == 'AITAN' )   
		{
			$full_data = substr($row, 5);
			//echo"<br> >>>>>>>   full_data =".$full_data."<br>";
			
			$corporate_code = strtok($full_data, ";");
			//echo"<br> >>>>>>>  corporate_code =".$corporate_code."<br>";

			$payment_method = "Client";
			$pay_method_arr = array("Payment_Method"=>$payment_method , "Code"=>$corporate_code);
			//echo"<br> >>>>>>>  payment_method =".$payment_method."<br>";
		}	
		
		if($client_string_2 == 'AIAN' )     
		{
			$full_data = substr($row, 4);
			echo"<br> >>>>>>>   full_data =".$full_data."<br>";
			
			$corporate_code = strtok($full_data, ";");
			//echo"<br> >>>>>>>  corporate_code =".$corporate_code."<br>";

			$payment_method = "Client";
			$pay_method_arr = array("Payment_Method"=>$payment_method , "Code"=>$corporate_code);
			//echo"<br> >>>>>>>  payment_method =".$payment_method."<br>";

		}

//-------------------------------------------------------------------------------------------------		
	
	}//end foreach

	//echo">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> reissue data <br><pre>";
	//print_r($price_detail);
	
	
	
	if(count($manage_fees_row_arr) == 1)// if there's one SERVICE FEES line
	{
		echo "only one feees<br><pre>";
		print_r($manage_fees_row_arr);
		
		$value = $manage_fees_row_arr[0];
		
		// check if currency found after RIS or not
		if(substr($value,0,4) == "RIS;")
		{
			$management_string = substr($value , 4);
			echo " management_string = $management_string<br>";
		}
		else
		{
			$management_string = substr($value , 7);
			echo " management_string = $management_string<br>";
		}
		$management_fees_amount = strtok($management_string , ";");
		$manag_fees_arr = array("manage_fees"=>$management_fees_amount);
		//echo"<br> management_fees_amount = ".$management_fees_amount."<br>";
	}
	elseif(count($manage_fees_row_arr)>1)// if there's two SERVICE FEES lines
	{
		echo"TWO service feeeeeees <br> <pre>";
		print_r($manage_fees_row_arr);
		
		foreach ($manage_fees_row_arr as $key => $value) 
		{
			$P_checker_str = strpos($value , $P_checker);
			$S_checker_str = strpos($value , $S_checker);
			$flag_infant_checker = strpos($value , "/INF");
			
			//-------------- if it's INFANT service fees----------------
			if(($flag_infant_checker !== false) && ($flag_infant == 1 ))
			{
				$management_string = substr($value , 7);
				$management_fees_amount = strtok($management_string , ";");
				$manag_fees_arr = array("manage_fees"=>$management_fees_amount);
			}
			
			elseif( ($P_checker_str !== false) || ($S_checker_str !== false))
			{
				echo"the right row is <br> $value <br>";
				$management_string = substr($value , 7);
				$management_fees_amount = strtok($management_string , ";");
				$manag_fees_arr = array("manage_fees"=>$management_fees_amount);
				
			}
			
			//take the first elemnt in $manag_fees_arr 
			//because if there's two services fess without key to konw which one to take
			else
			{
				echo"<br>can't detect which service fees so will get first fees <br> $value <br>";
				$management_string = substr($value , 7);
				$management_fees_amount = strtok($management_string , ";");
				$manag_fees_arr = array("manage_fees"=>$management_fees_amount);
				break;
				
			}
		}//end foreach
		
		
	}
	
	echo"<br> management fees amount = ".$manag_fees_arr["manage_fees"]."<br>";
	//return;
	
	
	$airline_info = array(
					"airlineName"=>$airline_name,
					"airline_Code"=>$airline_code,
					"airlineNumericCode"=>$airline_numeric_code,
					"booking_clerk"=>$booking_clerk,
					"executive"=>$executive,
					"flight_type"=>$flight_type,
					"IATA_number"=>$IATA_number) ;
					
//---------to check if the payment method is empty it is CASH or UNKNOWN method--------------------------

	if(empty($pay_method_arr["Payment_Method"]) && empty($pay_method_arr["Code"]))
	{
		$payment_method = "Unknown";
		$pay_method_arr = array("Payment_Method"=>$payment_method , "Code"=>"/");
		$final_arr = array_merge($amd_arr , $org_dest, $date,
								$airline_info , $price_detail , 
								$net , 
								$pay_method_arr,
								$manag_fees_arr,
								$reissue_data,
								$refund_data);

	}
	else
	{
		//echo"payment method is Client";
		//echo"<pre>";
		//print_r($pay_method_arr);
		$final_arr = array_merge($amd_arr , $org_dest, $date,
								$airline_info , $price_detail , 
								$net , $pay_method_arr,
								$manag_fees_arr,
								$reissue_data,
								$refund_data);
	}
	
//---------------------------------------------insert---------------------

		$this_item->clear() ;
		$this_item->business_data["AMD_FILE_NAME"] = $amd_file_name;
		$this_item->business_data["TKT_AMD_NUMBER"] = $final_arr["amd_number"];
		$this_item->business_data["TKT_BASE_FARE"] = $final_arr["base_fare"];
		$this_item->business_data["TKT_TOTAL_PRICE"] = $final_arr["total_price"];
		$this_item->business_data["TKT_VAT"] = $final_arr["vat_amount"];
		$this_item->business_data["TKT_NET"] = $final_arr["net_amount"];
		$this_item->business_data["TKT_COMMISSION"] = $final_arr["comm_amount"];
		$this_item->business_data["TKT_TOTAL_TAX"] = $final_arr["total_tax"];
		$this_item->business_data["ORG_DEST"] = $final_arr["org/dest"];
		$this_item->business_data["AIRLINE_NAME"] = $final_arr["airlineName"];
		$this_item->business_data["AIRLINE_CODE"] = $final_arr["airline_Code"];
		$this_item->business_data["AIRLINE_NUMERIC_CODE"] = $final_arr["airlineNumericCode"];
		$this_item->business_data["BOOKING_CLERK"] = $final_arr["booking_clerk"];
		$this_item->business_data["EXECUTIVE"] = $final_arr["executive"];
		$this_item->business_data["FLIGHT_TYPE"] = $final_arr["flight_type"];
		$this_item->business_data["BOOK_AGNCY_IATA_NUMBER"] = $final_arr["IATA_number"];
		$this_item->business_data["DATE_PNR_CREATION"] = $final_arr["PNR_creation_date"];
		$this_item->business_data["DATE_PNR_CHANGE"] = $final_arr["PNR_change_date"];
		
		if($tkt_type == "Refund")
			$this_item->business_data["DATE_AIR_CREATION"] = $refund_data["refund_date"];
		else
			$this_item->business_data["DATE_AIR_CREATION"] = $final_arr["AIR_creation_date"];
		
		//$this_item->business_data["DATE_REFUND"] = $refund_data["refund_date"];
		$this_item->business_data["ORIGINAL_REFUND"] = $refund_data["refund_number"];
		$this_item->business_data["CANCEL_FEES_REFUND"] = $refund_data["cancellation_fees"];
		
		$this_item->business_data["ORIGINAL_REISSUE"] = $reissue_data["original_reissue"];
		$this_item->business_data["ORIGINAL_REISSUE_CONJ_NUMBER"] = $reissue_data["original_reissue_conj_number"];		
		
		$this_item->business_data["MANAGEMENT_FEES"] = $manag_fees_arr["manage_fees"];
		$this_item->business_data["PAY_METHOD"] = $final_arr["Payment_Method"];
		$this_item->business_data["PAY_CODE"] = $final_arr["Code"];
		$this_item->business_data["TKT_TYPE"] = $tkt_type;
	
	$this_item->update();	
 
	$id = $this_item->ID();
	
	$total_fare_array = array("base_fare"=>$final_arr["base_fare"] , 
							"total_price"=>$final_arr["total_price"],
							"total_tax"=>$final_arr["total_tax"],
							"id"=>$id);
	echo"<br>****************** main tkt info ***********************<pre>";		
	echo"<pre>";
	print_r($final_arr);
	
		
	//echo"////////////////////////////////////////<pre>";
	//print_r($total_fare_array);
	return $total_fare_array;
}
//________________________________________________________________________

//public function insert_elemnt_in_arr()//to run this function only without main_process
public function insert_elemnt_in_arr($my_arr , $my_index)
{
	
//*************************************** Function Description *********************************
//*
//* pass to it : tkt_number_price array -> (my_array) that i want to insert element in it with
//* the index of INFANT -> (my_index) 

	//$my_index=1;//assume INFANT index (the new element index)
	
	//------------ my_array before insertion 
	//------------ i want to insert new element with zero values
	/*$my_arr = array(
					0 => array(
						"tkt_number" => "00",
			            "conj_number" => "/",
			            "ins_net" => "138.04",
			            "ins_total_tax" => "17.96",
			            "PASSENGER_TKT_TOTAL_PRICE" => "156.00"),
					1 => array(
						"tkt_number" => "11",
			            "conj_number" => "/",
			            "ins_net" => "138.04",
			            "ins_total_tax" => "17.96",
			            "PASSENGER_TKT_TOTAL_PRICE" => "156.00"),
					2 => array(
						"tkt_number" => "22",
			            "conj_number" => "/",
			            "ins_net" => "138.04",
			            "ins_total_tax" => "17.96",
			            "PASSENGER_TKT_TOTAL_PRICE" => "156.00")
						);*/
	//------- the result should be like this
	/*$my_arr = array(
					0 => array(
						"tkt_number" => "00",
			            "conj_number" => "/",
			            "ins_net" => "138.04",
			            "ins_total_tax" => "17.96",
			            "PASSENGER_TKT_TOTAL_PRICE" => "156.00"),
	 				1 => array(
						"tkt_number" => "0",
			            "conj_number" => "/",
			            "ins_net" => "0",
			            "ins_total_tax" => "0",
			            "PASSENGER_TKT_TOTAL_PRICE" => "0"),
					2 => array(
						"tkt_number" => "11",
			            "conj_number" => "/",
			            "ins_net" => "138.04",
			            "ins_total_tax" => "17.96",
			            "PASSENGER_TKT_TOTAL_PRICE" => "156.00"),
					3 => array(
						"tkt_number" => "22",
			            "conj_number" => "/",
			            "ins_net" => "138.04",
			            "ins_total_tax" => "17.96",
			            "PASSENGER_TKT_TOTAL_PRICE" => "156.00")
						);*/
//***********************************************************************************************
	//echo"index = $my_index , THe incomin array <br> <pre>";
	//print_r($my_arr);
	
	$new_arr = array(array());
	$number_of_elemnets = count($my_arr);

	for ($key=0; $key < ($number_of_elemnets+1) ; $key++) 
	{ 
		if($my_index > $key)
		{
			$new_arr[$key] = $my_arr[$key];
		}
		if($my_index == $key)
		{
			$new_arr[$key] = array("0");
		}
		if($my_index < $key)
		{
			$new_arr[$key] = $my_arr[$key - 1];
		}
	}
		
	//echo"new array after insert new Zero element is <br><pre>";
	//print_r($new_arr);
	
	return $new_arr;
}

//_______________________________________________________________________

//public function air_ticket_tax()//to run this function only without main_process
public function air_ticket_tax($new_arr , $ticket_id)
{
	$this_item = & $this->bi_air_tax;
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  
			
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		$data["public_data"] = $this->admin_public->DATA;
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);	// takes care of login / header loading
		return false; 
	}

//--------------------to run this function only -----------------------------
//-------------------- uncomment those lines---------------------------------
	//$new_arr = $this->ticket_read();//to read specific file and return file content in array
	//echo"<pre>";
	//print_r($new_arr);
	//$ticket_id = "465"; //for test 
//---------------------------------------------------------------------------
	
	
//************************************ Declerations ************************************
			
	$tax_amount=array();
	$tax_array=array();
	$tax_code_arr=array();
	$final_tax_arr=array();


 	foreach ($new_arr as $row) 
 	{
		$string = substr($row,0,4);
		$string_2 = substr($row,0,3);
		$string_3 = substr($row,0,3);	
		
		$reissue_tax_string = substr($row,0,4);
		$refund_tax_string = substr($row,0,3);
		
//-------------------- normal tkt tax-------------------------
		
		//check first if KFTF is found , 
		//if not , check second if KNT (in common KNT comes with KST) , 
		//if not , make sure the tax_array is empty and KST is found
		
		if(($string == 'KFTF') || ($string_2 == 'KNT') || ((empty($tax_array) && ($string_3 == 'KST'))))       
		{
			$full_data = substr($row,5);
			$tax_array = explode(";", $full_data);			
		}
		
//-------------------- reissue tkt tax-------------------------
		
		elseif($reissue_tax_string == 'KFTR')       
		{

			$full_data = substr($row,5);
			$tax_array = explode(";", $full_data);
			
			foreach ($tax_array as $key=>$value) 
			{
				//---------- to get new tax value only which hasn't O letter ---------------
				//---------- letter O refers to old-tax---------------
				
				if(!empty($value) && (substr($value,0,1) != "O"))
				{
					$tax_code_str = substr($value , 13 , 3);
					
					$tax_amount = substr($value , 4 , 9);
					
					$final_tax_arr[] = array("TAX_AMOUNT"=>$tax_amount , 
								"TAX_CODE"=>$tax_code_str,
								"TAX_TKT_ID"=>$ticket_id,
								);
				}		
			}
			
			foreach($final_tax_arr as $key=>$value)
			{
				if(empty($value["TAX_AMOUNT"]) && empty($value["TAX_CODE"]))
					unset($final_tax_arr[$key]);
			}
			
			echo " tax amounts";
			echo"<pre>";
			print_r($final_tax_arr);
			echo "<br>";
			
			goto insert_reissue_tax;
						
		}

//-------------------- Refund tkt tax-------------------------
		
		elseif($refund_tax_string == 'KRF')       
		{
			$full_data = substr($row,5);
			$tax_array = explode(";", $full_data);
		
		}

	}//end foreach

	//echo "----------------- all tax_array ----------- <br><pre>";
	//print_r($tax_array);
	
	//1------- 
	//tax_array is array with all taxes even the empty rows
	//to ignore the last element in the tax_array
	foreach($tax_array as $key=>$value)
	{
		if(strlen($value)==1)
		unset($tax_array[$key]);
	}
	
	//2------- 
	//filter the tax_array from empty elements 
	$filter_tax_arr = array_filter($tax_array) ;
	
	//echo "----------------- filter_tax_arr -----------<br> <pre>";
	//print_r($filter_tax_arr);
	
	//3------- 
	//--------------- to get tax amounts in an array ------------------------	
	foreach ($filter_tax_arr as $row) 
	{
		$tax_amount[] = substr($row,4, 9);
	}
	
	//echo "----------------- tax_amount -----------<br> <pre>";
	//print_r($tax_amount);
	
	//4------- 
	//--------- get tax_codes
	foreach ($filter_tax_arr as $row) 
	{
		//to skip the tax that dosen't contains number and skip its code
		if(strpos($row, 'EXEMPT') !== false)
		continue;
		else
		{
			$tax_code_str = substr($row , 13 , 3);
			$tax_code_arr[] = $tax_code_str;
		}
	}
	
	//echo "---------------- tax_code_arr -----------<br> <pre>";
	//print_r($tax_code_arr);
	
	
	//5------- 
	//-------------merge tax_amount with tax_codes------
	//------------- loop to save each amount with its code in new array ------
	
	for ($i=0; $i < count($tax_code_arr) ; $i++) 
	{
		$final_tax_arr[] = array("TAX_AMOUNT"=>$tax_amount[$i] , 
								"TAX_CODE"=>$tax_code_arr[$i] , 
								"TAX_TKT_ID"=>$ticket_id);	
	}
	
	if(empty($final_tax_arr))
	{
		$final_tax_arr[] = array("TAX_AMOUNT"=>"0" , 
								"TAX_CODE"=>"0" , 
								"TAX_TKT_ID"=>$ticket_id);
		//-------------------insert in database--------------------------	
		//$this->db->insert_batch('AIR_TAX_S' ,$final_tax_arr );
	}
	else
	{}
	
	echo"<br>----- final_tax_array ------<br><pre>";
	print_r( $final_tax_arr);
		
//-------------------insert in database--------------------------
	insert_reissue_tax://comes from goto from reissue tkt
	
	foreach($final_tax_arr as $key => $value)
	{	
		$this_item->clear() ;	
		$this_item->business_data["TAX_AMOUNT"] = $value["TAX_AMOUNT"];
		$this_item->business_data["TAX_TKT_ID"] =  $ticket_id;
		$this_item->business_data["TAX_CODE"] = $value["TAX_CODE"];
		$this_item->update();
	}
			
 //---------------------------------------------------------------
	 	
 	return $new_arr;
}

//_______________________________________________________________________

//public function segment()//to run this function only without main_process
public function segment($new_arr , $ticket_id)
{
	$this_item = & $this->bi_air_segment;
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  
			
	if ($this->admin_public->verify_access("read",0) == false ) 
	{
		$data["public_data"] = $this->admin_public->DATA;
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ; 					
		$this->load->view( '_general/general/invalid_rights_message',$data);		// takes care of login / header loading
		return false; 
	}
	
//--------------------to run this function only -----------------------------
//-------------------- uncomment those lines---------------------------------
	//$new_arr = $this->ticket_read();//to read specific file and return file content in array
	//echo"<pre>";
	//print_r($new_arr);
	//$ticket_id="55";
//---------------------------------------------------------------------------
	
	
	$all_segments_void = array();
	$all_segments = array();
	
 	foreach ($new_arr as $row) 
 	{
		$string = substr($row,0,3);
		$year_string = substr($row,0,2);
		$string_tkt_number = substr($row,0,2);
		
		if($year_string == 'D-')
		$year = substr($row,2,2);
		//echo"the yer is ".
		
		
		if($string == 'H-0')       
		{
			
			//$full_data = substr($row,2);
			$segment_number = substr($row,2, 3);
			$segment_data = substr($row,6);
			
			$full_year = "20".$year."-";
			//echo "the year is>>>>> " .$full_year;
			
			$origin_city_code = substr($segment_data , 4 , 3);
			$destination_city_code = substr($segment_data , 26 , 3);
			//echo "<br> full_data>>>>> " .$full_data;
			//echo "<br> segment_data>>>>> " .$segment_data;
			//echo "<br> segment_number>>>>> " .$segment_number;
			
			//echo "<br> origin_city_code>>>>> " .$origin_city_code;
			//echo "<br> destination_city_code>>>>> " .$destination_city_code;
			
			//$origin_city_name = substr($segment_data , 8 , 17);//17 = origin_city_name length
			$origin_city_name = ltrim(rtrim(substr($segment_data , 8 , 17)," ")," ");
			
			//$destination_city_name = substr($segment_data , 30 , 17);//17 = destination_city_name length
			$destination_city_name = ltrim(rtrim(substr($segment_data , 30 , 17)," ")," ");
			
			
			//$flight_number = substr($segment_data , 54 , 5);//5 = flight_number length
			$flight_number = ltrim(rtrim(substr($segment_data , 54 , 5)," ")," ");
		
			//$class = substr($segment_data , 59 , 2);//30=$string_2 , 17=Destination City Name , 6=Airline Code , 1=";"
			//class of sevice
			$class = ltrim(rtrim(substr($segment_data , 59 , 2)," ")," ");
							
			$string_1 = substr($segment_data , 63 , 5 );//54=$string_3 , 5=Flight Number , 2=Class of Service , 2=Class of Booking 
			$departure_date_format =substr(substr($string_1 , 0 ,5),0,2). "-" . substr(substr($string_1 , 0 ,5),2,3) ;	
			$strtotime_departure = strtotime($departure_date_format);
            $departure_date = date($full_year.'m-d',$strtotime_departure);
		
			$departure_time_string = substr($segment_data , 68 ,5);//63=$string_4 , 5=Departure Date //5 = departure_time length
			$departure_time = substr($departure_time_string,0,2).":".substr($departure_time_string,2,2);
			
			$arrival_time_string = substr($segment_data , 73 ,5);//68=$string_5 , 5=Departure Time //5 = arrival_time length
			$arrival_time = substr($arrival_time_string,0,2).":".substr($arrival_time_string,2,2);
			
			$string_2 = substr($segment_data , 78 ,5);//73=$string_6 , 5=Arrival Time 
			$arrival_date_format = substr(substr($string_2 , 0 ,5),0,2)."-". substr(substr($string_2 , 0 ,5),2,3) ;	
			$strtotime_arrival = strtotime($arrival_date_format);
            $arrival_date = date($full_year.'m-d',$strtotime_arrival);
		
			//$duration_time = $arrival_time - $departure_time ;
			
			//$origin_country_name = substr($segment_data , -7);
			//$destination_country_name = substr($segment_data ,-5);
			
			if($segment_number =='000')
			{
				$all_segments_void[] = array("data segment ".$segment_number=>$segment_data , 
											"origin_city_name"=>$origin_city_code , 
											"destination_city_name"=>$destination_city_code ,
											"segment_number"=>$segment_number,
											);
			}
			else
			{
				$all_segments[] = array( 
									"origin_city_name"=>$origin_city_code , 
									"destination_city_name"=>$destination_city_code ,
									"flight_number"=>$flight_number ,
									"class"=>$class ,
									"departure_date"=>$departure_date ,
									"departure_time"=>$departure_time ,
									"arrival_date"=>$arrival_date,
									"arrival_time"=>$arrival_time ,
									"segment_number"=>$segment_number,
									
									);
			}
	
		
		//echo "full_data is ".$full_data ;
		//echo "<br>--------------<br>";			
		}//end if H-0
 

}//end foreach
	
	if(!empty($all_segments_void))
	{
		$result = array_merge($all_segments_void , $all_segments);
	}
	else
	{
		$result =  $all_segments;
	}
	
	
	echo "all segments result";
	echo "<br> <pre>";
	print_r($result);

	
//-------------------insert in database--------------------------

	foreach($result as $key => $value)
	{
		foreach($value as $row)
		{
			if($value["segment_number"] =='000')//void segment
			{
				$this_item->clear() ;	
				$this_item->business_data["SEGMENT_TKT_ID"] = $ticket_id;
				$this_item->business_data["FLIGHT_NUMBER"] = "VOID Sector";
				$this_item->business_data["SEGMENT_NUMBER"] = $value["segment_number"];
				$this_item->business_data["ORIGIN_CITY"] = $value["origin_city_name"];
				$this_item->business_data["DEST_CITY"] = $value["destination_city_name"];
			}
			else
			{
				$this_item->clear() ;	
				$this_item->business_data["SEGMENT_TKT_ID"] = $ticket_id;
				$this_item->business_data["SEGMENT_NUMBER"] = $value["segment_number"];
				$this_item->business_data["FLIGHT_NUMBER"] = $value["flight_number"];
				$this_item->business_data["ORIGIN_CITY"] = $value["origin_city_name"];
				$this_item->business_data["DEST_CITY"] = $value["destination_city_name"];
				$this_item->business_data["DEPARTURE_DATE"] = $value["departure_date"];
				$this_item->business_data["DEPARTURE_TIME"] = $value["departure_time"];
				$this_item->business_data["ARRIVAL_DATE"] = $value["arrival_date"];
				$this_item->business_data["ARRIVAL_TIME"] = $value["arrival_time"];
				//$this_item->business_data["FARE_BASIS"] = $value["fare_basis"];
				$this_item->business_data["CLASS_OF_SEGMENT"] = $value["class"];
			
			}
		}


		$this_item->update();
	}
	
 //---------------------------------------------------------------
	
}

//_______________________________________________________________________

public function logout_auto()
{
	
    if ($this->admin_public->DATA["login_with_secret_key"] == "YES!") 
    {
        $this->load->model("admin/admin_user") ;
        $this->admin_user->logout(); 

        //$this->bi_log->logthis( "SEC :  logged out  ..... ")     ;  
        //redirect (site_url("account/login/logout")) ;          
        }
 
    }

public function ajax_table($purpose="" , $show_box = 'no')
	{
		
		$access_component_name = "security.general" ; 
		if (!$this->_top_function($access_component_name,'yes')) return ; 
		
		$access_verb="read" ;
		$data = array() ;
		$data["public_data"] = $this->admin_public->DATA;  	
		
				
		if ($this->admin_public->verify_access($access_verb,0) == false ) 
		{
			$data["access_component_name"] = $access_component_name ; 
			$data["access_verb"] = $access_verb ;							
			$this->load->view( '_general/general/invalid_rights_message',$data);		; // takes care of login / header loading
			return ; 
			
		}
			
		$this_item = & $this->bi_air_ticket; 
	
		$data["list_table"] = $this_item->list_items_rtable( "all",array() ,"");
		
		$data["table_purpose"] =$purpose ; 
		
		$data["this_concept"] = "air_ticket" ;
		$data["this_controller"] = $this->controller; 
	
		$data["this_id_field"] = "TKT_ID" ; 
		$data["this_name_field"] = "TKT_AMD_NUMBER" ; 
		$data["this_name_field_ar"] = "TKT_AMD_NUMBER" ;
	
	 	$data["options"]["hide_add_button"] = false ; 
		$data["options"]["disable_line_add"] = true ; 
		$data["options"]["disable_line_edit"] = true ; 
		$data["options"]["disable_line_delete"] = true ;
		$data["options"]["hide_line_verbs"] = false ; 
		$data["options"]["disable_datatable"] = false ; 
		$data["options"]["line_verbs_colors"] = true ; 
		$data["options"]["line_verbs_buttons"] = true ; 
 				
	  if ( $show_box == 'yes' )
             {
                 r_theme_box_start(r_langline('list_title',$this->concept.".master."),12,
					array("body_id"=>$this->concept."_list_body",
							"box_id"=>$this->concept."_list_box",
							"tools"=>"reload"	,
							"body_attributes"=>array('url'=>site_url($this->controller.'/ajax_table'))
						,	"back_color"=>"green" 
			
						) 
					); 
              }		
		if (!isset ($page_langauge)) $page_langauge = $this->admin_public->DATA["system_lang"] ;
		$template_folder = "_templates/".$this->template_name."/" ;  
		$this->load->helper($this->theme_helper)	;				
		$this->load->view( '_general/concept_table_aj',$data);
		return ; 
	}

public function ajax_table_passenger($purpose="" , $show_box = 'no')
	{
		
		$access_component_name = "security.general" ; 
		if (!$this->_top_function($access_component_name,'yes')) return ; 
		
		$access_verb="read" ;
		$data = array() ;
		$data["public_data"] = $this->admin_public->DATA;  	
		
				
		if ($this->admin_public->verify_access($access_verb,0) == false ) 
			{
				$data["access_component_name"] = $access_component_name ; 
				$data["access_verb"] = $access_verb ;							
				$this->load->view( '_general/general/invalid_rights_message',$data);		; // takes care of login / header loading
				return ; 
				
			}
		
		$TKT_ID = $this->uri->segment(4, 0);
			
		$this_item = & $this->bi_air_passenger; 
	
		//$data["list_table"] = $this_item->list_items_rtable( "all",array() ,"");
		$data["list_table"] = $this_item->list_items_rtable( "passenger",array("ticket_id"=>$TKT_ID) ,"" ,"default");
		
		$data["table_purpose"] =$purpose ; 
		
		$data["this_concept"] = "air_ticket" ;
		$data["this_controller"] = $this->controller; 
	
		$data["this_id_field"] = "PASSENGER_ID" ; 
		$data["this_name_field"] = "PASSENGER_FIRST_NAME" ; 
		$data["this_name_field_ar"] = "PASSENGER_FIRST_NAME" ;
		
	 	$data["options"]["hide_add_button"] = false ; 
		$data["options"]["disable_line_add"] = true ; 
		$data["options"]["disable_line_edit"] = true ; 
		$data["options"]["disable_line_delete"] = true ;
		$data["options"]["hide_line_verbs"] = true ; 
		//-----------box---------------------------------
		$data["show_box_title"] =  "Passengers Info" ;
		$data["options"]["disable_datatable"] = true ; 
		$data["options"]["line_verbs_colors"] = true ; 
		$data["options"]["line_verbs_buttons"] = true ; 
		//$data["hscroll"] = true ;
		
	  if ( $show_box == 'yes' )
             {
                 r_theme_box_start(r_langline('list_title',$this->concept.".master."),12,
					array("body_id"=>$this->concept."_list_body",
							"box_id"=>$this->concept."_list_box",
							"tools"=>"reload"	,
							"body_attributes"=>array('url'=>site_url($this->controller.'/ajax_table'))
						,	"back_color"=>"green" 
			
						) 
					); 
              }		
		if (!isset ($page_langauge)) $page_langauge = $this->admin_public->DATA["system_lang"] ;
		$template_folder = "_templates/".$this->template_name."/" ;  
		$this->load->helper($this->theme_helper)	;				
		$this->load->view( '_general/concept_table_aj',$data);
		return ; 
	}
	
public function ajax_table_tax($purpose="" , $show_box = 'no')
	{
		
		$access_component_name = "security.general" ; 
		if (!$this->_top_function($access_component_name,'yes')) return ; 
		
		$access_verb="read" ;
		$data = array() ;
		$data["public_data"] = $this->admin_public->DATA;  	
		
				
		if ($this->admin_public->verify_access($access_verb,0) == false ) 
			{
				$data["access_component_name"] = $access_component_name ; 
				$data["access_verb"] = $access_verb ;							
				$this->load->view( '_general/general/invalid_rights_message',$data);		; // takes care of login / header loading
				return ; 
				
			}
		
		$TKT_ID = $this->uri->segment(4, 0);
			
		$this_item = & $this->bi_air_tax; 
	
		//$data["list_table"] = $this_item->list_items_rtable( "all",array() ,"");
		$data["list_table"] = $this_item->list_items_rtable( "tax",array("ticket_id"=>$TKT_ID) ,"" ,"default");
		
		$data["table_purpose"] =$purpose ; 
		
		$data["this_concept"] = "air_ticket" ;
		$data["this_controller"] = $this->controller; 
	
		$data["this_id_field"] = "TAX_ID" ; 
		$data["this_name_field"] = "TAX_ID" ; 
		$data["this_name_field_ar"] = "TAX_ID" ;
	
	 	$data["options"]["hide_add_button"] = false ; 
		$data["options"]["disable_line_add"] = true ; 
		$data["options"]["disable_line_edit"] = true ; 
		$data["options"]["disable_line_delete"] = true ;
		$data["options"]["hide_line_verbs"] = false ; 
		//--------------box---------------
		$data["show_box_title"] =  "Tax Details" ;
		$data["options"]["disable_datatable"] = true ; 
		$data["options"]["line_verbs_colors"] = true ; 
		$data["options"]["line_verbs_buttons"] = true ; 
		$data["hscroll"] = true ;
 
		
	  if ( $show_box == 'yes' )
             {
                 r_theme_box_start(r_langline('list_title',$this->concept.".master."),12,
					array("body_id"=>$this->concept."_list_body",
							"box_id"=>$this->concept."_list_box",
							"tools"=>"reload"	,
							"body_attributes"=>array('url'=>site_url($this->controller.'/ajax_table'))
						,	"back_color"=>"green" 
			
						) 
					); 
              }		
		if (!isset ($page_langauge)) $page_langauge = $this->admin_public->DATA["system_lang"] ;
		$template_folder = "_templates/".$this->template_name."/" ;  
		$this->load->helper($this->theme_helper)	;				
		$this->load->view( '_general/concept_table_aj',$data);
		return ; 
	}
	
public function ajax_table_segment($purpose="" , $show_box = 'no')
{
	
	$access_component_name = "security.general" ; 
	if (!$this->_top_function($access_component_name,'yes')) return ; 
	
	$access_verb="read" ;
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  	
	
			
	if ($this->admin_public->verify_access($access_verb,0) == false ) 
	{
		$data["access_component_name"] = $access_component_name ; 
		$data["access_verb"] = $access_verb ;							
		$this->load->view( '_general/general/invalid_rights_message',$data);		; // takes care of login / header loading
		return ; 
		
	}
	
	$TKT_ID = $this->uri->segment(4, 0);
		
	$this_item = & $this->bi_air_segment; 

	//$data["list_table"] = $this_item->list_items_rtable( "all",array() ,"");
	$data["list_table"] = $this_item->list_items_rtable( "segment",array("ticket_id"=>$TKT_ID) ,"" ,"default");
	
	$data["table_purpose"] =$purpose ; 
	
	$data["this_concept"] = "air_ticket" ;
	$data["this_controller"] = $this->controller; 

	$data["this_id_field"] = "SEGMENT_ID" ; 
	$data["this_name_field"] = "SEGMENT_ID" ; 
	$data["this_name_field_ar"] = "SEGMENT_ID" ;

 	$data["options"]["hide_add_button"] = false ; 
	$data["options"]["disable_line_add"] = true ; 
	$data["options"]["disable_line_edit"] = true ; 
	$data["options"]["disable_line_delete"] = true ;
	$data["options"]["hide_line_verbs"] = false ; 
	
	//-------------------box-------------------------
	$data["show_box_title"] =  "Segments Details" ;
	$data["options"]["disable_datatable"] = true ; 
	$data["options"]["line_verbs_colors"] = true ; 
	$data["options"]["line_verbs_buttons"] = true ; 
	$data["hscroll"] = true ;
 
		
	if ( $show_box == 'yes' )
	{
     r_theme_box_start(r_langline('list_title',$this->concept.".master."),12,
		array("body_id"=>$this->concept."_list_body",
				"box_id"=>$this->concept."_list_box",
				"tools"=>"reload"	,
				"body_attributes"=>array('url'=>site_url($this->controller.'/ajax_table'))
			,	"back_color"=>"green" 

			) 
		); 
	 }		
	if (!isset ($page_langauge)) $page_langauge = $this->admin_public->DATA["system_lang"] ;
	$template_folder = "_templates/".$this->template_name."/" ;  
	$this->load->helper($this->theme_helper)	;				
	$this->load->view( '_general/concept_table_aj',$data);
	return ; 
}
	
//--------------------------------------------------------------------------------------------------
	
public function ajax_edit()
{
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  			
	if ($this->admin_public->verify_access("read",0) == false ) 
		{
			$data["access_component_name"] = $access_component_name ; 
			$data["access_verb"] = $access_verb ; 					
			$this->load->view( '_general/general/invalid_rights_message',$data);		; // takes care of login / header loading 
		}

	$this->load->library("form_validation");
	//------ to get fields from other models
	$this->load->model("bi_air_passenger");
	$this->load->model("bi_air_segment");
	$this->load->model("bi_air_tax");

	// load & read Existing object  ----------------------------------------------------
		$this_item = & $this->bi_air_ticket; 
		$this_item->clear();

		$incoming_id = $this->uri->segment(4, 0);//3 is number of segements in the url after /index.php 
	 
		if ($incoming_id !=0) {
			$this_item->Read($incoming_id,"",1);
			if (!$this_item->is_published )
			{
				//redirect with error not found object  
			}
		}
		
		$data["this_controller"] = $this->controller; 	
	//	echo $this->concept ; 
		
		$this->form_validation->set_rules("TKT_ID","TKT ID", "required") ;
		
		/*$drug_name = $this->input->post('drug_name') ;
		$check="select * from drug_s where drug_name='$drug_name'";
		$d_name = $this->db->query($check);
    	$result = $d_name->result_array();*/
    	//$checkrows= mysqli_num_rows($check);
		
		
		if ($this->form_validation->run() == FALSE )
		{	 
			$data["this_item"] = $this_item ; 			
			$data["public_data"] = $this->admin_public->DATA;
			$data["disable_edit"] = false;						
			$this->load->view( $this->view_folder.'/'.'amd_edit',$data);	
			return ; 
		}
		
		else 
		{			 
			if ($this_item->ID()==0) 
			{ if ($this->admin_public->verify_access("new",1) == false ) return ;}
			else { if ($this->admin_public->verify_access("edit",1) == false ) return ; }

			/*
			if ($this_item->ID()== 0)//new item
			{
				if($this_item->check_value_exist(array("drug_name"=>$this->input->post('drug_name')),0,0)== true)
					$continue_with_save = false ; 
			}
			else //edit exist item
			{
				
			$existing_id = $this_item->check_value_exist(array("drug_name"=>$this->input->post('drug_name')),0,1); 
				if ($existing_id != $this_item->ID() && $existing_id == true) 
					 $continue_with_save = false ;
			}		
					
			//	$this_item->business_data["date_created"] =  date('Y-m-d H:i:s');
			// ---------------------------------------------------------------------------------------------
			// this assumes that you only expose business_data from editing or filling 						/
			// you may require the input->post manually if you have additional fields , that_ 				/
			// are not in the data base or the business data 												/
			// ---------------------------------------------------------------------------------------------
			
			 */
					// just a quick fix for boolean // should find a long term solution
				//	$this_item->business_data["drug_available"] = 0 ; //it's for check-box when it's unchecked return 0
					//to add new values
			foreach ($this_item->business_data as $key => $value)
			{
			if (key_exists($key, $this->input->post())) // if ($this->input->post($key))
				{ 
				$this_item->business_data[$key] =$this->input->post($key);  	
				}
			}
					
			$this_item->validate();
		
			if ($this_item->success==FALSE)
			{
				//goto redo; 
				
				$data["this_item"] = $this_item ; 			
				$data["public_data"] = $this->admin_public->DATA;
				$data["disable_edit"] = false;		
				$template_folder = "_templates/".$this->template_name."/" ;  
				$this->load->helper($this->theme_helper)	;							
				$this->load->view( $this->view_folder.'/'.$this->concept .'_edit',$data);
				echo "<b><center>Error Validating Business Data</center></d><hr/>" ; 
				return ;
			}
			else
			{
				$this_item->business_data["sys_account_id"] = $this->admin_public->DATA["sys_account_id"];
				//print_r($this_item->business_data) ; 
				$this_item->update();
				$data["this_item"] = $this_item ; 			
				$data["public_data"] = $this->admin_public->DATA;
				$data["disable_edit"] = false;						
				$this->load->view( $this->view_folder.'/'.'amd_edit',$data);	
			
				echo "FINE: OK :"."<a msg=record_update_success /><ID>".$this_item->ID()."</ID>" ; 
			}					
			return;
			}		
			
}

//--------------------------------------------------------------------------------------------------
public function ajax_update_ticket()
{
	$access_component_name = "security.general" ; 
	$access_verb="read" ;
	
	if (!$this->_top_function($access_component_name)) return ; 
	$data = array() ;
	$data["public_data"] = $this->admin_public->DATA;  			
	if ($this->admin_public->verify_access("read",0) == false ) 
		{
			$data["access_component_name"] = $access_component_name ; 
			$data["access_verb"] = $access_verb ; 					
			$this->load->view( '_general/general/invalid_rights_message',$data);		; // takes care of login / header loading 
		}

	$this->load->library("form_validation");
	//------ to get fields from other models

	// load & read Existing object  ----------------------------------------------------
	$this_item = & $this->bi_air_ticket; 
	$this_item->clear();

	$incoming_id = $this->uri->segment(4, 0);
	$passenger_id = $this->uri->segment(5, 0);//3 is number of segements in the url after /index.php 
 
	if ($incoming_id !=0) {
		$this_item->Read($incoming_id,"",1);
		if (!$this_item->is_published )
		{
			//redirect with error not found object  
		}
	}
	
		$data["this_controller"] = $this->controller; 	
	//	echo $this->concept ; 
		
		//$this->form_validation->set_rules("TKT_ID","TKT ID", "required") ;
		
		/*$drug_name = $this->input->post('drug_name') ;
		$check="select * from drug_s where drug_name='$drug_name'";
		$d_name = $this->db->query($check);
    	$result = $d_name->result_array();*/
    	//$checkrows= mysqli_num_rows($check);
		
		
		/*if ($this->form_validation->run() == FALSE )
		{	 
			$data["this_item"] = $this_item ; 			
			$data["public_data"] = $this->admin_public->DATA;
			$data["disable_edit"] = false;						
			$this->load->view( $this->view_folder.'/'.'amd_edit',$data);	
			return ; 
		}
		
		else 
		{*/			 
			if ($this_item->ID()==0) 
			{
				 if ($this->admin_public->verify_access("new",1) == false ) return ;
			}
			else { if ($this->admin_public->verify_access("edit",1) == false ) return ; }
			
	
			//to add new values
			foreach ($this_item->business_data as $key => $value)
			{
				if (key_exists($key, $this->input->post())) // if ($this->input->post($key))
				{ 
					$this_item->business_data[$key] =$this->input->post($key);  	
				}
			}
					
			$this_item->validate();
		
			if ($this_item->success==FALSE)
			{
				//goto redo; 
				
				$data["this_item"] = $this_item ; 			
				$data["public_data"] = $this->admin_public->DATA;
				$data["disable_edit"] = false;		
				$template_folder = "_templates/".$this->template_name."/" ;  
				$this->load->helper($this->theme_helper)	;							
				$this->load->view( $this->view_folder.'/'.$this->concept .'_edit',$data);
				echo "<b><center>Error Validating Business Data</center></d><hr/>" ; 
				return ;
			}
			else
			{
				$this_item->business_data["sys_account_id"] = $this->admin_public->DATA["sys_account_id"];
				//print_r($this_item->business_data) ; 
				$this_item->update();
				$data["this_item"] = $this_item ; 			
				$data["public_data"] = $this->admin_public->DATA;
				$data["disable_edit"] = false;						
				//$this->load->view( $this->view_folder.'/'.'amd_edit',$data);	
			    redirect("/airticket/passengers/info/".$passenger_id);
				
				//echo "FINE: OK :"."<a msg=record_update_success /><ID>".$this_item->ID()."</ID>" ; 
			}					
			return;
		//}		
			
}

//***********************************************************************************************//
//*
//*										
//***************************** Functions to test something**************************************//
//*
//*

//______________________to read specific individual ticket_________________________________________________
public function ticket_read()
{
	$this_item = & $this->bi_air_ticket;      

    // load & read Existing object  ---------------------------------------------------- 
   
    $file = "104730_00.AIR" ; //ticket name

    $fullpath = "D:\\process tkt\\airline\\Air_back\\tkt\\" . $file ;//ticket path
	       
    $file_content = file_get_contents( $fullpath );

    $main_array = explode("\n", $file_content);
   
    echo "the content of main_array <br>" ;
    //echo "<pre>" ;
    //print_r($main_array) ; 

    return $main_array; 
}
//_______________________________________________________________________
/*public function try_commission()
{
	$new_arr = $this->ticket_read();
	//$new_arr = $this->multi_tkt_read();
	echo"<pre>";
	print_r($new_arr);
	
	
	foreach ($new_arr as $row) 
 	{
		$string_comm_rate = substr($row,0,2);
		$tkt_type = "";
		
		if($string_comm_rate == 'FM') // percent commission
		{
			//----------get the string-----------------
			$comm_full_string = strtok($row,";");
						
			preg_match_all('/\d+.\d{1,10}/', $comm_full_string, $comm_arr);
			echo">>>>>>>>>>>>>>>>>...<pre>";
			print_r($comm_arr);
			$comm_rate = implode('',$comm_arr[0]);
			echo"the commession >>> ".$comm_rate;
			
			
			//$comm_rate = substr($comm_full_string, 5);
			
			//$comm_rate = substr($row,5,2);
			//if(substr($comm_rate,-1) == ';')
				//$comm_rate = substr($row,5,1);
			

			$numeric_comm_rate = $comm_rate / 100 ;
			
			$comm_amount = - ($numeric_comm_rate * $base_fare) ;//negative as it's active (normal) tkt
			$round_comm_amount = round($comm_amount,2);
			//echo"<br>comm_amount >>>>".$round_comm_amount;	
			
			$vat_amount = 0.05 * (-$comm_amount);
			$round_vat_amount = round($vat_amount,2);
			//echo"<br>vat_amount >>>>".$round_vat_amount;
			
			$net_amount = $base_fare + $total_tax + $comm_amount + $vat_amount ;
			$round_net_amount = round($net_amount,2);
			//echo"<br>net_amount >>>>".$round_net_amount;
			
			$net = array("comm_amount"=>$round_comm_amount , 
						"vat_amount"=>$round_vat_amount , 
						"net_amount"=>$round_net_amount);
			
		}
		
		// A for Amount
		if($string_comm_rate == 'FM' && (strpos($row, 'A') !== false)) // found AMOUNT
		{
			$full_data = substr($row,5);
			$comm_amount = strtok($full_data , "A");
			echo"<br>comm_amount >>>>".$comm_amount;	
			
			$vat_amount = 0.05 * (-$comm_amount);
			$round_vat_amount = round($vat_amount,2);
			//echo"<br>vat_amount >>>>".$round_vat_amount;
			
			$net_amount = $base_fare + $total_tax + $comm_amount + $vat_amount ;
			$round_net_amount = round($net_amount,2);
			//echo"<br>net_amount >>>>".$round_net_amount;
			
			$net = array("comm_amount"=>$comm_amount , 
						"vat_amount"=>$round_vat_amount , 
						"net_amount"=>$round_net_amount);
		}
		
		//-----------------refund NET - VAT valued-------------------
				
		if($tkt_type == "Refund") // refund commission
		{
			$comm_rate = strtok(substr($row,2) , "P");
			
			$numeric_comm_rate = $comm_rate / 100 ;
			
			$comm_amount =  ($numeric_comm_rate * $fare_refund) ;//positive as it's refund
			$round_comm_amount = round($comm_amount,2);
			//echo"<br>comm_amount >>>>".$round_comm_amount;	
			
			$vat_amount = 0.05 * (-$comm_amount);//negative 
			$round_vat_amount = round($vat_amount,2);
			//echo"<br>vat_amount >>>>".$round_vat_amount;
			
			$net_amount = (-$fare_refund) + (-$total_tax_refund) + $comm_amount + $vat_amount + $cancel_fee;
			$round_net_amount = round($net_amount,2);
			//echo"<br>net_amount >>>>".$round_net_amount;
			
			$net = array("comm_amount"=>$round_comm_amount , 
						"vat_amount"=>$round_vat_amount , 
						"net_amount"=>$round_net_amount);
			
		}
		
	}//end of foreach
	
}*/

//________________________________________________________________________________

public function try_tax()
{
	
	$string = "asdfgh0000 mn mn";
	
	preg_match_all('/\d+.\d{0,9}/', $string, $tax_amount);// '/\d+/g'
	echo">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> <br><pre>";
	print_r($tax_amount);
	
	
	$original_reissue = "10;P1";
	if(strlen($original_reissue) == 10)
	echo"YES 10 digits = $original_reissue";

	else
	{
		$original_reissue = "0";
		echo"NOT 10 digits = $original_reissue";
	}
	
	
	
/*	
	$new_arr = $this->ticket_read();
	//$new_arr = $this->multi_tkt_read();
	echo"<pre>";
	print_r($new_arr);
	
	
	foreach ($new_arr as $row) 
 	{
 		$reissue_tax_string = substr($row,0,4);
		
		if($reissue_tax_string == 'KFTR')       
		{
			$full_data = substr($row,5);
			$tax_array = explode(";", $full_data);
			
			foreach ($tax_array as $key=>$value) 
			{
				if(!empty($value) && (substr($value,0,1) != "O"))
				{
					$tax_code_str = substr($value , 13 , 3);
					//$tax_code_arr[] = $tax_code_str;
					
					$tax_amount = substr($value , 4 , 9);
					//$tax_amount_arr[] = $tax_amount;	
					
					$final_tax_arr[] = array("TAX_AMOUNT"=>$tax_amount , 
								"TAX_CODE"=>$tax_code_str,
								"TAX_TKT_ID"=>$ticket_id,
								);
				}		
			}
			
			foreach($final_tax_arr as $key=>$value)
			{
				if(empty($value["TAX_AMOUNT"]) && empty($value["TAX_CODE"]))
					unset($final_tax_arr[$key]);
			}
			echo " tax amounts";
			echo"<pre>";
			print_r($final_tax_arr);
			echo "<br>";
			//return;
						
		}
		
		
	}//end of foreach
*/	
}

//************************************ End Of Test Functions*************************************//


}//end controller

