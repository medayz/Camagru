<?php require APP_PATH . 'views/includes/header.php'; ?>
        <div id="container">
            <div id="form">
                <form action="<?php echo URL_ROOT; ?>users/resetPwd" method="post">
                    <img id="logo" src="<?php echo URL_ROOT; ?>img/logo.png">

                    <input type="text" name="username" value="<?php echo $data['username']; ?>" hidden>
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