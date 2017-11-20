<!doctype html>
<html>
<head>
    <title>Onions NZ: Management Action Zones Dashboard</title>
    <!-- META -->
	<!--<meta name="viewport" content="width=980">
	<meta name="viewport" content="width=device-width"><!--<meta name="viewport" content="width=980">-->
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, min-width=500"/>-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!--<script type="text/javascript" src="viewport-min-width.js"></script>-->		
    <meta charset="utf-8">
    <!-- send empty favicon fallback to prevent user's browser hitting the server for lots of favicon requests resulting in 404s -->
    <!--<link rel="icon" href="data:;base64,=">-->
    <link rel="icon" href="<?php echo Config::get('URL'); ?>images/favicon.ico">
    <!-- CSS -->
	<link rel="stylesheet" href="<?php echo Config::get('URL'); ?>css/style.css">
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css"> 
	  <!-- scripts -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>	
    <script src="http://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <?php 	
    $page = strtolower(str_ireplace('.php', '', basename($_SERVER['QUERY_STRING']))); 

	//echo print_r($page, true);
	
	// responsive scripts 
	echo '<script src="'.Config::get('URL').'scripts/jquery-responsiveTables.js"></script>';
	echo "\r\n\t";
	echo '<script src="'.Config::get('URL').'scripts/jquery.responsiveText.js"></script>';
	echo "\r\n\t";
	echo '<script src="'.Config::get('URL').'scripts/responsive_report.js"></script>';
	echo "\r\n\t";

	
	// simply adds the scripts required for the selected page view
	if (strpos($_SERVER['QUERY_STRING'], 'url=admin/link/') !== false ) {
		//echo print_r(strpos($_SERVER['QUERY_STRING'], 'url=admin/link/', true));
		echo '<script src="'.Config::get('URL').'scripts/link.js"></script>';
	} else {
	
		switch ($page){
			case 'farm':
				echo '<script src="'.Config::get('URL').'scripts/farms.js"></script>';
				break;
			case 'paddock':
				echo '<script src="'.Config::get('URL').'scripts/paddocks.js"></script>';
				break;
			case 'crop':
				echo '<script src="'.Config::get('URL').'scripts/crops.js"></script>';
				echo "\r\n\t";
				echo '<script src="'.Config::get('URL').'scripts/farm_paddocks.js"></script>';
				break;
			case 'zones':
				echo '<script src="'.Config::get('URL').'scripts/zones.js"></script>';
				break;
			case 'viewpaddock':
				echo '<script src="'.Config::get('URL').'scripts/viewPaddock.js"></script>';
				break;			
			case 'draw':	
				echo '<script src="'.Config::get('URL').'scripts/drawPaddock.js"></script>';
				echo "\r\n\t";		
				break;
			case 'viewweather':
				echo '<script src="'.Config::get('URL').'scripts/weather.js"></script>';			
				break;
			//case 'url=edit':			
			//case 'url=map':
			case 'user':
			case 'addfarmuser':
				echo '<script src="'.Config::get('URL').'scripts/addFarmUser.js"></script>';
				echo "\r\n\t";		
				echo '<script src="'.Config::get('URL').'scripts/jquery-ajax-form-min.js"></script>';
				echo "\r\n\t";		
			case 'url=config':	// config/index			
			case 'url=collection':	// collection/index			
				echo '<script src="'.Config::get('URL').'scripts/farm_paddocks.js"></script>';
				break;
			case 'url=reports':	 // reports/index
			case 'index':
				echo '<script src="'.Config::get('URL').'scripts/farm_paddocks.js"></script>';
				echo "\r\n\t";			
				echo '<script src="'.Config::get('URL').'scripts/reports.js"></script>';
				echo "\r\n\t";
				break;
			case '2':	// growth_stage => three leaf 	
			case '3':	// growth_stage => five leaf 	
				echo '<script src="'.Config::get('URL').'scripts/mean_leaf_number.js"></script>';
				echo "\r\n\t";
			case '4':	// growth_stage => bulbing
			case '5':	// growth_stage => harvest
			case 'enterdata':
				echo '<script src="'.Config::get('URL').'scripts/imageEmail.js"></script>';
				echo "\r\n\t";			
				echo '<script src="'.Config::get('URL').'scripts/jquery-ajax-form-min.js"></script>';
				break;			
			case 'emergence':
			case 'threeleaf':
			case 'fiveleaf':
			case 'bulbing':
			case 'harvest':
				// report pages need responsive tables
				/*
				echo '<script src="'.Config::get('URL').'scripts/jquery-responsiveTables.js"></script>';
				echo "\r\n\t";
				echo '<script src="'.Config::get('URL').'scripts/jquery.responsiveText.js"></script>';
				echo "\r\n\t";
				echo '<script src="'.Config::get('URL').'scripts/responsive_report.js"></script>';
				echo "\r\n";
				 */
				break;
			default:       
		} 
	} ?>
