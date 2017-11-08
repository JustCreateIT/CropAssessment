<div class="container">
    <h1>Administration Panel</h1>
    <div class="box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); 
		

?>
        <h3>View all users currently in the system</h3>

        <div>
            As an Administrator you have the ability to soft delete or suspend a user, modify a user account type or view a user profile.
        </div>
        <div>
            <table class="overview-table">			
                <thead>
                <tr>                    				
                    <!--<td>Avatar</td>-->
					<td>User Id</td>
					<td>Account Type</td>					
                    <td>Activated</td>                    
					<td>Full Name</td>
					<td>View Profile</td>
                    <td>Suspend (Days)</td>
                    <td>Soft Delete</td>
                    <td>&nbsp;</td>
                </tr>
                </thead>
				<tbody><?php foreach ($this->users as $user) { ?>
				<tr class="<?= ($user->user_active == 0 ? 'inactive' : 'active'); ?>">
					<!--<td class="avatar"><?php if (isset($user->user_avatar_link)) { ?><img src="<?= $user->user_avatar_link; ?>"/><?php } ?></td>-->
					<form action="<?= config::get("URL"); ?>admin/actionAccountSettings" method="post">
					<td><?= $user->user_id; ?></td>
					<td><select name="user_account_type" id="user_account_type">
					<?php foreach ($this->account_types as $account_type) { 							
						if ($account_type->account_type == $user->user_account_type) { ?> 
						<option value="<?= $account_type->account_type ?>" selected><?= ucwords($account_type->account_name) ?></option>
					<?php } else { ?>
						<option value="<?= $account_type->account_type ?>"><?= ucwords($account_type->account_name)?></option>
					<?php } 									
					} ?></select></td>
					<td><?= ($user->user_active == 0 ? 'No' : 'Yes'); ?></td>
					<td><?= $user->user_first_name; ?>&nbsp;<?= $user->user_last_name; ?></td>
					<td>
						<a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->user_id; ?>">Profile</a>
					</td>						
					<td><input type="number" name="suspension" /></td>					
					<td><input type="checkbox" name="softDelete" value="<?= $user->user_id; ?>" <?php if ($user->user_deleted) { ?> checked <?php } ?>/></td>
					<td>
						<input type="hidden" name="user_id" value="<?= $user->user_id; ?>" />
						<input type="submit" value="Update" <?php if((Session::get("user_account_type") != 88)){ echo 'disabled style="color: #777; background-color: transparent; border: 2px solid #777;"'; } ?>/>
					</td>					
					</form>
				</tr>
				</tbody>
                <?php } ?>
            </table>			
        </div>
    </div>
</div>
