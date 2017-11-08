<div class="container">
    <h1>Update Your Email Address</h1>

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div class="input-box">
        <form action="<?php echo Config::get('URL'); ?>user/editUserEmail_action" method="post">
            <label>
                New email address: <input type="text" name="user_email" required />
            </label>
            <input type="submit" value="Update" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>/>
        </form>
    </div>
</div>
