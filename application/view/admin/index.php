<div class="container">
    <h1>Administration Panel</h1>
    <div class="box">
        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages();?>
        <h3>View all users currently in the system</h3>
        <div>
            As an Administrator you have the ability to modify a user account type, add a user to a farm, view a user profile or soft delete or suspend a user.
        </div>
        <div class="overview-table">
            <table class="overview-table">			
                <thead>
                <tr>
					<th>ID#</th>
					<th>Account Type</th>					
                    <th>Active</th>                  
					<th>Full Name</th>
					<th>Farms</th>
					<th>View</th>
                    <th>Suspend</th>
                    <th>Soft Delete</th>
                    <th>&nbsp;</th>
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
						<a href="<?= Config::get('URL') . 'admin/link/' . $user->user_id; ?>">Link Farms</a>
					</td>	
					<td>
						<a href="<?= Config::get('URL') . 'profile/showProfile/' . $user->user_id; ?>">Profile</a>
					</td>						
					<td><input type="number" name="suspension"/></td>					
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
