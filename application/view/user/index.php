<div class="container">
    <!--<h1>UserController/showProfile</h1>-->
	<h1>Profile Information</h1>

    <div class="box">
        <!--<h2>Your Profile Information</h2>-->

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <!--<div>Your username: <?= $this->user_name; ?></div>-->
		<div>Full Name: <?= $this->user_first_name; ?> <?= $this->user_last_name; ?></div>
        <div>Email Address: <?= $this->user_email; ?></div>
		<div>Phone Number: <?= $this->user_phone_number; ?></div>
        
		<?php /*  
		<div>Your Avatar Image:
            <?php if (Config::get('USE_GRAVATAR')) { ?>
                Your Gravatar Picture (on gravatar.com): <img src='<?= $this->user_gravatar_image_url; ?>' />
            <?php } else { ?>
                Your Avatar Picture (saved locally): <img src='<?= $this->user_avatar_file; ?>' />
            <?php } ?>
        </div>
		*/ ?>
		<p></p>
        <div>Account Type: <?php 
			switch ($this->user_account_type){
				case 88:
					echo 'Administrator';
					break;
				case 9:
					echo 'Owner';
					break;
				case 5:
					echo 'Standard User';
					break;
				case 1:
					echo 'Public User';
					break;					
				default:
					// To-do trap error
			}
			//($this->user_account_type == 7 ? 'Administrator' : 'Standard User'); ?>
		</div>
    </div>
</div>
