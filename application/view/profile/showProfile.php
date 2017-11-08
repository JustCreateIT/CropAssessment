<div class="container">
    <h1>View User Profile</h1>
    <div class="box">

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <h3>What happens here?</h3>
        <div>This page shows all public information about a certain user.</div>

        <?php if ($this->user) { ?>
            <div>
                <table class="overview-table">
                    <thead>
                    <tr>
                        <!--<td>Id</td>-->
                        <td>Avatar</td>
                        <!--<td>User name</td>
                        <td>User's email</td>-->
                        <td>Full Name</td>
                        <td>Email Address</td>	
						<!--// New field -->
						<td>Phone Number</td>
                        <td>Activated?</td>
                    </tr>
                    </thead>
                    <tbody>
                        <tr class="<?= ($this->user->user_active == 0 ? 'inactive' : 'active'); ?>">
                            <!--<td><?= $this->user->user_id; ?></td>-->
                            <td class="avatar">
                                <?php if (isset($this->user->user_avatar_link)) { ?>
                                    <img src="<?= $this->user->user_avatar_link; ?>" />
                                <?php } ?>
                            </td>
                            <!--<td><? //$this->user->user_name; ?></td>-->
							<td><?= $this->user->user_first_name; ?>&nbsp;<?= $this->user->user_last_name; ?></td>
                            <td><?= $this->user->user_email; ?></td>
							<td><?= $this->user->user_phone_number; ?></td>
                            <td><?= ($this->user->user_active == 0 ? 'No' : 'Yes'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php } ?>

    </div>
</div>
