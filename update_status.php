<?php
session_start();
require 'functions.php';

$id = validate_input($_POST["id"]);
$status = validate_input($_POST["status"]);

if(!check_role('admin')) {
    if(!is_author($id, $_SESSION['auth']['id'])) {
        set_flash_message('danger', 'Можно редактировать только свой профиль');
        redirect_to('/users.php');
    }
}

$user = get_user_by_id($id);

var_dump($id);

if(empty($user)) {
    set_flash_message('danger', 'Пользователя не существует');
    redirect_to('/users.php');
} else {
    set_flash_message('success', 'Статус успешно отредактирован.');
    set_user_status($id, $status);
    redirect_to('/page_profile.php');
}
?>