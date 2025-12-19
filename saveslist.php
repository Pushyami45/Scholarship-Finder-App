<?php
require("db.php");

$students_id = $_GET['students_id'] ?? '';

if (!$students_id) {
    echo json_encode(["status"=>"error","message"=>"Student ID required"]);
    exit;
}

$query = "
SELECT s.*, ss.saved_at 
FROM saved_scholarships ss 
JOIN scholarships s ON ss.scholarship_id = s.id
WHERE ss.students_id = $students_id
ORDER BY ss.saved_at DESC";

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