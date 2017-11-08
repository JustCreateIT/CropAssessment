<div class="container">
    <h1>Update Your Details</h1>

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div class="input-box">
        <form action="<?php echo Config::get('URL'); ?>user/editUser_action" method="post">
		            <label>
                First name: <input type="text" name="user_firstname" value="<?= $this->user_firstname ?>" required />
            </label> 
            <label>
                Last name: <input type="text" name="user_lastname" value="<?= $this->user_lastname ?>" required />
            </label>   
            <label>
                Email address: <input type="text" name="user_email" value="<?= $this->user_email ?>" required />
            </label>       
            <label>
                Phone number<input type="text" name="user_phone_number" value="<?= $this->user_phone_number ?>" required />
            </label>
            <input type="submit" value="Update" <?php if((Session::get("user_account_type") == 1)){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>/>
        </form>
    </div>
</div>
