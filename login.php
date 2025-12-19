<?php
require("db.php");

$type     = $_POST['type'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$type || !$username || !$password) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

if ($type === "admin") {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            echo json_encode([
                "status" => "success",
                "type" => "admin",
                "message" => "Admin login successful",
                "data" => [
                    "id" => $row['id'],
                    "username" => $row['username'],
                    "email" => $row['email'],
                    "mobile" => $row['mobile']
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Admin not found"]);
    }

} elseif ($type === "student") {
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            echo json_encode([
                "status" => "success",
                "type" => "student",
                "message" => "Student login successful",
                "data" => [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "email" => $row['email'],
                    "mobile" => $row['mobile'],
                    "basic_completed" => $row['basic_completed'],
                    "academic_completed" => $row['academic_completed'],
                    "parent_completed" => $row['parent_completed'],
                    "income_completed" => $row['income_completed'],
                    "category_completed" => $row['category_completed']
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Student not found"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Invalid login type"]);
}

$stmt->close();
$conn->close();
?>