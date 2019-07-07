<?php require APP_PATH . 'views/includes/header.php'; ?>
<?php require APP_PATH . 'views/includes/navbar.php'; ?>
        <div class="main">
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
<?php require APP_PATH . 'views/includes/logo_footer.php'; ?>
<?php require APP_PATH . 'views/includes/footer.php'; ?>
