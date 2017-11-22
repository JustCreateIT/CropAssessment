<?php

/**
 * This controller shows an area that's only visible for logged in users (because of Auth::checkAuthentication(); in line 16)
 */
class DashboardController extends Controller
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
     * This method controls what happens when you move to /dashboard/index in your app.
     */
    public function index() {
        /*
		$this->View->render('dashboard/index', array(
			'account_type' => DatabaseCommon::getAccountTypeByUserID(Session::get('user_id')),
            'farm_info' => DatabaseCommon::getFarmDetails(),
			'paddock_info' => DatabaseCommon::getPaddockDetails(),
            'sample_info' => DatabaseCommon::getSampleDetails()
        ));
*/		
		$this->View->render('dashboard/index');
	}
	
	public function selection_action()
    {
        $page = Request::post('dashboard');
		
		switch ($page) {
				case 'setup':
					Redirect::to('setup');
					break;	
				case 'enter':
					Redirect::to('collection');
					break;
				case 'view':
					Redirect::to('reports');
					break;
				case 'Modify Details': // administrator or owner user
				case 'View Farm Details': // standard user
				case 'config': // standard user
					Redirect::to('config');
					break;					
				default:
				// To-do (trap error)					
		}
    }
}
