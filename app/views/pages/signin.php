<?php require APP_PATH . 'views/includes/header.php'; ?>
        <div id="container">
            <div id="form">
                <form action="<?php echo URL_ROOT; ?>signin/login" method="post">
                    <img id="logo" src="<?php echo URL_ROOT; ?>img/logo.png">

                    <div class="inp-grp">
                        <input class="inp username" type="text" name="username" placeholder="username" value="<?php echo $data['username']; ?>">
                        <span class="err username"><?php echo $data['username_err']; ?></span>
                    </div>

                    <div class="inp-grp">
                        <input class="inp pwd" type="password" name="pwd" placeholder="password" value="<?php echo $data['pwd']; ?>">
                        <span class="err pwd"><?php echo $data['pwd_err']; ?></span>
                    </div>

                    <button id="btn">Log In</button>
                </form>
            </div>
        </div>
<?php require APP_PATH . 'views/includes/footer.php'; ?>
