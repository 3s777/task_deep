<?php
session_start();
require 'functions.php';

$id = validate_input($_GET["id"]);

if(!check_role('admin')) {
    if(!is_author($id, $_SESSION['auth']['id'])) {
        set_flash_message('danger', 'Можно удалить только свой профиль');
        redirect_to('/users.php');
    }
}

$user = get_user_by_id($id);

if(empty($user)) {
    set_flash_message('danger', 'Пользователя не существует');
    redirect_to('/users.php');
} else {

    delete_user($id);

    if(check_role('admin')) {
        set_flash_message('success', 'Пользователь успешно удален.');
        redirect_to('/users.php');
    } else {
        unset($_SESSION['auth']);
        set_flash_message('danger', 'Вы удалили себя из системы');
        redirect_to('/page_register.php');
    }
}
?>