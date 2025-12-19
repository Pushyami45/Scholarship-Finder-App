<?php
require("db.php");

$students_id = $_POST['students_id'] ?? '';
$income_range = $_POST['income_range'] ?? '';

if (!$students_id || !$income_range) {
    echo json_encode(["status"=>"error","message"=>"All fields required"]);
    exit;
}

$stmt = $conn->prepare("REPLACE INTO income_details (students_id, income_range) VALUES (?, ?)");
$stmt->bind_param("is", $students_id, $income_range);

if($stmt->execute()){
    $conn->query("UPDATE students SET income_completed = 1 WHERE id = $students_id");
    echo json_encode(["status"=>"success","message"=>"Income details saved successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to save details"]);
}

$stmt->close();
$conn->close();
?>