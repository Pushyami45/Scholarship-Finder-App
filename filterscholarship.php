<?php
require("db.php");

$parent_job_type = $_POST['parent_job_type'] ?? '';
$income_range = $_POST['income_range'] ?? '';
$category = $_POST['category'] ?? '';
$education_level = $_POST['education_level'] ?? '';

$conditions = [];
$params = [];
$types = "";

if ($parent_job_type) {
    $conditions[] = "parent_job_type = ?";
    $params[] = $parent_job_type;
    $types .= "s";
}

if ($income_range) {
    $conditions[] = "income_range = ?";
    $params[] = $income_range;
    $types .= "s";
}

if ($category) {
    $conditions[] = "category = ?";
    $params[] = $category;
    $types .= "s";
}

if ($education_level) {
    $conditions[] = "education_level = ?";
    $params[] = $education_level;
    $types .= "s";
}

if (empty($conditions)) {
    echo json_encode(["status"=>"error","message"=>"At least one filter required"]);
    exit;
}

$sql = "SELECT * FROM scholarships WHERE " . implode(" AND ", $conditions);

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

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