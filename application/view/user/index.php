<div class="container">
    <!--<h1>UserController/showProfile</h1>-->
	<h1>Profile Information</h1>

    <div class="box">
        <!--<h2>Your Profile Information</h2>-->

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

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
					echo 'Farm Owner / Manager';
					break;
				case 5:
					echo 'Employee / Contractor';
					break;
				case 1:
					echo 'Public User';
					break;					
				default:
					// To-do trap error
			}?>
		</div>
		<p></p>
		<div>Associated Farms: </div>
		<div>
<textarea cols="75" rows="<?php echo count($this->user_farms)+1?>">
<?php foreach($this->user_farms as $farm){
echo $farm->farm_name."\n";
} ?> 
		</textarea></div>
    </div>
</div>
