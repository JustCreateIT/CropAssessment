<?php

class ConfigController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // special authentication check for the entire controller: Note the check-ADMIN-authentication!
        // All methods inside this controller are only accessible for admins (= users that have role type 88)
        // Auth::checkAdminAuthentication();
		
		// VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions. If all of your pages should only
        // be usable by logged-in users: Put this line into libs/Controller->__construct
        Auth::checkAuthentication();
    }

    /**
     * This method controls what happens when you move to /admin or /admin/index in your app.
     */
    public function index()
    {
		$this->View->render('config/index', array(
			'farm_details' => DatabaseCommon::buildJSONcollection(),		
            'farm_info' => DatabaseCommon::getFarmDetails(),
			'paddock_info' => DatabaseCommon::getPaddockDetails()	
        ));	
    }

    public function addFarmUser()
    {
        $farm_id = Session::get('_farm_id');
		
		$this->View->render('config/addFarmUser', array(
			'farm_details' => DatabaseCommon::buildJSONcollection()	
        ));
    }
	
	public function editFarm()
    {
        $farm_id = Session::get('_farm_id');
		
		$this->View->render('config/editFarm', array(
                'farm' => ConfigModel::getFarm($farm_id)
        ));
    }
	
	public function editPaddock()
    {
		$paddock_id = Session::get('_paddock_id');	
		
		$this->View->render('config/editPaddock', array(
			'paddock' => ConfigModel::getPaddock($paddock_id)
        ));
    }
	
	public function farms()
    {
        $user_id = Session::get('user_id');
		
		$this->View->render('config/farms', array(
                'farms' => ConfigModel::getFarmsByUserID($user_id)
        ));
    }

	public function paddocks()
    {
        
		//$user_id = Session::get('user_id');
		$farm_id = Session::get('_farm_id');
		
		$this->View->render('config/paddocks', array(
                'paddocks' => ConfigModel::getPaddocksByFarmID($farm_id)
        ));
    }
	
	public function crops()
    {
        
		//$user_id = Session::get('user_id');
		$paddock_id = Session::get('_paddock_id');
		$crop_id = Session::get('_crop_id');
		
		$this->View->render('config/crops', array(
                'paddock' => ConfigModel::getPaddock($paddock_id),
				'crops' => ConfigModel::getCropsByCropID($crop_id),
				'variety_data' => ConfigModel::getVarietyData(),
				'paddock_id' => $paddock_id,
				'crop_id' => $crop_id
        ));
    }

	
	public function viewWeather()
    {
		$paddock_id = Session::get('_paddock_id');
				
		$this->View->render('config/viewWeather', array(
			'paddock_google_place_id' => ConfigModel::getPaddockGooglePlaceID($paddock_id)
        ));
    }
	
	public function viewPaddock()
    {
		
		$farm_id = Session::get('_farm_id');
		$paddock_id = Session::get('_paddock_id');
		$crop_id = Session::get('_crop_id');					

		$this->View->render('config/viewPaddock', array(
			'management_zone_map' => $farm_id.'_'.$paddock_id.'_'.$crop_id.'.kmz',
			'paddock_google_latlong_paths' => json_decode(ConfigModel::getPaddockPolygonPathByID($paddock_id)),
			'farm_name' => DatabaseCommon::getFarmNameByID($farm_id),
			'paddock_name' => DatabaseCommon::getPaddockNameByID($paddock_id),
			'paddock_plant_date' => DatabaseCommon::getCropPlantDate($crop_id),
			'paddock_zones' => DatabaseCommon::getCropZoneCount($crop_id),			
			'paddock_zone_sample_count' => DatabaseCommon::getCropZoneSampleCountByCropID($crop_id)
        ));
	}		
	
	
	// Main page marshalling method for edit section/controller
	public static function selection_action(){
        
		$farm_id = Request::post('farm_id');
		$paddock_id = Request::post('paddock_id');
		$crop_id = Request::post('crop_id');		
		
		Session::set('_farm_id', null);
		Session::set('_paddock_id', null);
		Session::set('_crop_id', null);		
		Session::set('_farm_id', $farm_id);
		Session::set('_paddock_id', $paddock_id);
		Session::set('_crop_id', $crop_id);		
		
		$page = strtolower(Request::post('config'));
		
		switch ($page) {
				//case 'add user':
				//	Redirect::to('config/addFarmUser');
				//	//self::linkUser();
				//	break;
				case 'edit crop':
					//Redirect::to('edit/editPaddock');
					Redirect::to('config/crops');					
					//self::editPaddock();
					break;			
				case 'edit paddock':
					//Redirect::to('edit/editPaddock');
					Redirect::to('config/paddocks');					
					//self::editPaddock();
					break;
				case 'edit farm':
					//Redirect::to('edit/editFarm');
					Redirect::to('config/farms');					
					//self::editFarm();
					break;	
				case 'view paddock':
					Redirect::to('config/viewPaddock');
					break;			
				case 'view weather':								
					Redirect::to('config/viewWeather');
					break;				
				default:
				// To-do (trap error)
		}
	}
	
	/* deprecated - moved to setup controller
	public static function addFarmUser_action(){
		
		$farm_user_registration_successful = RegistrationModel::registerFarmUser();

		//if ($farm_user_registration_successful) {
			//Session::add('feedback_positive') = null;			
		Redirect::to('config/addFarmUser');
		//} else {
		//	Redirect::to('config/index');
		//}
	}
	*/
	
    /**
     * This method controls what happens when you move to /config/configUpdateDeleteFarm in your app.
     * Edits a farm (performs the editing after form submit).
     * POST request.
     */
    public function configUpdateDeleteFarm()
    {
	
		$user_id = Session::get('user_id');
		
		if (Request::post('submit') == 'Update') {	
			$success = ConfigModel::updateFarm(			
				Request::post('farm_name'),
				Request::post('farm_contact_firstname'),
				Request::post('farm_contact_lastname'),
				Request::post('farm_email_address'),
				Request::post('farm_phone_number'),
				Request::post('farm_id'));
		} else {
			$success = ConfigModel::deleteFarmByID(Request::post('farm_id'));
		}
		
		if ($success) {
			// Does this user still have any farms?
			$farms_exist = ConfigModel::farmsExist($user_id);
			if ($farms_exist) {
				// back to the farm editing page
				Redirect::to(Request::post('return_page'));
			} else {
				Redirect::to(Request::post('dashboard'));
			}			
		} else {
			// display errors & ...
			Redirect::to('config');
		}
		
        
    }
	
    /**
     * This method controls what happens when you move to /config/configUpdateDeletePaddock in your app.
     * Edits a farm (performs the editing after form submit).
     * POST request.
     */
    public function configUpdateDeletePaddock()
    {
		
		if (Request::post('submit') == 'Update') {		
			$success = ConfigModel::updatePaddockByID(
				Request::post('paddock_id'),
				Request::post('paddock_name'),
				Request::post('paddock_address'),
				Request::post('paddock_area'),
				Request::post('paddock_zone_count'),
				Request::post('paddock_zone_sample_count'),
				Request::post('paddock_bed_width'),
				Request::post('paddock_bed_rows'),
				Request::post('paddock_plant_spacing'),
				Request::post('paddock_target_population'),
				Request::post('variety_id'));			
		} else {
			$success = ConfigModel::deletePaddockByID(Request::post('paddock_id'));
		}
		
		if ($success) {
			$paddocks_exist = ConfigModel::paddocksExist(Session::get('_farm_id'));
			if ($paddocks_exist) {
				Redirect::to(Request::post('return_page'));			
			} else {
				Redirect::to('config');
			}
		} else {
			// display errors & ...
			Redirect::to('config');
		}
        
    }
	
	 public function configUpdateDeleteCrop()
    {
		
		if (Request::post('submit') == 'Update') {		
			$success = ConfigModel::updateCropByID(
				Request::post('crop_id'),
				Request::post('crop_zone_count'),
				Request::post('crop_zone_sample_count'),
				Request::post('crop_plant_date'),
				Request::post('crop_bed_width'),
				Request::post('crop_bed_rows'),
				Request::post('crop_plant_spacing'),
				Request::post('crop_target_population'),
				Request::post('variety_id'));			
		} else {
			$success = ConfigModel::deleteCropByID(Request::post('crop_id'));
		}
		
		if ($success) {
			$crops_exist = ConfigModel::cropsExist(Session::get('_paddock_id'));
			if ($crops_exist) {
				Redirect::to(Request::post('return_page'));			
			} else {
				Redirect::to('config');
			}
		} else {
			// display errors & ...
			Redirect::to('config');
		}
        
    }
	
	
	public static function getAddFarmUserFeedbackResponse(){
		
		View::renderWithoutHeaderAndFooter('config/response');		
		// delete these messages (as they are not needed anymore and we want to avoid to show them twice
        Session::set('feedback_positive', null);
        Session::set('feedback_negative', null);
			
	}	
	
}
