<div class="container">
    <h1>Edit Your Avatar</h1>

    <!-- echo out the system feedback (error and success messages) -->
    <?php $this->renderFeedbackMessages(); ?>

    <div class="input-box">
        <h2>Upload Your Avatar</h2>
        <div class="feedback info">
            If you still see the old picture after uploading a new one: Hard-Reload the page with F5! Your browser doesn't
            realize there's a new image as new and old one have the same filename.
        </div>

        <form action="<?php echo Config::get('URL'); ?>user/uploadAvatar_action" method="post" enctype="multipart/form-data">
            <label for="avatar_file">Select an avatar image from your hard-disk</label>
            <input type="file" name="avatar_file" required />
            <!-- max size 5 MB (as many people directly upload high res pictures from their digital cameras) -->
            <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
            <input type="submit" value="Upload image" />
        </form>
    </div>

    <div class="input-box">
        <h2>Delete Your Avatar</h2>
        <p>Click this link to delete your (local) avatar: <a href="<?php echo Config::get('URL'); ?>user/deleteAvatar_action">Delete your avatar</a>
    </div>
</div>
