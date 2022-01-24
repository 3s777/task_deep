<?php
session_start();
require 'functions.php';

$email = validate_input($_POST["email"]);
$password = password_hash(validate_input($_POST["password"]), PASSWORD_DEFAULT);

$user = get_user_by_email($email);

if(!empty($user)) {
    set_flash_message('danger', '<strong>Уведомление!</strong> Этот эл. адрес уже занят другим пользователем.');
    redirect_to('/page_register.php');
} else {
    set_flash_message('success', 'Регистрация успешна');
    add_user($email, $password);
    redirect_to('/page_login.php');
}
?>