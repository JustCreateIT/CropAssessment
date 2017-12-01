<?php

/**
 * RegisterController
 * Register new user
 */
class RegisterController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class. The parent::__construct thing is necessary to
     * put checkAuthentication in here to make an entire controller only usable for logged-in users (for sure not
     * needed in the RegisterController).
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Register page
     * Show the register form, but redirect to main-page if user is already logged-in
     */
    public function index()
    {
        if (LoginModel::isUserLoggedIn()) {
            Redirect::home();
        } else {
            $this->View->render('register/index', array('account_types' => DatabaseCommon::getAccountTypes()));
        }
    }

    /**
     * Register page action
     * POST-request after form submit
	 * (2016/11/11) - Added overload parameter - $standard_user - allowing for new users to be linked to existing farms:UserAccountType=standard(5) 
     */
    public function register_action()
    {		
		$registration_successful = RegistrationModel::registerNewUser();

		if ($registration_successful) {
			Redirect::to('login/index');
		} else {
			Redirect::to('register/index');
		}
    }

    /**
     * Verify user after activation mail link opened
     * @param int $user_id user's id
     * @param string $user_activation_verification_code user's verification token
     */
    public function verify($user_id, $user_activation_verification_code)
    {
        if (isset($user_id) && isset($user_activation_verification_code)) {
            RegistrationModel::verifyNewUser($user_id, $user_activation_verification_code);
            $this->View->render('register/verify');
        } else {
            Redirect::to('login/index');
        }
    }
	
	    /**
     * Verify user after activation mail link opened
     * @param int $user_id user's id
     * @param string $user_activation_verification_code user's verification token
     */
    public function verifyFarmUser($user_id, $farm_id, $user_activation_verification_code)
    {
        if (isset($user_id) && isset($farm_id) && isset($user_activation_verification_code)) {
            RegistrationModel::verifyNewUser($user_id, $user_activation_verification_code, $farm_id);
			
			if(!ConfigModel::farmUserExists($farm_id, $user_id)){
				// verified so add this user to the farm_users table (linking farm_id with user_id)
				ConfigModel::addFarmUser($farm_id, $user_id);
			}
			// If no errors then send the farm user to reset their password as we've only 
			// provided a random default password that no one knows
            $this->View->render('login/requestPasswordReset');
        } else {
            Redirect::to('login/index');
        }
    }

    /**
     * Generate a captcha, write the characters into $_SESSION['captcha'] and returns a real image which will be used
     * like this: <img src="......./login/showCaptcha" />
     * IMPORTANT: As this action is called via <img ...> AFTER the real application has finished executing (!), the
     * SESSION["captcha"] has no content when the application is loaded. The SESSION["captcha"] gets filled at the
     * moment the end-user requests the <img .. >
     * Maybe refactor this sometime.
     */
    public function showCaptcha()
    {
        CaptchaModel::generateAndShowCaptcha();
    }
}
