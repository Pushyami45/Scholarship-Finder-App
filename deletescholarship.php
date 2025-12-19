<?php
require("db.php");

$id = $_POST['id'] ?? '';

if(!$id){
    echo json_encode(["status"=>"error","message"=>"Scholarship ID missing"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM scholarships WHERE id = ?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    echo json_encode(["status"=>"success","message"=>"Scholarship deleted successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Delete failed"]);
}

$stmt->close();
$conn->close();
?>