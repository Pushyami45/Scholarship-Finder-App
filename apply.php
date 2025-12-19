<?php
require("db.php");

$data = json_decode(file_get_contents("php://input"), true);

$student_id = $data['student_id'] ?? $_POST['student_id'] ?? '';
$scholarship_id = $data['scholarship_id'] ?? $_POST['scholarship_id'] ?? '';

if(!$student_id || !$scholarship_id){
    echo json_encode(["status"=>"error","message"=>"Student ID and Scholarship ID required"]);
    exit;
}

// Check if already applied
$check = $conn->query("SELECT * FROM applications WHERE student_id = $student_id AND scholarship_id = $scholarship_id");

if ($check->num_rows > 0) {
    echo json_encode(["status"=>"error","message"=>"Already applied to this scholarship"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO applications (student_id, scholarship_id, status) VALUES (?, ?, 'Pending')");
$stmt->bind_param("ii", $student_id, $scholarship_id);

if($stmt->execute()){
    $application_id = $conn->insert_id;
    echo json_encode([
        "status"=>"success",
        "message"=>"Application submitted successfully",
        "application_id"=>$application_id
    ]);
} else {
    echo json_encode(["status"=>"error","message"=>"Application submission failed"]);
}

$stmt->close();
$conn->close();
?>