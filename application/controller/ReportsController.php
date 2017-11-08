<?php

/**
 * This controller shows an area that's only visible for logged in users (because of Auth::checkAuthentication(); in line 16)
 */
class ReportsController extends Controller
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
     * This method controls what happens when you move to /reports/index in your app.
     */
    public function index()
    {
        //$this->View->render('reports/index');
		$this->View->render('reports/index', array(
			'farm_details' => DatabaseCommon::buildJSONcollection(),		
            'farm_info' => DatabaseCommon::getFarmDetails(),
			'paddock_info' => DatabaseCommon::getPaddockDetails(),
			'report_info' => DatabaseCommon::getPaddockSamplesByGrowthStage()
        ));			
    }
	
	// Main page marshalling method
	public static function selection_action(){
		
		$farm_id = Request::post('farm_id');
		$paddock_id = Request::post('paddock_id');
		$crop_id = Request::post('crop_id');
		
		Session::set('selected_farm_id', null);
		Session::set('selected_paddock_id', null);
		Session::set('selected_farm_id', $farm_id);
		Session::set('selected_paddock_id', $paddock_id);
		Session::set('selected_crop_id', $crop_id);
		
        $page = Request::post('reports');
		
		switch ($page) {
				case 'View Emergence Assessment':
					$view = 'emergence';					
					break;			
				case 'View Three Leaf Assessment':
					$view = 'threeleaf';
					break;
				case 'View Five Leaf Assessment':
					$view = 'fiveleaf';
					break;			
				case 'View Bulbing Assessment':
					$view = 'bulbing';
					break;
				case 'View Harvest Assessment':
					$view = 'harvest';
					break;	
				default:
				// To-do (trap error)		
		}
		
		Session::set('report_page', $view);	
		Redirect::to('reports/'.$view);
		
	}	
	
	/**
     * This method controls what happens when you move to /reports/emergence in your app.
     */
    public function emergence()
    {
		$page = Session::get('report_page');
		self::viewAssessment($page);
    }
	
	/**
     * This method controls what happens when you move to /reports/threeleaf in your app.
     */
    public function threeleaf()
    {
		$page = Session::get('report_page');
		self::viewAssessment($page);
    }

	/**
     * This method controls what happens when you move to /reports/fiveleaf in your app.
     */
    public function fiveleaf()
    {
		$page = Session::get('report_page');
		self::viewAssessment($page);
    }	

	/**
     * This method controls what happens when you move to /reports/bulbing in your app.
     */
    public function bulbing()
    {
		$page = Session::get('report_page');
		self::viewAssessment($page);
    }	
	/**
     * This method controls what happens when you move to /reports/harvest in your app.
     */
    public function harvest()
    {
		$page = Session::get('report_page');
		self::viewAssessment($page);	
    }	


    public function viewAssessment($page)
    {
		$crop_id = Session::get('selected_crop_id');
		$growth_stage_name = Session::get('report_page');
		$growth_stage_id = DatabaseCommon::getGrowthStageIDByName($growth_stage_name);
		
		// check if samples available for the crop at that growth stage
		if (DatabaseCommon::cropSamplesExist($crop_id, $growth_stage_id)){
			
			$report_data = new stdClass();
			
			$report_data->report_name = $growth_stage_name;
			$report_data->farm_id = Session::get('selected_farm_id');
			$report_data->paddock_id = Session::get('selected_paddock_id');
			$report_data->crop_id = $crop_id;
			$report_data->growth_stage_id = $growth_stage_id;
			$report_data->chartData = ReportsModel::getChartDataByZones($report_data->farm_id, $report_data->paddock_id, $crop_id, $growth_stage_id);
			$report_data->chartAverages = ReportsModel::getChartMeanYieldByZones($report_data->farm_id, $report_data->paddock_id, $crop_id, $growth_stage_id);
			$report_data->hAxisTitle = ReportsModel::gethAxisTitle($growth_stage_id);
			$report_data->gridLines = Session::get('gridLines');
			$report_data->vAxisTitle = ReportsModel::getvAxisTitle($growth_stage_id);		
			$report_data->vAxisMin = Session::get('vAxisMin');	
			$report_data->vAxisMax = Session::get('vAxisMax');				
		 
			$this->View->render('reports/'.$page, array(
				'farm_id' => $report_data->farm_id,
				'paddock_id' => $report_data->paddock_id,
				'crop_id' => $crop_id,
				'report_name' => $growth_stage_name,
				'report_header' => ReportsModel::assessmentReportHeader($growth_stage_name),
				'html_report_table' => ReportsModel::buildHTMLReporttable($report_data->farm_id, $report_data->paddock_id, $crop_id, $growth_stage_id),				
				'report_hidden' => ReportsModel::assessmentReportHidden($report_data),
				'bottom_navigation' => ReportsModel::assessmentBottomNavigation(),
				'growth_stage_id' => $growth_stage_id
			));
		}else{
			Session::add('feedback_negative', Text::get('FEEDBACK_ASSESSMENT_REPORT_UNAVAILABLE'));
			Redirect::to('reports/index');
		}
    }

	public static function getFeedbackResponse(){
		
		View::renderWithoutHeaderAndFooter('reports/index');		
		// delete these messages (as they are not needed anymore and we want to avoid to show them twice
        Session::set('feedback_positive', null);
        Session::set('feedback_negative', null);
			
	}
		

	/* Form post from the assessment pages */
    public function assessment_action()
    {
		$page = Request::post('assessment');
		$user_email = Session::get('user_email');		
		$farm_id = Request::post('farm_id');
		$paddock_id = Request::post('paddock_id');
		$crop_id = Request::post('crop_id');
		$growth_stage_id = Request::post('growth_stage_id');
		
		//Session::add('feedback_positive', $page));
		
		switch ( strtolower( substr( $page, 0, 4 ) ) ) {
			case 'send':
				ReportsModel::createEmail($user_email);
				Redirect::to('reports/'.Session::get('page'));
				break;
			case 'save':
				ReportsModel::savePDF(Session::get('report_page'));					
				break;				
			case 'expo':
				ReportsModel::exportCSV($farm_id, $paddock_id, $crop_id, $growth_stage_id);	
				break;
			default:
			// To-do (trap error)
		}
	}	
}		
