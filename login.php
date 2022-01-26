<?php
session_start();
require 'functions.php';

$email = validate_input($_POST["email"]);
$password = validate_input($_POST["password"]);

$user = get_user_by_email($email);

if(!empty($user) and password_verify($password, $user['password'])) {
    login($user);
    redirect_to('/users.php');
} else {
    set_flash_message('danger', 'Неверный логин или пароль');
    redirect_to('/page_login.php');
}
?>