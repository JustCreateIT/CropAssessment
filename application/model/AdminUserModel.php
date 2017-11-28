<?php

class AdminUserModel {
	
	public $user_first_name;
	public $user_last_name;
	public $user_email;
	public $user_password;
	public $user_phone_number;
	public $user_account_type_id;
	public $send_details_user;
	public $send_details_self;
	public $current_user_id;
	public $current_user_email;

    private static $current;

    public static function getCurrent()
    {
        if (!self::$current) {
            self::$current = new AdminUserModel();
        }
        return self::$current;
    }
	
	

    /**
     * @param  $user_first_name
     *
     * @return \AdminNewUserModel
     */
    public function setUserFirstName($user_first_name)
    {
        $this->user_first_name = $user_first_name;

        return $this;
    }

    /**
     * @return 
     */
    public function getUserFirstName()
    {
        return $this->user_first_name;
    }

    /**
     * @param  $user_last_name
     *
     * @return \AdminNewUserModel
     */
    public function setUserLastName($user_last_name)
    {
        $this->user_last_name = $user_last_name;

        return $this;
    }

    /**
     * @return 
     */
    public function getUserLastName()
    {
        return $this->user_last_name;
    }

    /**
     * @param  $user_email
     *
     * @return \AdminNewUserModel
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;

        return $this;
    }

    /**
     * @return 
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * @param  $user_password
     *
     * @return \AdminNewUserModel
     */
    public function setUserPassword($user_password)
    {
        $this->user_password = $user_password;

        return $this;
    }

    /**
     * @return 
     */
    public function getUserPassword()
    {
        return $this->user_password;
    }
	
	

    /**
     * @param  $user_account_type_id
     *
     * @return \AdminNewUserModel
     */
    public function setUserAccountTypeId($user_account_type_id)
    {
        $this->user_account_type_id = $user_account_type_id;

        return $this;
    }

    /**
     * @return 
     */
    public function getUserAccountTypeId()
    {
        return $this->user_account_type_id;
    }
	
	

    /**
     * @param  $send_details_user
     *
     * @return \AdminUserModel
     */
    public function setSendDetailsUser($send_details_user)
    {
        $this->send_details_user = $send_details_user;

        return $this;
    }

    /**
     * @return 
     */
    public function getSendDetailsUser()
    {
        return $this->send_details_user;
    }

    /**
     * @param  $send_details_self
     *
     * @return \AdminUserModel
     */
    public function setSendDetailsSelf($send_details_self)
    {
        $this->send_details_self = $send_details_self;

        return $this;
    }

    /**
     * @return 
     */
    public function getSendDetailsSelf()
    {
        return $this->send_details_self;
    }
	
	 /**
     * @param  $current_user_id
     *
     * @return \AdminUserModel
     */
    public function setCurrentUserId($current_user_id)
    {
        $this->current_user_id = $current_user_id;

        return $this;
    }

    /**
     * @return 
     */
    public function getCurrentUserId()
    {
        return $this->current_user_id;
    }
	
	/**
     * @param  $current_user_email
     *
     * @return \AdminUserModel
     */
    public function setCurrentUserEmail($current_user_email)
    {
        $this->current_user_email = $current_user_email;

        return $this;
    }

    /**
     * @return 
     */
    public function getCurrentUserEmail()
    {
        return $this->current_user_email;
    }

    /**
     * @param  $user_phone_number
     *
     * @return \AdminUserModel
     */
    public function setUserPhoneNumber($user_phone_number)
    {
        $this->user_phone_number = $user_phone_number;

        return $this;
    }

    /**
     * @return 
     */
    public function getUserPhoneNumber()
    {
        return $this->user_phone_number;
    }
}
