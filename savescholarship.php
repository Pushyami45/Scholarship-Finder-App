<?php
require("db.php");

$students_id = $_POST['students_id'] ?? '';
$scholarship_id = $_POST['scholarship_id'] ?? '';

if (!$students_id || !$scholarship_id) {
    echo json_encode(["status"=>"error","message"=>"Student ID and Scholarship ID required"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO saved_scholarships (students_id, scholarship_id) VALUES (?, ?)");
$stmt->bind_param("ii", $students_id, $scholarship_id);

if($stmt->execute()){
    echo json_encode(["status"=>"success","message"=>"Scholarship saved successfully"]);
} else {
    if($conn->errno == 1062) {
        echo json_encode(["status"=>"error","message"=>"Already saved"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Failed to save"]);
    }
}

$stmt->close();
$conn->close();
?>