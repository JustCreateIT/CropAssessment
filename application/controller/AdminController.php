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
		
		//		

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
	
	public function add()
    {
        $this->View->render('admin/add', array(                
			'account_types' => DatabaseCommon::getAccountTypes()				
        ));
    }	


	public function actionAddUser(){

		AdminUserModel::getCurrent()->user_first_name = strip_tags(Request::post('user_first_name'));
		AdminUserModel::getCurrent()->user_last_name = strip_tags(Request::post('user_last_name'));
		AdminUserModel::getCurrent()->user_email = strip_tags(Request::post('user_email_address'));
		AdminUserModel::getCurrent()->user_phone_number = strip_tags(Request::post('user_phone_number'));		
		AdminUserModel::getCurrent()->user_password = strip_tags(Request::post('user_password'));
		AdminUserModel::getCurrent()->user_account_type_id = strip_tags(Request::post('user_account_type_id'));	
		if(null!==(Request::post('send_details_user'))){
			AdminUserModel::getCurrent()->send_details_user = true;
		} 
		if(null!==(Request::post('send_details_self'))){
			AdminUserModel::getCurrent()->send_details_self = true;
			AdminUserModel::getCurrent()->current_user_id = Session::get('user_id');
			AdminUserModel::getCurrent()->current_user_email = Session::get('user_email');
		}
			
		
		/*
		$new_user->user_first_name = strip_tags(Request::post('user_first_name'));
        $new_user->user_last_name = strip_tags(Request::post('user_last_name'));        		
        $new_user->user_email = strip_tags(Request::post('user_email_address'));
		$new_user->user_password =  strip_tags(Request::post('user_password'));
		$new_user->user_account_type_id = Request::post('user_account_type_id');
		*/
/*
		echo '<pre>';
		 	var_dump(AdminUserModel::getCurrent(), true);
			//print_r($page);
		echo '</pre>';
*/		
		if (self::validateUserData()){
			$this->addNewUser();
		}
		// return and display any feedback messaging
		Redirect::to('admin/add');		
	}

	private function addNewUser(){
		// crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
        // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
        $user_password_hash = password_hash(AdminUserModel::getCurrent()->user_password, PASSWORD_DEFAULT);
		$user_id = null;
		$farm_id = null;
		
		// check if email already exists
        if (UserModel::doesEmailAlreadyExist(AdminUserModel::getCurrent()->user_email)) {
			// This user already exists so get user_id
			$user_id = UserModel::getUserIdByUserEmail(AdminUserModel::getCurrent()->user_email); 
			Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_EXISTS'));
			return false;
        }

        // generate random hash for email verification (40 char string)
        $user_activation_hash = sha1(uniqid(mt_rand(), true));

		// Current user_account_types [public=>1,standard=>5,owner=>9,administrator=>88]
		//$user_account_type=AdminUserModel::getCurrent()->user_account_type_id; 

        if (!RegistrationModel::writeNewUserToDatabase(
							AdminUserModel::getCurrent()->user_first_name,
							AdminUserModel::getCurrent()->user_last_name,
							$user_password_hash,
							AdminUserModel::getCurrent()->user_email, 
							AdminUserModel::getCurrent()->user_phone_number,
							time(), 
							$user_activation_hash, 
							AdminUserModel::getCurrent()->user_account_type_id)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CREATION_FAILED'));
            return false; // no reason not to return false here
        }		

		if (!$user_id) {
			// get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
			$user_id = UserModel::getUserIdByUserEmail(AdminUserModel::getCurrent()->user_email);		
		} 
        if (!$user_id) {
            Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
            return false;
        }

        // send verification email
        if (RegistrationModel::sendVerificationEmail($user_id, $farm_id, AdminUserModel::getCurrent()->user_email, $user_activation_hash)) {
            return true;
        }

        // if verification email sending failed: instantly delete the user
        RegistrationModel::rollbackRegistrationByUserId($user_id);
        Session::add('feedback_negative', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED'));
        return false;
	}

	
	private function validateUserData(){
		
		$user_email = AdminUserModel::getCurrent()->user_email;		
		$user_password = AdminUserModel::getCurrent()->user_password;
		
		// check email address and password
		if (!RegistrationModel::validateUserEmail($user_email, $user_email) AND 
			!RegistrationModel::validateUserPassword($user_password, $user_password)) {
			return false;		
		}
		return true;		
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
