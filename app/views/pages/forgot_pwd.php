<?php require APP_PATH . 'views/includes/header.php'; ?>
        <div id="container">
            <div id="form">
                <img id="logo" src="<?php echo URL_ROOT; ?>img/logo.png">

                <form action="<?php echo URL_ROOT; ?>users/forgotPwd" method="post">
                    <div class="inp-grp">
                        <span style="border-radius: 60px; padding: 6px 12px; color: #2D3047; background-color: #F8FA90; letter-spacing: 1.2pt; text-align: left; line-height: 40px; font-size: 10pt; font-family: Helvetica, sans-serif;">U didn't forget ur username? right?</span>
                        <input style="margin-top: 20px;" class="inp username" type="text" name="username" placeholder="username" value="<?php echo $data['username']; ?>">
                        <span class="err username"><?php echo $data['username_err']; ?></span>
                    </div>

                    <button id="btn" style="width: 180px;">Save my ass!</button>

                </form>
            </div>
        </div>
        <script src="<?php echo URL_ROOT; ?>js/forms.js"></script>
<?php require APP_PATH . 'views/includes/footer.php'; ?>