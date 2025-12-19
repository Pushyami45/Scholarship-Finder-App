<?php
require("db.php");

$query = $_POST['query'] ?? $_GET['query'] ?? '';

if (!$query) {
    echo json_encode(["status"=>"error","message"=>"Search query required"]);
    exit;
}

$search = "%" . $query . "%";

$stmt = $conn->prepare("SELECT * FROM scholarships WHERE 
name LIKE ? OR provider LIKE ? OR description LIKE ? OR category LIKE ?");
$stmt->bind_param("ssss", $search, $search, $search, $search);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while($row = $result->fetch_assoc()){ 
    $data[] = $row; 
}

echo json_encode([
    "status"=>"success",
    "count"=>count($data),
    "data"=>$data
]);

$stmt->close();
$conn->close();
?>