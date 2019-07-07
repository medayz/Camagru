<?php require APP_PATH . 'views/includes/header.php'; ?>
<?php require APP_PATH . 'views/includes/navbar.php'; ?>
        <div id="container">
            <div id="form">
                <form action="<?php echo URL_ROOT; ?>users/edit" method="post">
                    <div class="inp-grp">
                        <span style="border-radius: 10px; padding: 2px 10px; color: #2D3047; background-color: #F8FA90; letter-spacing: 1.2pt; text-align: left; line-height: 40px; font-size: 10pt; font-family: Helvetica, sans-serif;">e-mail notifications</span>
                        <div id="notif" class="<?php echo $data['notif']; ?>"></div>
                        <span><?php echo $data['notif']; ?></span>
                        <input type="text" name='notif' value='<?php echo $data['notif']; ?>' hidden>
                    </div>

                    <div class="inp-grp">
                        <span style="border-radius: 10px; padding: 2px 10px; color: #2D3047; background-color: #F8FA90; letter-spacing: 1.2pt; text-align: left; line-height: 40px; font-size: 10pt; font-family: Helvetica, sans-serif;">e-mail address</span>
                        <input class="inp email" type="text" name="email" placeholder="e-mail address" value="<?php echo $data['email']; ?>">
                        <span class="err email"><?php echo $data['email_err']; ?></span>
                    </div>

                    <div class="inp-grp">
                        <span style="border-radius: 10px; padding: 2px 10px; color: #2D3047; background-color: #F8FA90; letter-spacing: 1.2pt; text-align: left; line-height: 40px; font-size: 10pt; font-family: Helvetica, sans-serif;">username</span>
                        <input class="inp username" type="text" name="username" placeholder="username" value="<?php echo $data['username']; ?>">
                        <span class="err username"><?php echo $data['username_err']; ?></span>
                    </div>

                    <button id="btn" style="width: 180px;">Submit Changes</button>

                </form>
            </div>
        </div>
    <script src="<?php echo URL_ROOT . 'js/edit.js'; ?>"></script>
<?php require APP_PATH . 'views/includes/footer.php'; ?>