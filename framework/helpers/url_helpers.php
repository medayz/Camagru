<?php
// Redirect to new page
function    redirect($path) {
    header("Location: " . URL_ROOT . $path);
}
?>