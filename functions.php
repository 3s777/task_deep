<?php
/**
    Parameters:
    Description: создаем новый объект класса PDO
    Return value: object
 **/
function db_connect() {
    $db = new PDO('mysql:host=localhost;dbname=task_deep','root','');
    return $db;
}

/**
    Parameters:
        $input string
    Description: валидирует данные
    Return value: string
 **/
function validate_input($input) {
    return trim(strip_tags(htmlspecialchars($input)));
}

/**
    Parameters:
        $email str
    Description: получаем пользователя по email
    Return value: array
 **/
function get_user_by_email($email) {
    $db = db_connect();
    $sql = "SELECT id,email,password,role FROM users WHERE email <=>:email";
    $statement = $db->prepare($sql);
    $statement->execute(['email' => $email]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
}

/**
    Parameters:
        $id int
    Description: получаем пользователя по id
    Return value: array
 **/
function get_user_by_id($id) {
    $db = db_connect();
    $sql = "SELECT * FROM users WHERE id <=>:id";
    $statement = $db->prepare($sql);
    $statement->execute(['id' => $id]);
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    return $user;
}

/**
    Parameters:
    $user array
    Description: авторизуем пользователя
    Return value: null
 **/
function login($user) {
    $_SESSION['auth'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
}

/**
    Parameters:
    Description: получаем всех пользователей
    Return value: array
 **/
function get_all_users() {
    $db = db_connect();
    $sql = "SELECT * FROM users";
    $statement = $db->prepare($sql);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $users;
}

/**
    Parameters:
        $name string
        $message string
    Description: формирует сообщение пользователю и сохраняет в сессию
    Return value: array
 **/
function set_flash_message($name, $message) {
    $_SESSION['message'] = [
        'name' => $name,
        'message' => $message
    ];
}

/**
    Parameters:
        $path string
    Description: перенаправляем пользователя
    Return value: null
 **/
function redirect_to($path) {
    header("Location: $path");
    exit();
}

/**
    Parameters:
        $email string
        $password string
    Description: регистрируем нового пользователя
    Return value: int
 **/
function add_user($email, $password) {
    $db = db_connect();
    $sql = "INSERT INTO users (email,password) VALUES (:email,:password)";
    $result = $db->prepare($sql);
    $result->execute(['email' => $email, 'password' => $password]);
    return $db->lastInsertId();
}

/**
    Parameters:
        $id int
        $username string
        $job string
        $phone string
        $address string
    Description: редактируем данные пользователя
    Return value: null
 **/
function edit_user_info($id, $username, $job, $phone, $address) {
    $db = db_connect();
    $sql = "UPDATE users SET username=:username, job=:job, phone=:phone, address=:address WHERE id=:id";
    $result = $db->prepare($sql);
    $result->execute(['username' => $username, 'job' => $job, 'phone' => $phone, 'address' => $address, 'id' => $id]);
}

/**
    Parameters:
        $id int
        $email string
        $password string
    Description: редактируем логин и пароль
    Return value: null
 **/
function edit_credentials($id, $email, $password) {
    $db = db_connect();

    if(empty($password)) {
        $sql = "UPDATE users SET email=:email WHERE id=:id";
        $statement = $db->prepare($sql);
        $statement->bindValue('email', $email);
        $statement->bindValue('id', $id);
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET email=:email, password=:password WHERE id=:id";
        $statement = $db->prepare($sql);
        $statement->bindValue('email', $email);
        $statement->bindValue('password', $password);
        $statement->bindValue('id', $id);
    }

    $statement->execute();
}

/**
    Parameters:
        $id int
        $status string
    Description: устанавливает статус пользователю
    Return value: null
 **/
function set_user_status($id, $status) {
    $db = db_connect();
    $sql = "UPDATE users SET status=:status WHERE id=:id";
    $result = $db->prepare($sql);
    $result->execute(['status' => $status, 'id' => $id]);
}

/**
    Parameters:
        $status string
    Description: устанавливает иконку статуса пользователю
    Return value: string
 **/
function set_status_icon($status) {
    switch ($status) {
        case 'online':
            $icon = 'success';
            break;
        case 'busy':
            $icon = 'danger';
            break;
        default:
            $icon = 'secondary';
            break;
    }
    return $icon;
}

/**
    Parameters:
        $auth_user_id int
    Description: проверяет и удаляет аватар пользователя если он установлен
    Return value: null
 **/
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

/**
    Parameters:
        $id int
        $avatar file
    Description: обновляет аватар пользователю
    Return value: null
 **/
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

/**
    Parameters:
        $user_id int
    Description: формирует путь до аватара пользователя
    Return value: string
 **/
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

/**
    Parameters:
        $id int
        $vk string
        $telegram string
        $instagram string
    Description: добавляет социальные сети пользователям
    Return value: null
 **/
function add_user_social_links($id, $vk, $telegram, $instagram) {
    $db = db_connect();
    $sql = "UPDATE users SET vk=:vk, telegram=:telegram, instagram=:instagram WHERE id=:id";
    $result = $db->prepare($sql);
    $result->execute(['vk' => $vk, 'telegram' => $telegram, 'instagram' => $instagram, 'id' => $id]);
}

/**
    Parameters:
    Description: формируем сообщение для показа
    Return value: string
 **/
function display_flash_message() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-'.$_SESSION['message']['name'].' text-dark" role="alert">' . $_SESSION['message']['message'] . '</div>';
        unset($_SESSION['message']);
    }
}

/**
    Parameters:
    Description: проверяет авторизован ли пользователь
    Return value: boolean
 **/
function is_logged_in() {
    if(isset($_SESSION['auth']) && !empty($_SESSION['auth'])) {
        return true;
    }
}

/**
    Parameters:
        $role string
    Description: проверяет роль пользователя
    Return value: boolean
 **/
function check_role($role) {
    if($_SESSION['auth']['role'] == $role) {
        return true;
    }
}

/**
    Parameters:
        $auth_user_id int
        $edit_user_id int
    Description: проверяет является ли пользотель авторам записи
    Return value: boolean
 **/
function is_author($auth_user_id, $edit_user_id) {
    if($auth_user_id == $edit_user_id) {
        return true;
    }
}

/**
    Parameters: $id int
    Description: удалить пользователя
    Return value: null
 **/
function delete_user($id) {
    check_and_delete_user_avatar($id);

    $db = db_connect();
    $sql = "DELETE FROM users WHERE id=:id";
    $result = $db->prepare($sql);
    $result->execute(['id' => $id]);
}

?>