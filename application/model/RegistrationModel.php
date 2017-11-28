<?php

/**
 * Class RegistrationModel
 *
 * Everything registration-related happens here.
 */
class RegistrationModel
{
    /**
     * Handles the entire registration process for DEFAULT users (not for people who register with
     * 3rd party services, like facebook) and creates a new user in the database if everything is fine
     *
     * @return boolean Gives back the success status of the registration
     */
    public static function registerNewUser()
    {
        // clean the input
        //$user_name = strip_tags(Request::post('user_name'));
		// new fields ($user_first_name, $user_last_name, $user_phone_number)
		// deprecated fields ($user_name)
        $user_first_name = strip_tags(Request::post('user_first_name'));
        $user_last_name = strip_tags(Request::post('user_last_name'));        		
        $user_email = strip_tags(Request::post('user_email'));
        $user_email_repeat = strip_tags(Request::post('user_email_repeat'));
		$user_phone_number = strip_tags(Request::post('user_phone_number'));
        $user_password_new = Request::post('user_password_new');
        $user_password_repeat = Request::post('user_password_repeat');

        // stop registration flow if registrationInputValidation() returns false (= anything breaks the input check rules)
        //$validation_result = self::registrationInputValidation(Request::post('captcha'), $user_name, $user_password_new, $user_password_repeat, $user_email, $user_email_repeat);
		
        //$validation_result = self::registrationInputValidation(Request::post('captcha'), $user_name, $user_password_new, $user_password_repeat, $user_email, $user_email_repeat);

        $validation_result = self::registrationInputValidation(Request::post('captcha'), $user_password_new, $user_password_repeat, $user_email, $user_email_repeat);		
        if (!$validation_result) {
            return false;
        }

        // crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
        // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
        $user_password_hash = password_hash($user_password_new, PASSWORD_DEFAULT);

        // make return a bool variable, so both errors can come up at once if needed
        $return = true;

/*
        // check if username already exists
        if (UserModel::doesUsernameAlreadyExist($user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_ALREADY_TAKEN'));
            $return = false;
        }
*/
        // check if email already exists
        if (UserModel::doesEmailAlreadyExist($user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN'));
            $return = false;
        }

        // if Username or Email were false, return false
        if (!$return) return false;

        // generate random hash for email verification (40 char string)
        $user_activation_hash = sha1(uniqid(mt_rand(), true));

        // write user data to database
/*
        if (!self::writeNewUserToDatabase($user_name, $user_password_hash, $user_email, time(), $user_activation_hash)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CREATION_FAILED'));
            return false; // no reason not to return false here
        }
*/		

		// Default user_account_type=9 [user_account_name=>owner,user_account_id=>9]
		// Current user_account_types [public=>1,standard=>5,owner=>9,administrator=>88]
		$user_account_type=9;
        if (!self::writeNewUserToDatabase($user_first_name, $user_last_name, $user_password_hash, $user_email, $user_phone_number, time(), $user_activation_hash, $user_account_type)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CREATION_FAILED'));
            return false; // no reason not to return false here
        }		

        // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
        //$user_id = UserModel::getUserIdByUsername($user_name);
        $user_id = UserModel::getUserIdByUserEmail($user_email);	

	

        if (!$user_id) {
            Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
            return false;
        }

        // send verification email
        if (self::sendVerificationEmail($user_id, null, $user_email, $user_activation_hash)) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED'));
            return true;
        }

        // if verification email sending failed: instantly delete the user
        self::rollbackRegistrationByUserId($user_id);
        Session::add('feedback_negative', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED'));
        return false;
    }

    public static function registerFarmUser(){	
		
		$user_first_name = strip_tags(Request::post('user_first_name'));
        $user_last_name = strip_tags(Request::post('user_last_name'));        		
        $user_email = strip_tags(Request::post('user_email_address'));
        $user_email_repeat = $user_email;
		$user_phone_number = strip_tags(Request::post('user_phone_number'));
		$user_password = strip_tags(Request::post('user_password'));
        $user_password_repeat = $user_password;
		$farm_id = Request::post('farm_id');
		
		// check email address and password
		if (!self::validateUserEmail($user_email, $user_email_repeat) AND !self::validateUserPassword($user_password, $user_password_repeat)) {
			return false;		
		}
		
		// crypt the password with the PHP 5.5's password_hash() function, results in a 60 character hash string.
        // @see php.net/manual/en/function.password-hash.php for more, especially for potential options
        $user_password_hash = password_hash($user_password_new, PASSWORD_DEFAULT);

		// check if email already exists
		if (UserModel::doesEmailAlreadyExist($user_email)) {
			// This user already exists so get user_id
			$user_id = UserModel::getUserIdByUserEmail($user_email);
			// check if already linked to farm (farm_id)
			if(!ConfigModel::farmUserExists($farm_id, $user_id)){
				// previously verified so add this user to the farm_users table (linking farm_id with user_id)
				ConfigModel::addFarmUser($farm_id, $user_id);
			} else {
				Session::add('feedback_negative', Text::get('FEEDBACK_FARMUSER_ALREADY_LINKED'));
				return false;
			}            
		}

		// generate random hash for email verification (40 char string)
		$user_activation_hash = sha1(uniqid(mt_rand(), true));

		// Current user_account_types [public=>1,standard=>5,owner=>9,administrator=>88]
		$user_account_type=5; // Linked farm user user_account_type=5 [user_account_name=>standard,user_account_id=>5]		

		if (!self::writeNewUserToDatabase($user_first_name, $user_last_name, $user_password_hash, $user_email, $user_phone_number, time(), $user_activation_hash, $user_account_type)) {
			Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CREATION_FAILED'));
			return false; // no reason not to return false here
		}		

		if (!$user_id) {
			// get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
			$user_id = UserModel::getUserIdByUserEmail($user_email);		
		} 
		if (!$user_id) {
			Session::add('feedback_negative', Text::get('FEEDBACK_UNKNOWN_ERROR'));
			return false;
		}

		// send verification email
		if (self::sendVerificationEmail($user_id, $farm_id, $user_email, $user_activation_hash)) {
			return true;
		}

		// if verification email sending failed: instantly delete the user
		self::rollbackRegistrationByUserId($user_id);
		Session::add('feedback_negative', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED'));
		return false;
	}
	
	private function generateRandomPassword($length = 16){
		// DO NOT USE for secure passwords by itself
		// currently only used to create a default password when adding farm users to existing farms
		// this default password is hashed and the user is forced to change the password when logged 
		// in for the first time so should be fine anyway ...
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
		$chars_length = strlen(utf8_decode($chars)) - 1; 
		for ($i = 0; $i < $length; $i++) {
			$password .= $chars[mt_rand(0, $chars_length)];
		}
		return $password;	
	}
	
    /**
     * Validates the registration input
     *
     * @param $captcha
     * @param $user_name
     * @param $user_password_new
     * @param $user_password_repeat
     * @param $user_email
     * @param $user_email_repeat
     *
     * @return bool
     */
    //public static function registrationInputValidation($captcha, $user_name, $user_password_new, $user_password_repeat, $user_email, $user_email_repeat)
	
    public static function registrationInputValidation($captcha, $user_password_new, $user_password_repeat, $user_email, $user_email_repeat)	
    {
        $return = true;

        // perform all necessary checks
        if (!CaptchaModel::checkCaptcha($captcha)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_CAPTCHA_WRONG'));
            $return = false;
        }

        // if username, email and password are all correctly validated, but make sure they all run on first submit
/*
        if (self::validateUserName($user_name) AND self::validateUserEmail($user_email, $user_email_repeat) AND self::validateUserPassword($user_password_new, $user_password_repeat) AND $return) {
            return true;
        }
*/
		
        if (self::validateUserEmail($user_email, $user_email_repeat) AND self::validateUserPassword($user_password_new, $user_password_repeat) AND $return) {
            return true;
        }		

        // otherwise, return false
        return false;
    }

    /**
     * Validates the username
     *
     * @param $user_name
     * @return bool
     */

/*
    public static function validateUserName($user_name)
    {
        if (empty($user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_FIELD_EMPTY'));
            return false;
        }

        // if username is too short (2), too long (64) or does not fit the pattern (aZ09)
        if (!preg_match('/^[a-zA-Z0-9]{2,64}$/', $user_name)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        return true;
    }
	
*/	

    /**
     * Validates the email
     *
     * @param $user_email
     * @param $user_email_repeat
     * @return bool
     */
    public static function validateUserEmail($user_email, $user_email_repeat)
    {
        if (empty($user_email)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_FIELD_EMPTY'));
            return false;
        }

        if ($user_email !== $user_email_repeat) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_REPEAT_WRONG'));
            return false;
        }

        // validate the email with PHP's internal filter
        // side-fact: Max length seems to be 254 chars
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN'));
            return false;
        }

        return true;
    }

