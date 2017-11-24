<?php

/**
 * This controller shows an area that's only visible for logged in users (because of Auth::checkAuthentication(); in line 16)
 */
class CollectionController extends Controller
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
     * This method controls what happens when you move to /collection/index in your app.
     */
    public function index()
    {
		/*
		$farm_details = DatabaseCommon::buildJSONcollection();		
		$page = strtolower(str_ireplace('.php', '', basename($_SERVER['QUERY_STRING']))); 
		echo '<pre>';
		 	print_r($farm_details);
			print_r($page);
		echo '</pre>';
		*/
		
		$this->View->render('collection/index', array(
			'farm_details' => DatabaseCommon::buildJSONcollection(),
        ));		
    }
	
	// Main page marshalling method // what page are we loading?	
	public static function selection_action(){
        
		$farm_id = Request::post('farm_id');
		$paddock_id = Request::post('paddock_id');
		$crop_id = Request::post('crop_id');
		$previous_page = Request::post('page_');

		
		Session::set('selected_farm_id', null);
		Session::set('selected_paddock_id', null);
		Session::set('selected_crop_id', null);	
		Session::set('previous_page', null);
		Session::set('selected_farm_id', $farm_id);
		Session::set('selected_paddock_id', $paddock_id);
		Session::set('selected_crop_id', $crop_id);
		Session::set('previous_page', $previous_page);
		
		$page = Request::post('collection');
		
		switch ($page) {
				case 'Enter Emergence Data':
					Redirect::to('collection/growthstage/1');
					break;			
				case 'Enter Three Leaf Data':								
					Redirect::to('collection/growthstage/2');
					break;
				case 'Enter Five Leaf Data':					
					Redirect::to('collection/growthstage/3');
					break;			
				case 'Enter Bulbing Data':								
					Redirect::to('collection/growthstage/4');
					break;
				case 'Enter Harvest Data':					
					Redirect::to('collection/growthstage/5');
					break;
				default:
				// To-do (trap error)
		}
	}	
	

	public function enterCollectionData_action()
    {		
		$zone_sample_plot_id = Request::post('sample_id');
		$sample_date = Request::post('sample_date');
		$sample_count = Request::post('sample_count');
		$sample_comment = Request::post('sample_comment');
		$sample_ela_score = Request::post('sample_ela_score');
		$sample_bulb_weight = Request::post('sample_bulb_weight');
		$zone_mean_leaf_number = Request::post('mean_leaf_number');
		//$sample_file = Request::post('sample_file');
		$growth_stage_id = Request::post('growth_stage_id');
		$zone_id = Request::post('zone_id');
		$crop_id = Request::post('crop_id');
		$paddock_id = Request::post('paddock_id');
		$farm_id = Request::post('farm_id');
		$controller_page = Session::get('previous_page');
		/*
		echo '<pre>';
			print_r($_POST);
			print_r($_FILES);			
		echo '</pre>';		
		*/
		
		
		foreach ($zone_sample_plot_id as $key => $value) {
			
			$flag = true;
			$no_error = false;
			
			switch ($growth_stage_id) {
				case 1: // emergence so no image collected
					//$emailImageSent = false;
					break;
				case 2: // three-leaf
				case 3: // five-leaf
				case 4: // bulbing
				case 5: // harvest
					// check if any images have been added
					if(isset($_FILES['sample_file']['name'][$key]) && !empty($_FILES['sample_file']['name'][$key])){		
						// send collected images to defined email address
						if (!CollectionModel::emailSampleImage(
							$zone_sample_plot_id[$key], $sample_date, $sample_count[$key], $sample_comment[$key], $sample_ela_score[$key],
							$sample_bulb_weight[$key], $zone_mean_leaf_number, $growth_stage_id, $zone_id, $crop_id, $paddock_id, $farm_id, $key)) 
						{
							$flag = false;				
						}						
					}
					break;
				default:
				// To-do (trap error)
			}
			
			// do we have data to modify or add
			if(!empty($sample_count[$key]) || !empty($sample_ela_score[$key]) || !empty($sample_bulb_weight[$key])){
				
				if ($flag) {					
					// check if a record for this sample location already exists and if it does simply update 
					// the existing record without providing any user feedback
					// if it does not exist add a new record
					if (DatabaseCommon::zoneSamplesExist($farm_id, $paddock_id, $crop_id, $zone_id, $zone_sample_plot_id[$key], $growth_stage_id)){
						// UPDATE existing sample record into the database								
						if (!CollectionModel::updateCollectionData(
							$zone_sample_plot_id[$key], $sample_date, $sample_count[$key], $sample_comment[$key], $sample_ela_score[$key], 
							$sample_bulb_weight[$key], $growth_stage_id, $zone_id, $crop_id, $paddock_id, $farm_id))
						{
							$no_error = true;				
						}	
					} else {			
						// INSERT new sample record into the database
						if (!CollectionModel::enterCollectionData(
							$zone_sample_plot_id[$key], $sample_date, $sample_count[$key], $sample_comment[$key], $sample_ela_score[$key],
							$sample_bulb_weight[$key], $growth_stage_id, $zone_id, $crop_id, $paddock_id, $farm_id))
						{
							$no_error = true;				
						}					
					}
					if ($growth_stage_id != 1) { // no leaf counts for emergence data					
						if (DatabaseCommon::zoneMeanLeafNumberExist($zone_id, $growth_stage_id)){
							// UPDATE existing sample record into the database								
							if (!CollectionModel::updateMeanLeafNumber($zone_id, $growth_stage_id, $zone_mean_leaf_number))
							{
								$no_error = true;				
							}	
						} else {			
							// INSERT new sample record into the database
							if (!CollectionModel::insertMeanLeafNumber($zone_id, $growth_stage_id, $zone_mean_leaf_number))
							{
								$no_error = true;				
							}					
						}
					}
				}		
			}
		}

		// Display success or failure messages from session
		if($no_error == true){
			Session::add('feedback_positive', Text::get('FEEDBACK_ZONE_DATA_ENTRY_SUCCESSFUL'));
		}
		// Potentially many samples so back to collection page		
		Redirect::to('collection/growthstage/'.$growth_stage_id);
		//Redirect::to('collection/'.$controller_page);
			
		
    }		
	



    /**
     * This method controls what happens when you move to /collection/enterData_action in your app.
     * Creates a new farm. This is usually the target of form submit actions.
     * POST request.
     */

	public function growthstage()
    {
		$farm_id = Session::get('selected_farm_id');
		$paddock_id = Session::get('selected_paddock_id');
		$crop_id = Session::get('selected_crop_id');
		$previous_page = Session::get('previous_page');
		$growth_stage_id = strtolower(str_ireplace('.php', '', basename($_SERVER['QUERY_STRING'])));
		$growth_stage_name = DatabaseCommon::getGrowthStageNameByID($growth_stage_id);
        $this->View->render('collection/'.$growth_stage_name, array(
			'farm_name' => DatabaseCommon::getFarmNameByID($farm_id),		
			'paddock_name' => DatabaseCommon::getPaddockNameByID($paddock_id),
			'crop_plant_date' => DatabaseCommon::getCropPlantDate($crop_id),
            'zone_info' => DatabaseCommon::getCropZones($crop_id),			
			'sample_info' => DatabaseCommon::getZoneSamples($crop_id),			
            'growth_stage_id' => $growth_stage_id,
			'farm_id' => $farm_id,
            'paddock_id' => $paddock_id,
			'crop_id' => $crop_id,
			'previous_page' => $previous_page
        ));				
    }


	
	public function enterData()
    {
		$farm_id = Session::get('selected_farm_id');
		$paddock_id = Session::get('selected_paddock_id');
		$crop_id = Session::get('selected_crop_id');
		$previous_page = Session::get('previous_page');
        //$this->View->render('collection/enterEmergence');
        $this->View->render('collection/enterEmergence', array(
			'farm_name' => DatabaseCommon::getFarmNameByID($farm_id),		
			'paddock_name' => DatabaseCommon::getPaddockNameByID($paddock_id),
			'crop_plant_date' => DatabaseCommon::getCropPlantDate($crop_id),
            //'zone_info' => DatabaseCommon::getPaddockZones($farm_id, $paddock_id),
			//'sample_info' => DatabaseCommon::getZoneSamples($farm_id, $paddock_id),
            //'zone_info' => DatabaseCommon::getPaddockZones($farm_id, $paddock_id),
            'zone_info' => DatabaseCommon::getCropZones($crop_id),			
			'sample_info' => DatabaseCommon::getZoneSamples($crop_id),			
            'growth_stage_id' => DatabaseCommon::getGrowthStageIDByName('emergence'),
			'farm_id' => $farm_id,
            'paddock_id' => $paddock_id,
			'crop_id' => $crop_id,
			'previous_page' => $previous_page
        ));				
    }
	
	
	public static function getFeedbackResponse(){
		
		View::renderWithoutHeaderAndFooter('collection/response');		
		// delete these messages (as they are not needed anymore and we want to avoid showing them twice
        Session::set('feedback_positive', null);
        Session::set('feedback_negative', null);
			
	}
		
}
