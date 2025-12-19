<?php
require("db.php");

$name = $_POST['name'] ?? '';
$provider = $_POST['provider'] ?? '';
$amount = $_POST['amount'] ?? '';
$deadline = $_POST['deadline'] ?? '';
$description = $_POST['description'] ?? '';
$parent_job_type = $_POST['parent_job_type'] ?? '';
$income_range = $_POST['income_range'] ?? '';
$category = $_POST['category'] ?? '';
$education_level = $_POST['education_level'] ?? '';
$eligibility_criteria = $_POST['eligibility_criteria'] ?? '';
$benefits = $_POST['benefits'] ?? '';
$documents_required = $_POST['documents_required'] ?? '';
$application_process = $_POST['application_process'] ?? '';
$contact_info = $_POST['contact_info'] ?? '';

if(!$name || !$provider || !$parent_job_type || !$income_range || !$category || !$education_level){
    echo json_encode(["status"=>"error","message"=>"Required fields missing"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO scholarships 
(name, provider, amount, deadline, description, parent_job_type, income_range, category, education_level, 
eligibility_criteria, benefits, documents_required, application_process, contact_info) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("ssssssssssssss", $name, $provider, $amount, $deadline, $description, 
$parent_job_type, $income_range, $category, $education_level, $eligibility_criteria, 
$benefits, $documents_required, $application_process, $contact_info);

if ($stmt->execute()) {
    $scholarship_id = $conn->insert_id;
    echo json_encode([
        "status"=>"success",
        "message"=>"Scholarship added successfully",
        "scholarship_id"=>$scholarship_id
    ]);
} else {
    echo json_encode(["status"=>"error","message"=>"Failed to add scholarship"]);
}

$stmt->close();
$conn->close();
?>