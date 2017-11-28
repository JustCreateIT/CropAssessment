<?php

/**
 * UserController
 * Controls everything that is user-related
 */
class UserController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class.
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions.
        Auth::checkAuthentication();
    }

    /**
     * Show user's PRIVATE profile
     */
    public function index()
    {
        $this->View->render('user/index', array(
            //'user_name' => Session::get('user_name'),
            'user_first_name' => Session::get('user_first_name'),
            'user_last_name' => Session::get('user_last_name'),				
            'user_email' => Session::get('user_email'),
            'user_phone_number' => Session::get('user_phone_number'),				
            'user_gravatar_image_url' => Session::get('user_gravatar_image_url'),
            'user_avatar_file' => Session::get('user_avatar_file'),
            'user_account_type' => Session::get('user_account_type')
        ));		

    }


	public function add(){
		
		/*
		echo '<pre>';
		var_dump($_SESSION);
		var_dump(DatabaseCommon::getFarmDetails());
		echo '</pre>';
		*/
		
		$this->View->render('user/add', array( 
			'farm_details' => DatabaseCommon::getFarmDetails(),
			'account_types' => DatabaseCommon::getAccountTypes()				
        ));	
	}

    	
    /**
     * Show edit-my-user-details page
     */
    public function editUser()
    {
        $this->View->render('user/editUser', array(
            //'user_name' => Session::get('user_name'),
            'user_firstname' => Session::get('user_first_name'),
            'user_lastname' => Session::get('user_last_name'),				
            'user_email' => Session::get('user_email'),
            'user_phone_number' => Session::get('user_phone_number')
        ));		

    }

    /**
     * Edit user's name (first/last), email address and phone number (perform the real action after form has been submitted)
     */
    // make this POST
    public function editUser_action()
    {
        
		$firstname = Request::post('user_firstname');
		$lastname = Request::post('user_lastname');
		$email = Request::post('user_email');
		$phone = Request::post('user_phone_number');

		UserModel::editUser($firstname, $lastname, $email, $phone);
		// display feedback (errors/success)
        Redirect::to('user/editUser');
    }

    // make this POST
    public function actionAddUser()
    {
        
		RegistrationModel::registerFarmUser();
		// display feedback (errors/success)
        Redirect::to('user/add');
    }
	

    /**
     * Edit avatar
     */
    public function editAvatar()
    {
        $this->View->render('user/editAvatar', array(
            'avatar_file_path' => AvatarModel::getPublicUserAvatarFilePathByUserId(Session::get('user_id'))
        ));
    }

    /**
     * Perform the upload of the avatar
     * POST-request
     */
    public function uploadAvatar_action()
    {
        AvatarModel::createAvatar();
        Redirect::to('user/editAvatar');
    }

    /**
     * Delete the current user's avatar
     */
    public function deleteAvatar_action()
    {
        AvatarModel::deleteAvatar(Session::get("user_id"));
        Redirect::to('user/editAvatar');
    }

    /**
     * Show the change-account-type page
     */
    public function changeUserRole()
    {
        $this->View->render('user/changeUserRole');
    }

    /**
     * Perform the account-type changing
     * POST-request
     */
    public function changeUserRole_action()
    {
        if (Request::post('user_account_upgrade')) {
            // "2" is quick & dirty account type 2, something like "premium user" maybe. you got the idea :)
            UserRoleModel::changeUserRole(2);
        }

        if (Request::post('user_account_downgrade')) {
            // "1" is quick & dirty account type 1, something like "basic user" maybe.
            UserRoleModel::changeUserRole(1);
        }

        Redirect::to('user/changeUserRole');
    }

    /**
     * Password Change Page
     */
    public function changePassword()
    {
        $this->View->render('user/changePassword');
    }

    /**
     * Password Change Action
     * Submit form, if retured positive redirect to index, otherwise show the changePassword page again
     */
    public function changePassword_action()
    {
        $result = PasswordResetModel::changePassword(
            Session::get('user_name'), Request::post('user_password_current'),
            Request::post('user_password_new'), Request::post('user_password_repeat')
        );

        if($result)
            Redirect::to('user/index');
        else
            Redirect::to('user/changePassword');
    }
}
