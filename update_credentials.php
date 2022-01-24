<?php
session_start();
require 'functions.php';

$id = validate_input($_POST["id"]);
$email = validate_input($_POST["email"]);
$password = validate_input($_POST["password"]);

if(!check_role('admin')) {
    if(!is_author($id, $_SESSION['auth']['id'])) {
        set_flash_message('danger', 'Можно редактировать только свой профиль');
        redirect_to('/users.php');
    }
}

$current_user = get_user_by_id($id);
$check_user = get_user_by_email($email);

if(empty($check_user) || $current_user['email'] == $check_user['email']) {
    set_flash_message('success', 'Данные пользователя успешно обновлены');
    edit_credentials($id, $email, $password);
    redirect_to('/page_profile.php?id='.$id);
} else {
    set_flash_message('danger', '<strong>Уведомление!</strong> Этот эл. адрес уже занят другим пользователем.');
    redirect_to('security.php?id='.$id);
}
?>