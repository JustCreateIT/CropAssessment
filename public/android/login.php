<?php
	/*
    require("password.php");

    $con = mysqli_connect("my_host", "my_user", "my_password", "my_database");    

    $username = $_POST["username"];
    $password = $_POST["password"];    

    $statement = mysqli_prepare($con, "SELECT * FROM user WHERE username = ?");
    mysqli_stmt_bind_param($statement, "s", $username);
    mysqli_stmt_execute($statement);
    mysqli_stmt_store_result($statement);
    mysqli_stmt_bind_result($statement, $colUserID, $colName, $colUsername, $colAge, $colPassword);    

    $response = array();
    $response["success"] = false;   

		/*
		$response = array();
        $response["first_name"] = Session::get('user_first_name');  
        $response["last_name"] = Session::get('user_last_name');
        $response["email_address"] = Session::get('user_email');
        $response["phone_number"] = Session::get('user_phone_number');    
		json_encode($response);
		
		*/   
	//*/
	/*
    while(mysqli_stmt_fetch($statement)){
        if (password_verify($password, $colPassword)) {
            $response["success"] = true;  
            $response["name"] = $colName;
            $response["age"] = $colAge;
        }
    }
	*/
	$response = array();    
	$response["result"] = LoginModel::login(Request::post('user_email'), Request::post('user_password'), null);
	$response["user_first_name"] = Session::get('user_first_name');  
    $response["user_last_name"] = Session::get('user_last_name');
    $response["user_email"] = Session::get('user_email');
    $response["user_phone_number"] = Session::get('user_phone_number'); 	
		
    echo json_encode($response);
?>