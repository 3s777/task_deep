<?php
session_start();
require 'functions.php';

$email = validate_input($_POST["email"]);
$password = validate_input($_POST["password"]);

$user = get_user_by_email($email);

if(!empty($user) and password_verify($password, $user['password'])) {
    $_SESSION['auth'] = [
        'id' => $user['id'],
        'email' => $user['email'],
    ];
    redirect_to('/users.php');
} else {
    set_flash_message('danger', 'Неверный логин или пароль');
    redirect_to('/page_login.php');
}


//$_SESSION['error'] = false;
//
//
//if(count($users) > 0) {
//    foreach ($users as $user) {
//        if (password_verify($password, $user['password'])) {
//            $_SESSION['auth'] = true;
//            $_SESSION['login'] = $user['email'];
//        } else {
//            $_SESSION['error'] = true;
//        }
//    }
//} else {
//    $_SESSION['error'] = true;
//}
//
//if($_SESSION['error']) {
//    header("Location: /task_14.php");
//    exit();
//} else {
//    header("Location: /task_14_1.php");
//    exit();
//}
?>