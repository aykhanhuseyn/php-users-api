<?php

header("Content-Type: application/json");

// die(json_encode($_SERVER));

$method = $_SERVER['REQUEST_METHOD'];
$request = @explode('/', trim($_SERVER['REQUEST_URI'], '/'));

// Connect to the database
$db_host = "localhost:3306";
$db_name = "users";
$db_user = "root";
$db_pass = "";
$db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);

// Handle the request
switch ($method) {
    case 'GET':
        $stmt = $db->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($users);
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $db->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->execute();
        $user_id = $db->lastInsertId();
        $user = array("id" => $user_id, "name" => $data['name'], "email" => $data['email']);
        echo json_encode($user);
        break;
    case 'PUT':
        $user_id = intval($request[1]);
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $user = array("id" => $user_id, "name" => $data['name'], "email" => $data['email']);
        echo json_encode($user);
        break;
    default:
        http_response_code(400);
        echo json_encode(array("message" => "Invalid request method"));
        break;
}

?>