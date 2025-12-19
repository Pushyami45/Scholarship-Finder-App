<?php
require("db.php");

$students_id = $_GET['students_id'] ?? '';

if (!$students_id) {
    echo json_encode(["status"=>"error","message"=>"Student ID required"]);
    exit;
}

// Check if profile is complete
$profile_check = $conn->query("SELECT basic_completed, academic_completed, parent_completed, 
income_completed, category_completed FROM students WHERE id = $students_id")->fetch_assoc();

if (!$profile_check || !$profile_check['basic_completed'] || !$profile_check['academic_completed'] || 
    !$profile_check['parent_completed'] || !$profile_check['income_completed'] || 
    !$profile_check['category_completed']) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Please complete your profile to view eligible scholarships"
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
        "message"=>"Student profile details not found"
    ]);
    exit;
}

$student = $student_result->fetch_assoc();

// Get all scholarships
$scholarships_query = "SELECT * FROM scholarships WHERE deadline >= CURDATE() ORDER BY deadline ASC";
$scholarships_result = $conn->query($scholarships_query);

$eligible = [];
$count = 0;

while($scholarship = $scholarships_result->fetch_assoc()) {
    // Check eligibility for each scholarship
    $is_eligible = 
        ($scholarship['parent_job_type'] == 'Any' || $student['parent_job_type'] == $scholarship['parent_job_type']) &&
        ($scholarship['income_range'] == 'Any' || $student['income_range'] == $scholarship['income_range']) &&
        ($scholarship['category'] == 'General' || $student['category'] == $scholarship['category']) &&
        ($scholarship['education_level'] == 'Any' || $student['education_level'] == $scholarship['education_level']);
    
    if ($is_eligible) {
        // Check if already applied
        $applied_check = $conn->query("SELECT id FROM applications WHERE student_id = $students_id AND scholarship_id = {$scholarship['id']}");
        $scholarship['already_applied'] = ($applied_check->num_rows > 0);
        
        // Check if saved
        $saved_check = $conn->query("SELECT id FROM saved_scholarships WHERE students_id = $students_id AND scholarship_id = {$scholarship['id']}");
        $scholarship['is_saved'] = ($saved_check->num_rows > 0);
        
        $eligible[] = $scholarship;
        $count++;
    }
}

echo json_encode([
    "status"=>"success",
    "count"=>$count,
    "data"=>$eligible,
    "student_profile"=>[
        "parent_job_type"=>$student['parent_job_type'],
        "income_range"=>$student['income_range'],
        "category"=>$student['category'],
        "education_level"=>$student['education_level']
    ]
]);

$conn->close();
?>