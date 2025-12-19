<?php
require("db.php");

$students_id = $_POST['students_id'] ?? '';
$scholarship_id = $_POST['scholarship_id'] ?? '';

if (!$students_id || !$scholarship_id) {
    echo json_encode(["status"=>"error","message"=>"Student ID and Scholarship ID required"]);
    exit;
}

// Check if profile is complete
$profile_check = $conn->query("SELECT basic_completed, academic_completed, parent_completed, 
income_completed, category_completed FROM students WHERE id = $students_id")->fetch_assoc();

if (!$profile_check['basic_completed'] || !$profile_check['academic_completed'] || 
    !$profile_check['parent_completed'] || !$profile_check['income_completed'] || 
    !$profile_check['category_completed']) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Please complete your profile first",
        "eligible"=>"INCOMPLETE_PROFILE"
    ]);
    exit;
}

// Get student details
$student_query = "
SELECT pd.parent_job_type, id.income_range, cd.category, a.education_level 
FROM parent_details pd
JOIN income_details id ON pd.students_id = id.students_id
JOIN category_details cd ON cd.students_id = id.students_id
JOIN academic a ON a.students_id = id.students_id
WHERE pd.students_id = $students_id";

$student_result = $conn->query($student_query);

if (!$student_result || $student_result->num_rows == 0) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Student details not found",
        "eligible"=>"NO"
    ]);
    exit;
}

$student = $student_result->fetch_assoc();

// Get scholarship criteria
$scholarship = $conn->query("SELECT * FROM scholarships WHERE id = $scholarship_id")->fetch_assoc();

if (!$scholarship) {
    echo json_encode(["status"=>"error","message"=>"Scholarship not found"]);
    exit;
}

// Check eligibility
$eligible = 
    ($scholarship['parent_job_type'] == 'Any' || $student['parent_job_type'] == $scholarship['parent_job_type']) &&
    ($scholarship['income_range'] == 'Any' || $student['income_range'] == $scholarship['income_range']) &&
    ($scholarship['category'] == 'General' || $student['category'] == $scholarship['category']) &&
    ($scholarship['education_level'] == 'Any' || $student['education_level'] == $scholarship['education_level']);

echo json_encode([
    "status"=>"success",
    "eligible"=> $eligible ? "YES" : "NO",
    "student_criteria"=>[
        "parent_job_type"=>$student['parent_job_type'],
        "income_range"=>$student['income_range'],
        "category"=>$student['category'],
        "education_level"=>$student['education_level']
    ],
    "scholarship_criteria"=>[
        "parent_job_type"=>$scholarship['parent_job_type'],
        "income_range"=>$scholarship['income_range'],
        "category"=>$scholarship['category'],
        "education_level"=>$scholarship['education_level']
    ]
]);

$conn->close();
?>