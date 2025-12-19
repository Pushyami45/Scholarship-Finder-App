<?php
require("db.php");

$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 20;
$offset = ($page - 1) * $limit;

$result = $conn->query("SELECT * FROM scholarships ORDER BY id DESC LIMIT $limit OFFSET $offset");
$total = $conn->query("SELECT COUNT(*) as count FROM scholarships")->fetch_assoc()['count'];

$data = [];
while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode([
    "status"=>"success",
    "data"=>$data,
    "pagination"=>[
        "current_page"=>intval($page),
        "total_records"=>intval($total),
        "total_pages"=>ceil($total/$limit)
    ]
]);

$conn->close();
?>