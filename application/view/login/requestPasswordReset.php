<div class="container">
    <h1>Request a password reset</h1>
    <div class="input-box" style="padding:0;">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <!-- request password reset form box -->
        <form method="post" action="<?php echo Config::get('URL'); ?>login/requestPasswordReset_action">
            <label for="user_name_or_email">
                Enter your email address
                <input type="text" name="user_name_or_email" required />
            </label>

            <!-- show the captcha by calling the login/showCaptcha-method in the src attribute of the img tag -->
            <img id="captcha" src="<?php echo Config::get('URL'); ?>register/showCaptcha" /><br/>
            <input type="text" name="captcha" placeholder="Enter captcha above" required />

            <!-- quick & dirty captcha reloader -->
            <a href="#" style="display: block; font-size: 11px; margin: 5px 0 15px 0;"
               onclick="document.getElementById('captcha').src = '<?php echo Config::get('URL'); ?>register/showCaptcha?' + Math.random(); return false">Reload Captcha</a>

            <input type="submit" value="Request a password-reset email" />
        </form>

    </div>
</div>
<div class="container">
    <p style="display: block; font-size: 0.75em; color: #999; margin:15px 0 0 0;">
        Once submitted you will receive an email with information detailing how to reset your password. If you do not receive this message please check your spam filter settings and/or request a new password reset from this page.
    </p>
</div>