</head>
<body>
    <!-- wrapper, to center website -->
    <div class="wrapper">
        <!-- logo -->
        <div class="logo"></div>
        <!-- navigation -->
        <ul class="navigation">
            <!--<li <?php if (View::checkForActiveController($filename, "index")) { echo ' class="active" '; } ?> >
                <a href="<?php echo Config::get('URL'); ?>index">Home</a>
            </li>-->
            <?php if (Session::userIsLoggedIn()) { ?>
                <li <?php if (View::checkForActiveController($filename, "dashboard")) { echo ' class="active" '; } ?> >
                    <a href="<?php echo Config::get('URL'); ?>dashboard">Dashboard</a>
					<ul class="navigation-submenu">
						<li <?php if (View::checkForActiveController($filename, "dashboard")) { echo ' class="active" '; } ?> >
							<a href="<?php echo Config::get('URL'); ?>setup">Setup & Configuration</a>
						</li>
						<li <?php if (View::checkForActiveController($filename, "dashboard")) { echo ' class="active" '; } ?> >
							<a href="<?php echo Config::get('URL'); ?>collection">Enter Sample Data</a>
						</li>
						<li <?php if (View::checkForActiveController($filename, "dashboard")) { echo ' class="active" '; } ?> >
							<a href="<?php echo Config::get('URL'); ?>reports">View Reports</a>
						</li>
						<li <?php if (View::checkForActiveController($filename, "dashboard")) { echo ' class="active" '; } ?> >
							<a href="<?php echo Config::get('URL'); ?>config">Update Existing Settings</a>
						</li>
					</ul>
                </li>
            <?php } else { ?>
                <!-- for not logged in users -->
                <li <?php if (View::checkForActiveControllerAndAction($filename, "login/index")) { echo ' class="active" '; } ?> >
                    <a href="<?php echo Config::get('URL'); ?>login">Login</a>
                </li>
                <li <?php if (View::checkForActiveControllerAndAction($filename, "register/index")) { echo ' class="active" '; } ?> >
                    <a href="<?php echo Config::get('URL'); ?>register">Register</a>
                </li>
            <?php } ?>
        </ul>

        <!-- my account -->
        <ul class="navigation right">
        <?php if (Session::userIsLoggedIn()) : ?>
            <li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                <a href="<?php echo Config::get('URL'); ?>user/index">My Account</a>
                <ul class="navigation-submenu">
					<li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/editUser">Update User Info</a>
                    </li>
					<li <?php if (View::checkForActiveController($filename, "user")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>user/changePassword">Update Password</a>
                    </li>
                    <li <?php if (View::checkForActiveController($filename, "login")) { echo ' class="active" '; } ?> >
                        <a href="<?php echo Config::get('URL'); ?>login/logout">Logout</a>
                    </li>
                </ul>
            </li>
            <?php // UAT(administator)=88, UAT(owner)=9, UAT(standard)=5, UAT(public)=1
			if (Session::get("user_account_type") == 88) : ?>
				<!-- Administration section -->
				<li <?php if (View::checkForActiveController($filename, "admin")) { echo ' class="active" '; } ?> >
					<a href="<?php echo Config::get('URL'); ?>admin/index">Admin</a>
				</li>
            <?php endif; ?>
        <?php endif; ?>
        </ul>