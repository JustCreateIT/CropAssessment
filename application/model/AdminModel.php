<?php

/**
 * Handles all data manipulation of the admin part
 */
class AdminModel
{
	
	 /**
     * Simply update the account tyoe for the user into the database
     *
     * @param $userAccountType
     * @return bool
     */
    public static function setUserAccountType($userAccountType, $user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
		$sql = "UPDATE users 
				SET user_account_type = :user_account_type
				WHERE user_id = :user_id";

        $query = $database->prepare($sql);
        $query->execute(array( ':user_account_type' => $userAccountType, ':user_id' => $user_id ));

        if ($query->rowCount() == 1) {
            //Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS'));
            return true;
        }
    }
	
	public static function setUserFarms($userFarms, $user_id)
    {
		
		$database = DatabaseFactory::getFactory()->getConnection();
		// remove any existing linked farms
		$sql = "DELETE from farm_users WHERE user_id = :user_id";		
		$query = $database->prepare($sql);
        $query->execute(array( ':user_id' => $user_id ));
		
		// insert updated linked farms		
		foreach ($userFarms as $farm_id){
		
			$sql = "INSERT INTO farm_users
					(farm_id, user_id) 
					VALUES 
			(:farm_id, :user_id)"; 

			$query = $database->prepare($sql);
			$query->execute(array( ':farm_id' => $farm_id, ':user_id' => $user_id ));
		}
		Session::add('feedback_positive', Text::get('FEEDBACK_USER_FARM_LINK_STATUS'));
		return true;
    }

	/**
     * Sets the deletion and suspension values
     *
     * @param $suspensionInDays
     * @param $softDelete
     * @param $userId
     */
    public static function setAccountSuspensionAndDeletionStatus($suspensionInDays, $softDelete, $userId)
    {

        // Prevent to suspend or delete own account.
        // If admin suspend or delete own account will not be able to do any action.
        if ($userId == Session::get('user_id')) {
            Session::add('feedback_negative', Text::get('FEEDBACK_ACCOUNT_CANT_DELETE_SUSPEND_OWN'));
            return false;
        }

        if ($suspensionInDays > 0) {
            $suspensionTime = time() + ($suspensionInDays * 60 * 60 * 24);
        } else {
            $suspensionTime = null;
        }

        // FYI "on" is what a checkbox delivers by default when submitted. Didn't know that for a long time :)
        if ($softDelete == "on") {
            $delete = 1;
        } else {
            $delete = 0;
        }

        // write the above info to the database
        self::writeDeleteAndSuspensionInfoToDatabase($userId, $suspensionTime, $delete);

        // if suspension or deletion should happen, then also kick user out of the application instantly by resetting
        // the user's session :)
        if ($suspensionTime != null OR $delete = 1) {
            self::resetUserSession($userId);
        }
    }

    /**
     * Simply write the deletion and suspension info for the user into the database, also puts feedback into session
     *
     * @param $userId
     * @param $suspensionTime
     * @param $delete
     * @return bool
     */
    private static function writeDeleteAndSuspensionInfoToDatabase($userId, $suspensionTime, $delete)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET user_suspension_timestamp = :user_suspension_timestamp, user_deleted = :user_deleted  WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(
                ':user_suspension_timestamp' => $suspensionTime,
                ':user_deleted' => $delete,
                ':user_id' => $userId
        ));

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS'));
            return true;
        }
    }
	
	
		
	
	public static function farmUserRowExists($user_id, $farm_id){
		$database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM farm_users
				WHERE farm_id = :farm_id
				AND user_id = :user_id";
	
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id, ':farm_id' => $farm_id));

		return ((int)$query->rowCount() > 0 ? true : false);	
	}
	

    /**
     * Kicks the selected user out of the system instantly by resetting the user's session.
     * This means, the user will be "logged out".
     *
     * @param $userId
     * @return bool
     */
    private static function resetUserSession($userId)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $query = $database->prepare("UPDATE users SET session_id = :session_id  WHERE user_id = :user_id LIMIT 1");
        $query->execute(array(
                ':session_id' => null,
                ':user_id' => $userId
        ));

        if ($query->rowCount() == 1) {
            Session::add('feedback_positive', Text::get('FEEDBACK_ACCOUNT_USER_SUCCESSFULLY_KICKED'));
            return true;
        }
    }
}
