<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class passengers extends CI_Controller {

	public $template_name ; 
	public $theme_helper;
	
	public $concept  = "passenger" ; 
	
	public $controller = "airticket/passengers"; //controller path 	
		
	public $class_name = "bi_air_passenger" ; 
	public $class_path =  "airticket/bi_air_passenger" ; //model path
 
 	public $view_folder = "airticket"; //view path
	
	public $lang_file = "business/passenger_main" ;
	
	public $id_field  = "PASSENGER_ID"; 
	
    function __construct()
	{			
		parent::__construct();
	}
	public function index()
	{
		$this->master()  ; 
	}
	
	// main public loader & rights validator 		
public function _top_function($component_code,$second_time='no')
{
	$this->my_output->nocache(); 		
	$this->load->model("admin/admin_public") ; 	
	// start with the public items always 	
	 
	$this->lang->load("config/config",$this->admin_public->DATA["system_lang"]) ;
	
	if ($this->template_name=="") $this->template_name=r_langline("admin_template_name");
	if ($this->theme_helper=="") $this->theme_helper = r_langline("admin_template_helper"); 
	  
	$this->load->helper($this->theme_helper)	;    		
	$this->admin_public->DATA["template_folder"] =  "_templates/".$this->template_name."/" ;	
	
	if (!$this->admin_public->load($component_code)) return false;  

//----------------------------------------------------------------------
			 
	// needed in all the controllers functions,  
	// loaded any way ..............$this->load->model("admin/bi_user");

	$this->load->model($this->class_path);
	$this->load->model("airticket/bi_air_ticket") ;
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
	$this_view_file = "passenger_addedit"	; 
	
	
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
	
	$incoming_id = $this->uri->segment(4, 0);//passenger id in case filters not creat new ticket
	
	$this_item = & $this->bi_air_passenger;
	$this_item->clear();
	$this_item->read($incoming_id , "" ,1);
	
	$amd_id = $this_item->business_data["PASSENGER_TKT_ID"];
		
	$data["public_data"] = $this->admin_public->DATA;
	
	$data["PASSENGER_ID"] = $incoming_id;
	$data["PASSENGER_TKT_ID"] = $amd_id;
	$data["new_tkt_id_str"] = "";

	$data["this_concept"] = $this->concept ; 
	$data["this_controller"] = $this->controller ; 
	//$data["this_lang_folder"] = "trans"	;
	$data["this_id_field"] = $this->id_field ; 
	

	$this->load->view( $this_view , $data );
	
		
	
} 
 
//________________________________________________________________________________________
 
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
		
	$this_item = & $this->bi_air_passenger; 

	$data["list_table"] = $this_item->list_items_rtable( "all",array() ,"");
	
	$data["table_purpose"] =$purpose ; 
	
	$data["this_concept"] = "passenger" ;
	$data["this_controller"] = $this->controller; 

	$data["this_id_field"] = "PASSENGER_ID" ; 
	$data["this_name_field"] = "PASSENGER_TKT_NUMBER" ; 
	$data["this_name_field_ar"] = "PASSENGER_TKT_NUMBER" ;

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

//________________________________________________________________________________________

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
	$this->load->model("bi_air_ticket");
	$this->load->model("bi_air_segment");
	$this->load->model("bi_air_tax");

	// load & read Existing object  ----------------------------------------------------
		$this_item = & $this->bi_air_passenger; 
		$this_item->clear();

		$incoming_id = $this->uri->segment(4, 0);//3 is number of segements in the url after /index.php 
	 
		if ($incoming_id !=0) 
		{
			$this_item->Read($incoming_id,"",1);
			if (!$this_item->is_published )
			{
				//redirect with error not found object  
			}
		}
		
		$amd_id = $this_item->business_data["PASSENGER_TKT_ID" ] ; 
		
		$this_amd = & $this->bi_air_ticket; 
		$this_amd->clear();
		$this_amd->Read($amd_id,"",1);
	
		
		$data["this_controller"] = $this->controller; 	
	//	echo $this->concept ; 
		
		$this->form_validation->set_rules("PASSENGER_ID","PASSENGER ID", "required") ;
			
		
		if ($this->form_validation->run() == FALSE )
		{	 
			$data["this_item"] = $this_item ;
			$data["this_amd"] = $this_amd;
			$data["amd_id"] = $amd_id;			
			$data["public_data"] = $this->admin_public->DATA;
			$data["disable_edit"] = false;
		
			$this->load->view( $this->view_folder.'/'.'passenger_edit',$data);	
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
				
				$this_item->update();
				$data["this_item"] = $this_item ; 			
				$data["public_data"] = $this->admin_public->DATA;
				$data["disable_edit"] = false;						
				$this->load->view( $this->view_folder.'/'.'passenger_edit',$data);//redirect	
			
				echo "FINE: OK :"."<a msg=record_update_success /><ID>".$this_item->ID()."</ID>" ; 
			}					
			return;
		}		
			
	}

//________________________________________________________________________________________

/*public function ajax_table_tax($purpose="" , $show_box = 'no')
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
	
	$PASSENGER_TKT_ID = $this->uri->segment(4, 0);
	
	$this_item = & $this->bi_air_tax; 

	//$data["list_table"] = $this_item->list_items_rtable( "all",array() ,"");
	$data["list_table"] = $this_item->list_items_rtable( "tax_for_passenger",array("passenger_ticket_id"=>$PASSENGER_TKT_ID) ,"" ,"default");
	
	$data["table_purpose"] =$purpose ; 
	
	$data["this_concept"] = "passenger" ;
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
*/
//________________________________________________________________________________________
	
/*public function ajax_table_segment($purpose="" , $show_box = 'no')
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
	
	$PASSENGER_TKT_ID = $this->uri->segment(4, 0);
		
	$this_item = & $this->bi_air_segment; 

	//$data["list_table"] = $this_item->list_items_rtable( "all",array() ,"");
	$data["list_table"] = $this_item->list_items_rtable( "segment",array("ticket_id"=>$PASSENGER_TKT_ID) ,"" ,"default");
	
	$data["table_purpose"] =$purpose ; 
	
	$data["this_concept"] = "passenger" ;
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
*/
}//end controller

	