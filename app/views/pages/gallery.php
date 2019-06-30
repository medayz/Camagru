<?php require APP_PATH . 'views/includes/header.php'; ?>
<?php require APP_PATH . 'views/includes/navbar.php'; ?>
        <div class="main">
<!--		    <img id="logo" src="--><?php //echo URL_ROOT; ?><!--img/logo.png">-->
			<div class="pics">
                <?php
                    foreach ($data['pics'] as $name) {
                        echo '<div class="pic gallery"><img class="img" src="' . URL_ROOT . 'img/Users_pics/' . $name . '"><div class="over-img"><div class="engagement"><span><span>0</span> likes <span>0</span> comments</span><div class="comments"></div><div class="like"></div></div></div></div>';
                    }
                ?>
			</div>
		</div>
        <div id="show-post">
            <div id="cancel"></div>
            <div id="post">
                <div id="post-img">
                    <div id="big-like"></div>
                </div>
                <div id="user-likes">
                </div>
                <div id="comment-div">
                    <div id="new-comment" class="comment">
                        <input id="com-inp" type="text" placeholder="comment this pic...">
                    </div>
                </div>
            </div>
        </div>
        <div id="footer" style="color: #DFF8EB; background-color: #292C41;">
            <div id="flex-container">
                <div><span id="school">1337 School</span></div>
                <div id="watermark"><img src="<?php echo URL_ROOT; ?>img/watermark.png"></div>
                <div><span id="copyrights"">&copy; Camagru mzahir 2019</span></div>
            </div>
        </div>
<?php require APP_PATH . 'views/includes/footer.php'; ?>
