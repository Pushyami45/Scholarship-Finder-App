<?php
require("db.php");

$scholarships = $conn->query("SELECT COUNT(*) AS total FROM scholarships")->fetch_assoc()['total'];
$students = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
$admins = $conn->query("SELECT COUNT(*) AS total FROM admin")->fetch_assoc()['total'];
$applications = $conn->query("SELECT COUNT(*) AS total FROM applications")->fetch_assoc()['total'];

// Get recent applications
$recent_apps = $conn->query("
SELECT a.*, s.name as student_name, sc.name as scholarship_name 
FROM applications a
JOIN students s ON a.student_id = s.id
JOIN scholarships sc ON a.scholarship_id = sc.id
ORDER BY a.applied_at DESC
LIMIT 10");

$recent_data = [];
while($row = $recent_apps->fetch_assoc()){ 
    $recent_data[] = $row; 
}

echo json_encode([
    "status"=>"success",
    "stats"=>[
        "scholarships"=>intval($scholarships),
        "students"=>intval($students),
        "admins"=>intval($admins),
        "applications"=>intval($applications)
    ],
    "recent_applications"=>$recent_data
]);

$conn->close();
?>