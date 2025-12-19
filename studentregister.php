<?php
require("db.php");

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$password = $_POST['password'] ?? '';

if(!$name || !$email || !$mobile || !$password){
    echo json_encode(["status"=>"error","message"=>"All fields required"]);
    exit;
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    echo json_encode(["status"=>"error","message"=>"Invalid email format"]);
    exit;
}

if(!preg_match("/^[6-9][0-9]{9}$/", $mobile)){
    echo json_encode(["status"=>"error","message"=>"Invalid mobile number"]);
    exit;
}

// Hash password for security
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO students (name, email, mobile, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $mobile, $hashedPassword);

if($stmt->execute()){
    $student_id = $conn->insert_id;
    echo json_encode([
        "status"=>"success",
        "message"=>"Student registered successfully",
        "student_id"=>$student_id
    ]);
} else {
    if($conn->errno == 1062) {
        echo json_encode(["status"=>"error","message"=>"Email already exists"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Registration failed"]);
    }
}

$stmt->close();
$conn->close();
?>