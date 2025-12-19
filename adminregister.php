<?php
require("db.php");

$username = $_POST["username"] ?? '';
$email    = $_POST["email"] ?? '';
$mobile   = $_POST["mobile"] ?? '';
$password = $_POST["password"] ?? '';

if (!$username || !$email || !$mobile || !$password) {
    echo json_encode(["status" => "error", "message" => "All fields required"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status"=>"error","message"=>"Invalid email format"]);
    exit;
}

if (!preg_match("/^[6-9][0-9]{9}$/", $mobile)) {
    echo json_encode(["status"=>"error","message"=>"Invalid mobile number"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin (username, email, mobile, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $mobile, $hashedPassword);

if($stmt->execute()){
    $admin_id = $conn->insert_id;
    echo json_encode([
        "status"=>"success",
        "message"=>"Admin registered successfully",
        "admin_id"=>$admin_id
    ]);
} else {
    if($conn->errno == 1062) {
        echo json_encode(["status"=>"error","message"=>"Username or email already exists"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Registration failed"]);
    }
}

$stmt->close();
$conn->close();
?>