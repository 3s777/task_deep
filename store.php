<?php
session_start();
require 'functions.php';

$email = validate_input($_POST["email"]);
$password = password_hash(validate_input($_POST["password"]), PASSWORD_DEFAULT);
$username = validate_input($_POST["username"]);
$job = validate_input($_POST["job"]);
$phone = validate_input($_POST["phone"]);
$address = validate_input($_POST["address"]);
$status = validate_input($_POST["status"]);
$avatar = $_FILES['avatar'];
$vk = validate_input($_POST["vk"]);
$telegram = validate_input($_POST["telegram"]);
$instagram = validate_input($_POST["instagram"]);

$user = get_user_by_email($email);

if(!empty($user)) {
    set_flash_message('danger', '<strong>Уведомление!</strong> Этот эл. адрес уже занят другим пользователем.');
    redirect_to('/create_user.php');
} else {
    set_flash_message('success', 'Новый пользователь успешно добавлен.');
    $user_id = add_user($email, $password);
    edit_user_info($user_id, $username, $job, $phone, $address);
    set_user_status($user_id, $status);
    add_user_social_links($user_id, $vk, $telegram, $instagram);
    upload_user_avatar($user_id, $avatar);
    redirect_to('/users.php');
}
?>