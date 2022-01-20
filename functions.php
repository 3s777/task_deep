<?php
function validate_input($input) {
    return trim(strip_tags(htmlspecialchars($input)));
}

function get_user_by_email($email) {
    $db = new PDO('mysql:host=localhost;dbname=task_deep','root','');
    $sql = "SELECT id FROM users WHERE email <=>:email";
    $statement = $db->prepare($sql);
    $statement->execute(['email' => $email]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
}

function set_flash_message($name, $message) {
    $_SESSION[$name] = $message;
}

function redirect_to($path) {
    header("Location: $path");
}

function add_user($email, $password) {
    $db = new PDO('mysql:host=localhost;dbname=task_deep','root','');
    $sql = "INSERT INTO users (email,password) VALUES (:email,:password)";
    $result = $db->prepare($sql);
    $result->execute(['email' => $email, 'password' => $password]);
}

function display_flash_message($name) {
    if (isset($_SESSION[$name])) {
        echo '<div class="alert alert-'.$name.' text-dark" role="alert">' . $_SESSION[$name] . '</div>';
        unset($_SESSION[$name]);
    }
}

?>