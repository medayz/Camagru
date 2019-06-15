<div id="navbar" style="opacity: 0.93; vertical-align: middle; display: block; position: fixed; top: 0; left: 0;width: 100%; background-color: #2D3047; z-index: 1337;">
    <a href="<?php echo URL_ROOT; ?>home"><img id="logo_nav" style="display: inline-block;" src="<?php echo URL_ROOT; ?>img/logo.png"></a>

    <div id="menu-btn">
<!--        <img src="--><?php //echo URL_ROOT . 'img/Icons/menu.svg';?><!--" alt="menu">-->
    </div>

    <a
            href="<?php echo !empty($_SESSION['user']) ? URL_ROOT . "home/logOut" : URL_ROOT . "signin"; ?>"
    >
        <button id="btn" style="font-size: 10pt; padding: 0; width: 100px; height: 28px; margin: 10px 20px !important; float: right;">
            <?php echo !empty($_SESSION['user']) ? "Log Out" : "Log in" ?>
        </button>
    </a>

    <ul class="menu">
        <li class="menu-item">
            <a class="menu-link<?php echo $data['page'] === 'Camera' ? ' active' : ''; ?>" href="<?php echo URL_ROOT; ?>home">Camera</a>
        </li>
        <li class="menu-item">
            <a class="menu-link<?php echo $data['page'] === 'Gallery' ? ' active' : ''; ?>" href="<?php echo URL_ROOT?>gallery">Gallery</a>
        </li>
    </ul>
</div>
