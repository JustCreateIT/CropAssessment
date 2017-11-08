<div class="container">
    <h1>Set Password</h1>

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div class="input-box">
        <?php  // FYI: ... Idenfitication process works via password-reset-token (hidden input field) ?>
        <!-- new password form box -->
        <form method="post" action="<?php echo Config::get('URL'); ?>login/setNewPassword" name="new_password_form">
            <input type='hidden' name='user_email' value='<?php echo $this->user_email; ?>' />
            <input type='hidden' name='user_password_reset_hash' value='<?php echo $this->user_password_reset_hash; ?>' />
            <label for="reset_input_password_new">Password (minimum 8 characters)</label>
            <input class="float_left" id="reset_input_password_new" class="reset_input" type="password"
                   name="user_password_new" pattern=".{8,}" required autocomplete="off" />
            <label for="reset_input_password_repeat">Repeat Password</label>
            <input id="reset_input_password_repeat" class="reset_input" type="password"
                   name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />
            <input type="submit"  name="submit_new_password" value="Submit new password" />
        </form>

        <a href="<?php echo Config::get('URL'); ?>login/index">Back to Login Page</a>
    </div>
</div>
