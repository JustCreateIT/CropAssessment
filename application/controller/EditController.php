<?php

class EditController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // special authentication check for the entire controller: Note the check-ADMIN-authentication!
        // All methods inside this controller are only accessible for admins (= users that have role type 7)
        Auth::checkAdminAuthentication();
    }

    /**
     * This method controls what happens when you move to /admin or /admin/index in your app.
     */
    public function index()
    {
		$this->View->render('edit/index', array(
			'farm_details' => DatabaseCommon::buildJSONcollection(),		
            'farm_info' => DatabaseCommon::getFarmDetails(),
			'paddock_info' => DatabaseCommon::getPaddockDetails(),		
        ));	
    }

    public function linkUser()
    {
        $farm_id = Session::get('edit_farm_id');
		
		$this->View->render('edit/linkUser', array(
                'users' => UserModel::getPublicProfilesOfAllUsers(),
				'account_types' => DatabaseCommon::getAccountTypes()
        ));
    }
	
	public function editFarm()
    {
        $farm_id = Session::get('edit_farm_id');
		
		$this->View->render('edit/editFarm', array(
                'farm' => EditModel::getFarm($farm_id)
        ));
    }
	
	public function editPaddock()
    {
		$paddock_id = Session::get('edit_paddock_id');	
		
		$this->View->render('edit/editPaddock', array(
			'paddock' => EditModel::getPaddock($paddock_id)
        ));
    }
	
	public function farms()
    {
        $user_id = Session::get('user_id');
		
		$this->View->render('edit/farms', array(
                'farms' => EditModel::getFarmsByUserID($user_id)
        ));
    }

	public function paddocks()
    {
        
		$user_id = Session::get('user_id');
		$farm_id = Session::get('edit_farm_id');
		
		$this->View->render('edit/paddocks', array(
                'paddocks' => EditModel::getPaddocksByFarmID($farm_id)
				
        ));
    }		
	
	
	// Main page marshalling method for edit section/controller
	public static function selection_action(){
        
		$farm_id = Request::post('farm_id');
		$paddock_id = Request::post('paddock_id');

		
		Session::set('edit_farm_id', null);
		Session::set('edit_paddock_id', null);
		Session::set('edit_farm_id', $farm_id);
		Session::set('edit_paddock_id', $paddock_id);
		
		$page = strtolower(Request::post('edit'));
		
		switch ($page) {
				case 'link users to your farm':
					Redirect::to('edit/linkUser');
					//self::linkUser();
					break;			
				case 'edit paddock information':
					//Redirect::to('edit/editPaddock');
					Redirect::to('edit/paddocks');					
					//self::editPaddock();
					break;
				case 'edit farm information':
					//Redirect::to('edit/editFarm');
					Redirect::to('edit/farms');					
					//self::editFarm();
					break;					
				default:
				// To-do (trap error)
		}
	}

    /**
     * This method controls what happens when you move to /farm/editSaveFarm in your app.
     * Edits a farm (performs the editing after form submit).
     * POST request.
     */
    public function editSaveFarm()
    {
        $return_page = Request::post('return_page');
		
		EditModel::updateFarm(			
			Request::post('farm_name'),
			Request::post('farm_contact_firstname'),
			Request::post('farm_contact_lastname'),
			Request::post('farm_email_address'),
			Request::post('farm_phone_number'),
			Request::post('farm_id'));
		if ($return_page == "farms"){
			Redirect::to('edit/farms');
		} else {
			Redirect::to('edit');
		}
        
    }
	
}
