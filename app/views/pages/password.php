<?php require APP_PATH . 'views/includes/header.php'; ?>
<?php require APP_PATH . 'views/includes/navbar.php'; ?>
        <div id="container">
            <div id="form">
                <form action="<?php echo URL_ROOT; ?>users/change_pwd" method="post">

                    <div class="inp-grp">
                        <input class="inp old-pwd" type="password" name="pwd" placeholder="current password" value="<?php echo $data['pwd']; ?>">
                        <span class="err old-pwd"><?php echo $data['pwd_err']; ?></span>
                    </div>

                    <div class="inp-grp">
                        <input class="inp pwd" type="password" name="new_pwd" placeholder="new password" value="<?php echo $data['new_pwd']; ?>">
                        <span class="err pwd"><?php echo $data['new_pwd_err']; ?></span>
                    </div>

                    <div class="inp-grp">
                        <input class="inp cpwd" type="password" name="confirm_pwd" placeholder="confirm new password" value="<?php echo $data['confirm_pwd']; ?>">
                        <span class="err cpwd"><?php echo $data['confirm_pwd_err']; ?></span>
                    </div>

                    <button id="btn" style="width: 180px;">Change Password</button>

                </form>
            </div>
        </div>
<?php require APP_PATH . 'views/includes/footer.php'; ?>