<?php

/*
    require("password.php");

    $connect = mysqli_connect(Config::get('DB_HOST'), Config::get('DB_USER'), Config::get('DB_PASS'), Config::get('DB_NAME'));    

    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email_address = $_POST["email_address"];
    $phone_number = $_POST["phone_number"];	
    $password = $_POST["password"];

	function registerUser() {
        global $connect, $first_name, $last_name, $email_address, $phone_number, $password;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $statement = mysqli_prepare($connect, "INSERT INTO users (user_first_name, user_last_name, user_email, user_phone_number, user_password_hash) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($statement, "sssis", $first_name, $last_name, $email_address, $phone_number, $passwordHash);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement); 
    }
*/
    function emailAddressAvailable() {
		/*
        global $connect, $email_address;
        $statement = mysqli_prepare($connect, "SELECT * FROM users WHERE user_email = ?"); 
        mysqli_stmt_bind_param($statement, "s", $email_address);
        mysqli_stmt_execute($statement);
        mysqli_stmt_store_result($statement);
        $count = mysqli_stmt_num_rows($statement);
        mysqli_stmt_close($statement); 
        if ($count < 1){
            return true; 
        }else {
            return false; 
        }
		*/
		// check if email already exists
        if (UserModel::doesEmailAlreadyExist($email_address)) {
            //Session::add('feedback_negative', Text::get('FEEDBACK_USER_EMAIL_ALREADY_TAKEN'));
            $return = false;
        }
    }

    $response = array();
    $response["result"] = false; 

    if (emailAddressAvailable()){
        $response["result"] = RegistrationModel::registerNewUser();
        //$response["result"] = true;  
    }
    echo json_encode($response);
?>