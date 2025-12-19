<?php
require("db.php");

$students_id = $_POST['students_id'] ?? '';
$education_level = $_POST['education_level'] ?? '';
$college = $_POST['college'] ?? '';
$course = $_POST['course'] ?? '';
$year = $_POST['year'] ?? '';

if (!$students_id || !$education_level || !$college || !$course || !$year) {
    echo json_encode(["status"=>"error","message"=>"All fields required"]);
    exit;
}

$stmt = $conn->prepare("REPLACE INTO academic (students_id, education_level, college, course, year) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $students_id, $education_level, $college, $course, $year);

if($stmt->execute()){
    $conn->query("UPDATE students SET academic_completed = 1 WHERE id = $students_id");
    echo json_encode(["status"=>"success","message"=>"Academic details saved successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to save details"]);
}

$stmt->close();
$conn->close();
?>