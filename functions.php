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

function get_user_by_id($id) {
    $db = db_connect();
    $sql = "SELECT * FROM users WHERE id <=>:id";
    $statement = $db->prepare($sql);
    $statement->execute(['id' => $id]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
}


function get_all_users() {
    $db = db_connect();
    $sql = "SELECT * FROM users";
    $statement = $db->prepare($sql);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $users;
}

function get_user_avatar_url($user_id) {
    $db = db_connect();
    $sql = "SELECT avatar FROM users WHERE id <=>:id";
    $statement = $db->prepare($sql);
    $statement->execute(['id' => $user_id]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    $avatar_path = 'img/demo/avatars/';
    if(!empty($user['avatar'])) {
        $path = $avatar_path.$user['avatar'];
    } else {
        $path = $avatar_path.'avatar-m.png';
    }

    return $path;
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
    $db = db_connect();
    $sql = "INSERT INTO users (email,password) VALUES (:email,:password)";
    $result = $db->prepare($sql);
    $result->execute(['email' => $email, 'password' => $password]);
    return $db->lastInsertId();
}

function edit_user_info($id, $username, $job, $phone, $address) {
    $db = db_connect();
    $sql = "UPDATE users SET username=:username, job=:job, phone=:phone, address=:address WHERE id=:id";
    $result = $db->prepare($sql);
    $result->execute(['username' => $username, 'job' => $job, 'phone' => $phone, 'address' => $address, 'id' => $id]);
}

function set_user_status($id, $status) {
    $db = db_connect();
    $sql = "UPDATE users SET status=:status WHERE id=:id";
    $result = $db->prepare($sql);
    $result->execute(['status' => $status, 'id' => $id]);
}

function check_and_delete_user_avatar($id) {
    $db = db_connect();
    $sql = "SELECT avatar FROM users WHERE id =:id";
    $statement = $db->prepare($sql);
    $statement->execute(['id' => $id]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    if(!empty($user['avatar'])) {
        unlink("img/demo/avatars/".$user['avatar']);
    }
}

function upload_user_avatar($id, $avatar) {
    $image = $avatar['name'];
    if(!empty($image)) {

        check_and_delete_user_avatar($id);

        $image_tmp_name = $avatar['tmp_name'];
        $extension = pathinfo($image, PATHINFO_EXTENSION);
        $uploaded_file_name = uniqid();
        $full_uploaded_file_name = $uploaded_file_name.".".$extension;
        move_uploaded_file($image_tmp_name, "img/demo/avatars/".$full_uploaded_file_name);

        $db = db_connect();
        $sql = "UPDATE users SET avatar=:avatar WHERE id=:id";
        $result = $db->prepare($sql);
        $result->execute(['avatar' => $full_uploaded_file_name, 'id' => $id]);
    }
}

function add_user_social_links($id, $vk, $telegram, $instagram) {
    $db = db_connect();
    $sql = "UPDATE users SET vk=:vk, telegram=:telegram, instagram=:instagram WHERE id=:id";
    $result = $db->prepare($sql);
    $result->execute(['vk' => $vk, 'telegram' => $telegram, 'instagram' => $instagram, 'id' => $id]);
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

function is_author($auth_user_id, $edit_user_id) {
    if($auth_user_id == $edit_user_id) {
        return true;
    }
}

?>