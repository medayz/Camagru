<?php require APP_PATH . 'views/includes/header.php'; ?>
<?php require APP_PATH . 'views/includes/navbar.php'; ?>

    <div id="grid-container">
        <div id="camera">
            <div id="video">
                <video style="transform: scaleX(-1);">Camera not working!</video>
                <div id="video-btns">
                    <button id="take-pic" disabled>Take a pic!</button>
                    <div id="upload-pic"><input type="file" accept="image/*"></div>
                </div>
            </div>
            <canvas style="display: none;"></canvas>
        </div>
        <div id="stickers">
            <div id="sticker-bar">
            <?php
                foreach ($data['stickers'] as $sticker) {
                    echo '<div id="' . $sticker . '" class="sticker"><img src="' . URL_ROOT . "img/Stickers/" . $sticker . '"></div>';
                }
            ?>
            </div>
        </div>
        <div id="pics-bar">
<!--            <div class="pic"><img filter="url(#svgBlur)" style="width: 300px;" src="--><?php //echo URL_ROOT; ?><!--img/pic.png"></div>-->
            <?php
            foreach ($data['pics'] as $name) {
                echo '<div class="pic"><img class="img" src="' . URL_ROOT . 'img/Users_pics/' . $name . '"><div class="delete"></div></div>';
            }
            ?>
        </div>
    </div>
    <div id="show-upload">
        <div id="cancel"></div>
        <div id="edit-pic">
            <div id="edit-uploaded">
                <div id="pic-frame">
                    <img id="uploaded">
                </div>
                <button id="save-pic" disabled>Save!</button>
            </div>
            <div id="stickers">
                <div id="sticker-bar">
                    <?php
                    foreach ($data['stickers'] as $sticker) {
                        echo '<div id="' . $sticker . '" class="sticker-upload"><img src="' . URL_ROOT . "img/Stickers/" . $sticker . '"></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo URL_ROOT; ?>js/camera.js"></script>
<?php require APP_PATH . 'views/includes/footer.php'; ?>
