<?php
function db_connect() {
    $db = new PDO('mysql:host=localhost;dbname=task_deep','root','');
    return $db;
}

function validate_input($input) {
    return trim(strip_tags(htmlspecialchars($input)));
}

function get_user_by_email($email) {
    $db = db_connect();
    $sql = "SELECT id,email,password,role FROM users WHERE email <=>:email";
    $statement = $db->prepare($sql);
    $statement->execute(['email' => $email]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
}

function get_all_users() {
    $db = db_connect();
    $sql = "SELECT * FROM users";
    $statement = $db->prepare($sql);
    $statement->execute(['email' => $email]);
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $users;
}

function set_flash_message($name, $message) {
    $_SESSION['message'] = [
        'name' => $name,
        'message' => $message
    ];
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

function display_flash_message() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-'.$_SESSION['message']['name'].' text-dark" role="alert">' . $_SESSION['message']['message'] . '</div>';
        unset($_SESSION['message']);
    }
}

function is_logged_in() {
    if(isset($_SESSION['auth']) && !empty($_SESSION['auth'])) {
        return true;
    }
}

function check_role($role) {
    if($_SESSION['auth']['role'] == $role) {
        return true;
    }
}

?>