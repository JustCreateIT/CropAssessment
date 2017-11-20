<?php

class AdminController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // special authentication check for the entire controller: Note the check-ADMIN-authentication!
        // All methods inside this controller are only accessible for admins (= users that have role type 88)
        Auth::checkAdminAuthentication();
    }

    /**
     * This method controls what happens when you move to /admin or /admin/index in your app.
     */
    public function index()
    {
        $this->View->render('admin/index', array(
                'users' => UserModel::getPublicProfilesOfAllUsers(),
				'account_types' => DatabaseCommon::getAccountTypes(),
				'farms' => DatabaseCommon::getFarms()
        ));
    }
	
	public function link($user_id)
    {
		$farms = DatabaseCommon::getFarms();
		//$users = UserModel::getPublicProfilesOfAllUsers();
		if (isset($user_id)) {
			$user = UserModel::getPublicProfileOfUser($user_id);
		
			$unlinked = array();
			$i = 0;		
			//foreach ($users as $user) {
				foreach ($farms as $farm) {
					// does this farm_id/user_id combo exist in farm_users table?
					if ( !AdminModel::farmUserRowExists($user->user_id, $farm->farm_id) ){		
						$unlinked[$i] = new stdClass();		
						$unlinked[$i]->user_id = $user->user_id;
						$unlinked[$i]->farm_id = $farm->farm_id;
						$unlinked[$i]->farm_name = $farm->farm_name;
						$i++;
					}
				}
			//}
		}
		
		/*
		echo '<pre>';
		 	print_r($user);
		echo '</pre>';
		*/
		
		$this->View->render('admin/link', array(
			'user' => $user,
			'farm_users' => DatabaseCommon::getLinkedFarmUsers(),
			'farms_count' => count($farms),
			'unlinked_farm_users' => $unlinked
        ));
    }


    public function actionAccountSettings()
    {
        
		AdminModel::setUserAccountType( Request::post('user_account_type'), Request::post('user_id') );
		
		// To-do??? // combine these methods into a single database call
		AdminModel::setAccountSuspensionAndDeletionStatus(
            Request::post('suspension'), Request::post('softDelete'), Request::post('user_id')
        );

        Redirect::to("admin");
    }
	
	
	public function actionLinkFarmsToUser()
    {
		$linked_farms = Request::post('destination');
		$user_id = Request::post('user_id');
		
		AdminModel::setUserFarms( $linked_farms, $user_id );

        Redirect::to("admin");
    }	
}
