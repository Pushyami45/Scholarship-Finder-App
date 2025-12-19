<?php
require("db.php");

// Get trending scholarships (by application count)
$trending_query = "
SELECT s.*, COUNT(a.id) as application_count 
FROM scholarships s
LEFT JOIN applications a ON s.id = a.scholarship_id
GROUP BY s.id
ORDER BY application_count DESC, s.deadline ASC
LIMIT 10";

$trending_result = $conn->query($trending_query);

$trending = [];
while($row = $trending_result->fetch_assoc()){ 
    $trending[] = $row; 
}

// Get upcoming deadlines
$upcoming_query = "
SELECT * FROM scholarships 
WHERE deadline >= CURDATE()
ORDER BY deadline ASC
LIMIT 10";

$upcoming_result = $conn->query($upcoming_query);

$upcoming = [];
while($row = $upcoming_result->fetch_assoc()){ 
    $upcoming[] = $row; 
}

// Get latest scholarships
$latest = $conn->query("SELECT * FROM scholarships ORDER BY created_at DESC LIMIT 10");
$latest_data = [];
while($row = $latest->fetch_assoc()){ 
    $latest_data[] = $row; 
}

echo json_encode([
    "status"=>"success",
    "trending"=>$trending,
    "upcoming_deadlines"=>$upcoming,
    "latest_scholarships"=>$latest_data
]);

$conn->close();
?>