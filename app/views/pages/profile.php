<?php require APP_PATH . 'views/includes/header.php'; ?>
<?php require APP_PATH . 'views/includes/navbar.php'; ?>
        <div id="container">
            <div id="form">
                <form action="<?php echo URL_ROOT; ?>users/edit" method="post">
                    <div class="inp-grp">
                        <input class="inp email" type="text" name="email" placeholder="e-mail address" value="<?php echo $data['email']; ?>">
                        <span class="err email"><?php echo $data['email_err']; ?></span>
                    </div>

                    <div class="inp-grp">
                        <input class="inp username" type="text" name="username" placeholder="username" value="<?php echo $data['username']; ?>">
                        <span class="err username"><?php echo $data['username_err']; ?></span>
                    </div>

                    <button id="btn" style="width: 180px;">Submit Changes</button>

                </form>
            </div>
        </div>
<?php require APP_PATH . 'views/includes/footer.php'; ?>