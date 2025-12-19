<?php
require("db.php");

$id = $_GET['id'] ?? '';

if (!$id) {
    echo json_encode(["status" => "error", "message" => "Scholarship ID required"]);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM scholarships WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()){
    echo json_encode(["status"=>"success","data"=>$row]);
} else {
    echo json_encode(["status"=>"error","message"=>"Scholarship not found"]);
}

$stmt->close();
$conn->close();
?>