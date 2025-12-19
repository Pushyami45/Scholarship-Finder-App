<?php
require("db.php");

$student_id = $_GET['student_id'] ?? '';

if (!$student_id) {
    echo json_encode(["status"=>"error","message"=>"Student ID required"]);
    exit;
}

$query = "
SELECT a.*, s.name, s.provider, s.amount, s.deadline, s.description 
FROM applications a
JOIN scholarships s ON a.scholarship_id = s.id
WHERE a.student_id = $student_id
ORDER BY a.applied_at DESC";

$result = $conn->query($query);

$data = [];
while($row = $result->fetch_assoc()){ 
    $data[] = $row; 
}

echo json_encode([
    "status"=>"success",
    "count"=>count($data),
    "data"=>$data
]);

$conn->close();
?>