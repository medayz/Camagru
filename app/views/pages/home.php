<?php require APP_PATH . 'views/includes/header.php'; ?>
<?php require APP_PATH . 'views/includes/navbar.php'; ?>

<!--    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: none; position: absolute; top: -999999px">-->
<!--        <defs>-->
<!--            <filter id="contrast">-->
<!--                <feComponentTransfer>-->
<!--                    <feFuncR type="linear" slope="9" intercept="-5"/>-->
<!--                    <feFuncG type="linear" slope="81" intercept="-41"/>-->
<!--                    <feFuncB type="linear" slope="121" intercept="-61"/>-->
<!--                </feComponentTransfer>-->
<!--            </filter>-->
<!--        </defs>-->
<!--    </svg>-->
    <svg style="width: 0; height: 0;">
        <!-- THE mask -->
        <mask id="mask" maskContentUnits="objectBoundingBox">
            <!-- using an img, but this img is black/transparent so we filter it to make it white -->
            <image xlink:href="http://iamvdo.me/conf/2012/kiwiparty/img/masque2.png" width="1" height="1" preserveAspectRatio="none" filter="url(#filter)"/>
        </mask>

        <!-- the filter to make the image white -->
        <filter id="filter">
            <feFlood flood-color="white" />
            <feComposite in2="SourceAlpha" operator="in" />
        </filter>
    </svg>
    <div id="grid-container">
        <div id="camera">
<!--            <input type="file" accept="image/*;capture=camera">-->
            <div id="video">
                <video style="transform: scaleX(-1);">Camera not working!</video>
                <div id="video-btns">
                    <button id="take-pic">Take a pic!</button>
                    <div id="upload-pic"><input type="file"></div>
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
                <!--            <input type="file" accept="image/*;capture=camera">-->
            <div id="edit-uploaded">
<!--                <div style="margin: 0 auto; display: flex; justify-content: center; align-items: center; width: 640px; height: 480px; background-color: #F8FA90;">-->
                <div id="pic-frame">
                    <img id="uploaded">
                </div>
                <button id="save-pic">Save!</button>
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
<?php require APP_PATH . 'views/includes/footer.php'; ?>
