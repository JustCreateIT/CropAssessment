<?php

/**
 * This controller shows an area that's only visible for logged in users (because of Auth::checkAuthentication(); in line 16)
 */
class SetupController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions. If all of your pages should only
        // be usable by logged-in users: Put this line into libs/Controller->__construct
        Auth::checkAuthentication();
    }

    /**
     * This method controls what happens when you move to /farm/index in your app.
     */
    public function index()
    {
        //$this->View->render('setup/index');	
		$this->View->render('setup/index', array(
            'farm_info' => DatabaseCommon::getFarmDetails()
        ));		
    }
	
	public static function selection_action(){
        $page = Request::post('setup');
		
		switch ($page) {
			case 'farm':
				//$this->View->render('setup/createFarm');
				Redirect::to('setup/farm');
				break;			
			case 'paddock':
				//$this->View->render('setup/createPaddock');				
				Redirect::to('setup/paddock');
				break;
			case 'crop':
				//$this->View->render('setup/createPaddock');				
				//Redirect::to('setup/createCrop');
				Redirect::to('setup/crop');
				break;
			case 'user':
				//$this->View->render('setup/createPaddock');				
				//Redirect::to('setup/createCrop');
				Redirect::to('setup/user');
				break;				
			default:
			// To-do (trap error)					
		}
	}
	
	
	
	/**
     * This method controls what happens when you move to /farm/createFarm in your app.
     */
    public function farm()
    {
		$this->View->render('setup/create/farm', array(
            'user_first_name' => Session::get('user_first_name'),
            'user_last_name' => Session::get('user_last_name'),				
            'user_email' => Session::get('user_email'),
            'user_phone_number' => Session::get('user_phone_number')	
        ));		
    }
	
	/**
     * This method controls what happens when you move to /farm/createPaddock in your app.
     */
    public function paddock()
    {
        //$this->View->render('setup/createPaddock');
		$this->View->render('setup/create/paddock', array(
            'farms' => SetupModel::getFarmsByUserID(),
            'paddock_name' => SetupModel::GetPaddockValueFromSession('paddock_name'),
            'paddock_address' => SetupModel::GetPaddockValueFromSession('paddock_address'),		
            'paddock_area' => SetupModel::GetPaddockValueFromSession('paddock_area'),
            'paddock_longitude' => SetupModel::GetPaddockValueFromSession('paddock_longitude'),
            'paddock_latitude' => SetupModel::GetPaddockValueFromSession('paddock_latitude'),
            'paddock_zone_count' => SetupModel::GetPaddockValueFromSession('paddock_zone_count'),	
            'paddock_zone_sample_count' => SetupModel::GetPaddockValueFromSession('paddock_zone_sample_count'),
            'paddock_google_place_id' => SetupModel::GetPaddockValueFromSession('paddock_google_place_id')			
        ));		
    }
	
	/**
     * This method controls what happens when you move to /setup/drawPaddockPolygon in your app.
     */
    public function draw()
    {
        
		$this->View->render('setup/draw/paddock', array(		
			'paddock_google_latlong_paths' => SetupModel::GetPaddockMapValueFromSession('paddock_google_latlong_paths'),
			'paddock_google_area' => SetupModel::GetPaddockMapValueFromSession('paddock_google_area'),
			'paddock_google_place_id' => SetupModel::GetPaddockValueFromSession('paddock_google_place_id')
        ));
    }	

	/**
     * This method controls what happens when you move to /farm/createCrop in your app.
     */
    public function crop()
    {			
		$setup = true;
		$this->View->render('setup/create/crop', array(
			'farm_details' => DatabaseCommon::buildJSONcollection($setup),
			//'farms' => DatabaseCommon::getFarmDetails(),
			//'paddocks' => DatabaseCommon::getPaddockDetails(),			
            'crop_variety' => SetupModel::getCropVarieties()		 
        ));		
    }	
	/**
     * This method controls what happens when you move to /farm/createZones in your app.
     */	
	public function zones()
    {
		/* Zones were defined in the 'Paddock Information' screen
		 * so we need to retrieve this information in order to build the form
		 * @param int $paddock->paddock_zone_count - zone count for the current paddock
		 */
		 
        $this->View->render('setup/create/zones', array(
			'crop_zone_count' => SetupModel::GetCropValueFromSession('crop_zone_count')
        ));
    }

    public function user()
    {
        $farm_id = Session::get('farm_id');
		$this->View->render('setup/create/user', array(
			'farm_details' => DatabaseCommon::buildJSONcollection($setup = true)	
        ));
    }	
		
	
    /**
     * This method controls what happens when you move to /farm/createFarm_action in your app.
     * Creates a new farm. This is usually the target of form submit actions.
     * POST request.
     */
    public function createFarm_action()
    {
        SetupModel::createFarm(
						strip_tags(Request::post('farm_name')),
						strip_tags(Request::post('farm_contact_firstname')),
						strip_tags(Request::post('farm_contact_lastname')),
						strip_tags(Request::post('farm_email_address')),
						strip_tags(Request::post('farm_phone_number'))
						);
        Redirect::to('setup');
    }	

    /**
     * This method controls what happens when you move to /farm/createPaddock_action in your app.
     * Creates a new paddock. This is usually the target of form submit actions.
     * POST request.
     */
    public function createPaddock_action()
    {			
		//Session::add('feedback_positive', 'farm_id='.strip_tags(Request::post('farm_id')));
		//if (SetupModel::InsertIntoPaddockTable(	
		if (SetupModel::InsertIntoPaddockSession(	
						strip_tags(Request::post('farm_id')),
						strip_tags(Request::post('paddock_name')),
						strip_tags(Request::post('paddock_address')),
						strip_tags(Request::post('paddock_area')),
						strip_tags(Request::post('paddock_longitude')),
						strip_tags(Request::post('paddock_latitude')),
						strip_tags(Request::post('paddock_zone_count')),
						strip_tags(Request::post('paddock_zone_sample_count')),						
						strip_tags(Request::post('paddock_google_place_id'))))
		{
			// no errors so move to next form input page
			//Redirect::to('setup/createCrop');
			// Added Google mapping for users to add paddock polygon
			// no errors so move to next form input page
			Redirect::to('setup/draw');			
		} else {
			// was an error so display on current form input page
			Redirect::to('setup/paddock');			
		}
    }

    /**
     * This method controls what happens when you move to /setup/drawPaddock_action in your app.
     * Updates the current paddock with additional crop specific information. 
	 * This is usually the target of form submit actions POST request.
     */
    public function drawPaddock_action()
    {        
		//$latlng = Request::post('paddock_google_latlong_paths');
		//foreach($latlng as $value){
		//	$json = json_encode($value);
		//}
		
		//if (SetupModel::UpdateIntoPaddockTable(	
		if (SetupModel::InsertIntoPaddockMapSession(serialize(Request::post('paddock_google_latlong_paths')),
						strip_tags(Request::post('paddock_google_area')))
		And (SetupModel::PaddockInsertTransaction())){ // begin inserting records into the database
			// no errors so move to back to setup page
			Redirect::to('setup');
		} else {
			// was an error so display on current form input page
			Redirect::to('setup/drawPaddock');			
		}
    }	

    /**
     * This method controls what happens when you move to /setup/createCrop_action in your app.
     * Updates the current paddock with additional crop specific information. 
	 * This is usually the target of form submit actions POST request.
     */
    public function createCrop_action()
    {        
		/*
		$crop_zone_count = strip_tags(Request::post('crop_zone_count'));		
		echo '<pre>';
		 	print_r($crop_zone_count);
		echo '</pre>';
		*/
		
		//if (SetupModel::UpdateIntoPaddockTable(	
		if (SetupModel::InsertIntoCropSession(
						strip_tags(Request::post('farm_id')),
						strip_tags(Request::post('paddock_id')),
						strip_tags(Request::post('crop_plant_date')),
						strip_tags(Request::post('crop_bed_width')),
						strip_tags(Request::post('crop_bed_rows')),
						strip_tags(Request::post('crop_plant_spacing')),
						strip_tags(Request::post('crop_target_population')),
						strip_tags(Request::post('crop_zone_count')),
						strip_tags(Request::post('crop_zone_sample_count')),
						strip_tags(Request::post('crop_sample_plot_width')),						
						strip_tags(Request::post('crop_variety_id')))
						)
		{
			// no errors so move to next form input page
			Redirect::to('setup/zones');
		} else {
			// was an error so display on current form input page
			Redirect::to('setup/crop');			
		}
    }	

