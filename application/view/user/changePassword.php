<div class="container">
    <h1>Update Your Password</h1>

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div class="input-box">
        <!-- new password form box -->
        <form method="post" action="<?php echo Config::get('URL'); ?>user/changePassword_action" name="new_password_form">
            <label for="change_input_password_current">Enter current password:</label>
            <p><input id="change_input_password_current" class="reset_input" type='password'
                   name='user_password_current' pattern=".{8,}" required autocomplete="off"  /></p>
            <label for="change_input_password_new">New password (min. 8 characters)</label>
            <p><input id="change_input_password_new" class="reset_input" type="password"
                   name="user_password_new" pattern=".{8,}" required autocomplete="off" /></p>
            <label for="change_input_password_repeat">Repeat new password</label>
            <p><input id="change_input_password_repeat" class="reset_input" type="password"
                   name="user_password_repeat" pattern=".{8,}" required autocomplete="off" /></p>
            <input type="submit"  name="submit_new_password" value="Submit new password" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>/>
        </form>

    </div>
</div>
