<?php
session_start();
require 'functions.php';

$id = validate_input($_POST["id"]);
$username = validate_input($_POST["username"]);
$job = validate_input($_POST["job"]);
$phone = validate_input($_POST["phone"]);
$address = validate_input($_POST["address"]);

if(!check_role('admin')) {
    if(!is_author($id, $_SESSION['auth']['id'])) {
        set_flash_message('danger', 'Можно редактировать только свой профиль');
        redirect_to('/users.php');
    }
}

$user = get_user_by_id($id);

if(empty($user)) {
    set_flash_message('danger', 'Пользователя не существует');
    redirect_to('/users.php');
} else {
    set_flash_message('success', 'Пользователь успешно отредактирован.');
    edit_user_info($id, $username, $job, $phone, $address);
    redirect_to('/users.php');
}
?>