/**
     * This method controls what happens when you move to /setup/createZones_action in your app.
     * Initialises the creation of one or more zone records in the database (zone table). 
	 * This is usually the target of form submit actions POST request.
     */
    public function createZones_action()
    {
        $zone_count = Request::post('zone_count');
		$crop_zones = array();
		for ($i=0; $i<$zone_count;$i++){			
			$crop_zones[$i] = new stdClass();
			$crop_zones[$i]->zone_name = strip_tags(Request::post("zone_name_".($i+1)));
			$crop_zones[$i]->zone_paddock_percentage = Request::post("zone_paddock_percentage_".($i+1));			
			//$paddock_zone_area[$i] = Request::post("zone_area_".($i+1));
		}
		// Store form session details
		SetupModel::InsertIntoZoneSession($crop_zones);
		// begin inserting records into the database
		if (SetupModel::CropInsertTransaction()){
			// no errors so move to back to setup page
			Redirect::to('setup');
		} else {
			// was an error so display on current form input page
			Redirect::to('setup/zones');			
		}		
		
    }	

	public static function addFarmUser_action(){
		
		$farm_user_registration_successful = RegistrationModel::registerFarmUser();

		//if ($farm_user_registration_successful) {
			//Session::add('feedback_positive') = null;			
		Redirect::to('setup/user');
		//} else {
		//	Redirect::to('config/index');
		//}
	}	
}
