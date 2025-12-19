<?php
require("db.php");

$students_id = $_POST['students_id'] ?? '';
$parent_job_type = $_POST['parent_job_type'] ?? '';
$occupation = $_POST['occupation'] ?? '';

if (!$students_id || !$parent_job_type || !$occupation) {
    echo json_encode(["status"=>"error","message"=>"All fields required"]);
    exit;
}

$stmt = $conn->prepare("REPLACE INTO parent_details (students_id, parent_job_type, occupation) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $students_id, $parent_job_type, $occupation);

if($stmt->execute()){
    $conn->query("UPDATE students SET parent_completed = 1 WHERE id = $students_id");
    echo json_encode(["status"=>"success","message"=>"Parent details saved successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to save details"]);
}

$stmt->close();
$conn->close();
?>