<?php
require("db.php");

$students_id = $_POST['students_id'] ?? '';
$category = $_POST['category'] ?? '';

if (!$students_id || !$category) {
    echo json_encode(["status"=>"error","message"=>"All fields required"]);
    exit;
}

$stmt = $conn->prepare("REPLACE INTO category_details (students_id, category) VALUES (?, ?)");
$stmt->bind_param("is", $students_id, $category);

if($stmt->execute()){
    $conn->query("UPDATE students SET category_completed = 1 WHERE id = $students_id");
    echo json_encode(["status"=>"success","message"=>"Category details saved successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to save details"]);
}

$stmt->close();
$conn->close();
?>