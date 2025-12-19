<?php
require("db.php");

$students_id = $_POST['students_id'] ?? '';
$dob = $_POST['dob'] ?? '';
$gender = $_POST['gender'] ?? '';
$state = $_POST['state'] ?? '';
$district = $_POST['district'] ?? '';
$address = $_POST['address'] ?? '';

if (!$students_id || !$dob || !$gender || !$state || !$district || !$address) {
    echo json_encode(["status"=>"error","message"=>"All fields required"]);
    exit;
}

$stmt = $conn->prepare("REPLACE INTO students_profile (students_id, dob, gender, state, district, address) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $students_id, $dob, $gender, $state, $district, $address);

if($stmt->execute()){
    $conn->query("UPDATE students SET basic_completed = 1 WHERE id = $students_id");
    echo json_encode(["status"=>"success","message"=>"Basic profile saved successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to save profile"]);
}

$stmt->close();
$conn->close();
?>