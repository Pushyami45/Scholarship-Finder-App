<?php
require("db.php");

$students_id = $_POST['students_id'] ?? '';
$scholarship_id = $_POST['scholarship_id'] ?? '';

if (!$students_id || !$scholarship_id) {
    echo json_encode(["status"=>"error","message"=>"Student ID and Scholarship ID required"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM saved_scholarships WHERE students_id = ? AND scholarship_id = ?");
$stmt->bind_param("ii", $students_id, $scholarship_id);

if($stmt->execute()){
    echo json_encode(["status"=>"success","message"=>"Scholarship removed from saved list"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to remove"]);
}

$stmt->close();
$conn->close();
?>