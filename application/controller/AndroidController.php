<?php

/**
 * LoginController
 * Controls everything that is authentication-related
 */
class AndroidController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class. The parent::__construct thing is necessary to
     * put checkAuthentication in here to make an entire controller only usable for logged-in users (for sure not
     * needed in the LoginController).
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Index, default action (shows the login form), when you do login/index
     */
    public function index()
    {
		
    }	
	/**
    * The login action, when you do login/login
    */
	public function login()
    {
		$login_response = ['result' => false]; 
		//$user_email = Request::post('user_email');
		//$user_password = Request::post('user_password');
        // perform the login method, put result (true or false) into $login_successful
        $login_successful = LoginModel::login(Request::post('user_email'), Request::post('user_password'), null);
		// check login status: 
        if ($login_successful) {				   
			$login_response['result'] = true;
			$login_response['user_first_name'] = Session::get('user_first_name');  
			$login_response['user_last_name'] = Session::get('user_last_name');
			$login_response['user_email'] = Session::get('user_email');
			$login_response['user_phone_number'] = (int)Session::get('user_phone_number');
        } else {
			$login_response['login_feedback'] = 'This sux shit!';
			//$login_response['login_feedback'] = Session::get('feedback_negative');
			//Session::set('feedback_negative', null);			
		}
		
		header("Content-Type: application/json;charset=utf-8");

		$json = json_encode($login_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
		if ($json === false) {
			// Avoid echo of empty string (which is invalid JSON), and
			// JSONify the error message instead:
			$json = json_encode(array("jsonError", json_last_error_msg()));
			if ($json === false) {
				// This should not happen, but we go all the way now:
				$json = '{"jsonError": "unknown"}';
			}
			// Set HTTP response status code to: 500 - Internal Server Error
			http_response_code(500);
		}
		
		echo strip_tags($json);
		
    }
	/*
	public function emailAddressAvailable($user_email)
	{
		// check if email already exists
        if (UserModel::doesEmailAlreadyExist($user_email)) {
            //Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN'));
            return false;
        }
		return true;		
    }
	*/
	public function register_action()
    {
		
		//$user_email = Request::post('user_email');
		$register_response = ['result' => false];		
		
		if (!UserModel::doesEmailAlreadyExist(strip_tags(Request::post('user_email')))) {
		//if (self::emailAddressAvailable($user_email)){			
			//$_POST['user_email'] = $user_email;				
			$registration_successful = RegistrationModel::registerNewUser();
		}
		if ($registration_successful) {
			$register_response["result"]  = true; 
		} 
		
		header("Content-Type: application/json;charset=utf-8");
		$json = json_encode($register_response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
		if ($json === false) {
			// Avoid echo of empty string (which is invalid JSON), and
			// JSONify the error message instead:
			$json = json_encode(array("jsonError", json_last_error_msg()));
			if ($json === false) {
				// This should not happen, but we go all the way now:
				$json = '{"jsonError": "unknown"}';
			}
			// Set HTTP response status code to: 500 - Internal Server Error
			http_response_code(500);
		}
		
		echo strip_tags($json);
   }	
}