<?php
    session_start();
    require 'functions.php';
    unset($_SESSION['auth']);
    set_flash_message('danger', 'Вы вышли из системы');
    redirect_to('/page_login.php');
?>