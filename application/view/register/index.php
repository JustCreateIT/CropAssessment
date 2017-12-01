<div class="container">

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <!-- login box on left side -->
    <!--<div class="login-box" style="width: 50%; display: block;">-->
	<div class="login-box">
        <h1>Register a new account</h1>

        <!-- register form -->
        <form method="post" action="<?php echo Config::get('URL'); ?>register/register_action">
            <!-- the user name input field uses a HTML5 pattern check -->
            <!--<input type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" placeholder="Username (letters/numbers, 2-64 chars)" required />-->
						<div>
			<label style="width:100%;text-transform:none;margin-bottom:10px;" for="user_account_type">Select your role on the farm:</label>
			</div>
			<div>				
				<select style="width:100%;padding:15px; margin-bottom:10px;" name="user_account_type" id="user_account_type">
					<?php foreach ($this->account_types as $account_type) { 							
						if ($account_type->account_type == 5) { ?> 
						<option value="<?= $account_type->account_type ?>" selected>Employee / Contractor</option>
						<?php } 
						if ($account_type->account_type == 9) { ?> 
						<option value="<?= $account_type->account_type ?>">Farm Owner / Manager</option>
					<?php } 				
					} ?></select>
			</div>
			<input type="text" name="user_first_name" placeholder="First name" required />
			<input type="text" name="user_last_name" placeholder="Last name" required />			
            <input type="email" name="user_email" placeholder="Email address (a real address)" required />
            <input type="email" name="user_email_repeat" placeholder="Repeat email address (to prevent typos)" required />
			<input type="number" name="user_phone_number" placeholder="Phone number" />
            <input type="password" name="user_password_new" pattern=".{8,}" placeholder="Password (8+ characters)" required autocomplete="off" />
            <input type="password" name="user_password_repeat" pattern=".{8,}" required placeholder="Repeat your password" autocomplete="off" />

            <!-- show the captcha by calling the login/showCaptcha-method in the src attribute of the img tag -->
            <img id="captcha" src="<?php echo Config::get('URL'); ?>register/showCaptcha" />
            <input type="text" name="captcha" placeholder="Please enter above characters" required />

            <!-- quick & dirty captcha reloader -->
            <a href="#" style="display: block; font-size: 11px; margin: 5px 0 15px 0; text-align: center"
               onclick="document.getElementById('captcha').src = '<?php echo Config::get('URL'); ?>register/showCaptcha?' + Math.random(); return false">Reload Captcha</a>

            <input type="submit" value="Register" />
        </form>
    </div>
</div>
<?php 

/*
<!--
<div class="container">
    <p style="display: block; font-size: 11px; color: #999;">
        Please note: This captcha will be generated when the img tag requests the captcha-generation
        (= a real image) from YOURURL/register/showcaptcha. As this is a client-side triggered request, a
        $_SESSION["captcha"] dump will not show the captcha characters. The captcha generation
        happens AFTER the request that generates THIS page has been finished.
    </p>
</div>
-->
*/

?>