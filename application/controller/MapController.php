<?php

/**
 * This controller shows an area that's only visible for logged in users (because of Auth::checkAuthentication(); in line 16)
 */
class MapController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // this entire controller should only be visible/usable by logged in users, so we put authentication-check here
        Auth::checkAuthentication();
    }

    /**
     * This method controls what happens when you move to /mapping/index in your app.
     */
    public function index()
    {
		$this->View->render('map/index', array(
			'farm_details' => DatabaseCommon::buildJSONcollection(),
            'farm_info' => DatabaseCommon::getFarmDetails(),
			'paddock_info' => DatabaseCommon::getPaddockDetails()			
        ));	
    }
	
	public function viewWeather()
    {
		$paddock_id = Session::get('map_paddock_id');
		$this->View->render('map/viewWeather', array(
			'paddock_google_place_id' => MapModel::getPaddockGooglePlaceID($paddock_id)
        ));
    }
	
	public function viewPaddock()
    {
		
		$farm_id = Session::get('map_farm_id');
		$paddock_id = Session::get('map_paddock_id');
		$crop_id = Session::get('map_crop_id');					
		/*		
		Session::add('feedback_positive', 'paddock_google_latlong_paths: '.json_decode(MapModel::getPaddockPolygonPathByID($paddock_id)));
		Session::add('feedback_positive', 'farm_name: '.DatabaseCommon::getFarmNameByID($farm_id));
		Session::add('feedback_positive', 'paddock_name: '.DatabaseCommon::getPaddockNameByID($paddock_id));
		Session::add('feedback_positive', 'paddock_zones: '.DatabaseCommon::getPaddockZoneCount($farm_id,$paddock_id));		
		Session::add('feedback_positive', 'paddock_zone_sample_count: '.DatabaseCommon::getPaddockZoneSampleCountByPaddockID($farm_id,$paddock_id));
		*/
		$this->View->render('map/viewPaddock', array(
			'management_zone_map' => $farm_id.'_'.$paddock_id.'.kmz',
			'paddock_google_latlong_paths' => json_decode(MapModel::getPaddockPolygonPathByID($paddock_id)),
			'farm_name' => DatabaseCommon::getFarmNameByID($farm_id),
			'paddock_name' => DatabaseCommon::getPaddockNameByID($paddock_id),
			//'paddock_zones' => DatabaseCommon::getPaddockZoneCount($farm_id,$paddock_id),
			'paddock_zones' => DatabaseCommon::getCropZoneCount($crop_id),			
			//'paddock_zone_sample_count' => DatabaseCommon::getPaddockZoneSampleCountByPaddockID($farm_id,$paddock_id)
			'paddock_zone_sample_count' => DatabaseCommon::getCropZoneSampleCountByCropID($crop_id)	
		
        ));
		

    }
	
	// Main page marshalling method for map section/controller
	public static function selection_action(){
        
		$farm_id = Request::post('farm_id');
		$paddock_id = Request::post('paddock_id');
		$crop_id = Request::post('crop_id');
		
		Session::set('map_farm_id', null);
		Session::set('map_paddock_id', null);
		Session::set('map_farm_id', $farm_id);
		Session::set('map_paddock_id', $paddock_id);
		Session::set('map_crop_id', $crop_id);		
		
		$page = Request::post('view');
		
		switch ($page) {
				case 'View Paddock Map':
					Redirect::to('map/viewPaddock');
					break;			
				case 'View Weather Information':								
					Redirect::to('map/viewWeather');
					break;
				default:
				// To-do (trap error)
		}
	}
	
}
