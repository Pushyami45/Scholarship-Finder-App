<?php
require("db.php");

$id = $_POST['id'] ?? '';

if (!$id) {
    echo json_encode(["status"=>"error","message"=>"Scholarship ID missing"]);
    exit;
}

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

$stmt = $conn->prepare("UPDATE scholarships SET 
name=?, provider=?, amount=?, deadline=?, description=?, 
parent_job_type=?, income_range=?, category=?, education_level=?,
eligibility_criteria=?, benefits=?, documents_required=?, application_process=?, contact_info=?
WHERE id=?");

$stmt->bind_param("ssssssssssssssi", $name, $provider, $amount, $deadline, $description,
$parent_job_type, $income_range, $category, $education_level, $eligibility_criteria,
$benefits, $documents_required, $application_process, $contact_info, $id);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Scholarship updated successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Update failed"]);
}

$stmt->close();
$conn->close();
?>