    /**
     * Validates the password
     *
     * @param $user_password_new
     * @param $user_password_repeat
     * @return bool
     */
    public static function validateUserPassword($user_password_new, $user_password_repeat)
    {
        if (empty($user_password_new) OR empty($user_password_repeat)) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_FIELD_EMPTY'));
            return false;
        }

        if ($user_password_new !== $user_password_repeat) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_REPEAT_WRONG'));
            return false;
        }

        if (strlen($user_password_new) < 6) {
            Session::add('feedback_negative', Text::get('FEEDBACK_PASSWORD_TOO_SHORT'));
            return false;
        }

        return true;
    }

    /**
     * Writes the new user's data to the database
     *
     * @param $user_first_name
	 * @param $user_last_name
     * @param $user_password_hash
     * @param $user_email
     * @param $user_phone_number
     * @param $user_creation_timestamp
     * @param $user_activation_hash
     *
     * @return bool
     */
    //public static function writeNewUserToDatabase($user_name, $user_password_hash, $user_email, $user_creation_timestamp, $user_activation_hash)
	public static function writeNewUserToDatabase($user_first_name, $user_last_name, $user_password_hash, $user_email, $user_phone_number, $user_creation_timestamp, $user_activation_hash, $user_account_type)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        // write new users data into database
/*
        $sql = "INSERT INTO users (user_name, user_password_hash, user_email, user_creation_timestamp, user_activation_hash, user_provider_type)
                    VALUES (:user_name, :user_password_hash, :user_email, :user_creation_timestamp, :user_activation_hash, :user_provider_type)";
        $query = $database->prepare($sql);
        $query->execute(array(':user_name' => $user_name,
                              ':user_password_hash' => $user_password_hash,
                              ':user_email' => $user_email,
                              ':user_creation_timestamp' => $user_creation_timestamp,
                              ':user_activation_hash' => $user_activation_hash,
                              ':user_provider_type' => 'DEFAULT'));
*/
        $sql = "INSERT INTO users (user_first_name, user_last_name, user_password_hash, user_email, user_phone_number, user_creation_timestamp, user_activation_hash, user_account_type, user_provider_type)
                    VALUES (:user_first_name, :user_last_name, :user_password_hash, :user_email, :user_phone_number, :user_creation_timestamp, :user_activation_hash, :user_account_type, :user_provider_type)";
        $query = $database->prepare($sql);
        $query->execute(array(':user_first_name' => $user_first_name,
							  ':user_last_name' => $user_last_name,
                              ':user_password_hash' => $user_password_hash,
                              ':user_email' => $user_email,
                              ':user_phone_number' => $user_phone_number,							  
                              ':user_creation_timestamp' => $user_creation_timestamp,
                              ':user_activation_hash' => $user_activation_hash,
							  ':user_account_type' => $user_account_type,
                              ':user_provider_type' => 'DEFAULT'));
							  
        $count =  $query->rowCount();
        if ($count == 1) {
            return true;
        }
        return false;
    }

    /**
     * Deletes the user from users table. Currently used to rollback a registration when verification mail sending
     * was not successful.
     *
     * @param $user_id
     */
    public static function rollbackRegistrationByUserId($user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("DELETE FROM users WHERE user_id = :user_id");
        $query->execute(array(':user_id' => $user_id));
    }

    /**
     * Sends the verification email (to confirm the account).
     * The construction of the mail $body looks weird at first, but it's really just a simple string.
     *
     * @param int $user_id user's id
     * @param string $user_email user's email
     * @param string $user_activation_hash user's mail verification hash string
     *
     * @return boolean gives back true if mail has been sent, gives back false if no mail could been sent
     */
    public static function sendVerificationEmail($user_id, $farm_id, $user_email, $user_activation_hash)
    {
        if(isset($farm_id) && !empty($farm_id)){
			$farm_id_link = urlencode($farm_id) . '/';
			$controllerView = Config::get('EMAIL_FARM_USER_VERIFICATION_URL');
			$mail_sent_success_message = Text::get('FEEDBACK_FARMUSER_VERIFICATION_MAIL_SENDING_SUCCESSFUL');
		} else {
			$farm_id_link = null;
			$controllerView = Config::get('EMAIL_VERIFICATION_URL');
			$mail_sent_success_message = Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_SUCCESSFUL');
		}
		
		/*
		$body = Config::get('EMAIL_VERIFICATION_CONTENT') . Config::get('URL') . Config::get('EMAIL_VERIFICATION_URL')
                . '/' . urlencode($user_id) . '/' . $farm_id_link . urlencode($user_activation_hash);
*/
		$body = Config::get('EMAIL_VERIFICATION_CONTENT') . Config::get('URL') . $controllerView . '/' 
		. urlencode($user_id) . '/' . $farm_id_link . urlencode($user_activation_hash);
		
		$mail = new Mail;
        $mail_sent = $mail->sendMail($user_email, Config::get('EMAIL_VERIFICATION_FROM_EMAIL'),
            Config::get('EMAIL_VERIFICATION_FROM_NAME'), Config::get('EMAIL_VERIFICATION_SUBJECT'), $body
        );

		//Session::add('feedback_negative', 'sendVerificationEmail: this is bullshit!. SMTPDebug:'.$mail->SMTPDebug());

        if ($mail_sent) {
            Session::add('feedback_positive', $mail_sent_success_message);
            return true;
        } else {
            Session::add('feedback_negative', Text::get('FEEDBACK_VERIFICATION_MAIL_SENDING_ERROR') . $mail->getError() );
            return false;
        }
    }

    /**
     * checks the email/verification code combination and set the user's activation status to true in the database
     *
     * @param int $user_id user id
     * @param string $user_activation_verification_code verification token
     *
     * @return bool success status
     */
    public static function verifyNewUser($user_id, $user_activation_verification_code, $farm_id=null)
    {
        if (isset($farm_id)){
			$account_activation_message = Text::get('FEEDBACK_FARMUSER_ACCOUNT_ACTIVATION_SUCCESSFUL');
		} else {
			$account_activation_message = Text::get('FEEDBACK_ACCOUNT_ACTIVATION_SUCCESSFUL');
		}
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "UPDATE users SET user_active = 1, user_activation_hash = NULL
                WHERE user_id = :user_id AND user_activation_hash = :user_activation_hash LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id, ':user_activation_hash' => $user_activation_verification_code));

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', $account_activation_message);
            return true;
        }

        Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_ACTIVATION_FAILED'));
        return false;
    }
	
}
