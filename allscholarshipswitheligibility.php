<?php
require("db.php");

$students_id = $_GET['students_id'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 20;
$offset = ($page - 1) * $limit;

if (!$students_id) {
    echo json_encode(["status"=>"error","message"=>"Student ID required"]);
    exit;
}

// Check if profile is complete
$profile_check = $conn->query("SELECT basic_completed, academic_completed, parent_completed, 
income_completed, category_completed FROM students WHERE id = $students_id")->fetch_assoc();

$profile_complete = $profile_check && $profile_check['basic_completed'] && 
                    $profile_check['academic_completed'] && $profile_check['parent_completed'] && 
                    $profile_check['income_completed'] && $profile_check['category_completed'];

if (!$profile_complete) {
    // Return all scholarships without eligibility check
    $result = $conn->query("SELECT * FROM scholarships WHERE deadline >= CURDATE() ORDER BY deadline ASC LIMIT $limit OFFSET $offset");
    $total = $conn->query("SELECT COUNT(*) as count FROM scholarships WHERE deadline >= CURDATE()")->fetch_assoc()['count'];
    
    $data = [];
    while($row = $result->fetch_assoc()){
        $row['eligibility_status'] = 'PROFILE_INCOMPLETE';
        $data[] = $row;
    }
    
    echo json_encode([
        "status"=>"success",
        "profile_complete"=>false,
        "message"=>"Complete your profile to check eligibility",
        "count"=>count($data),
        "data"=>$data,
        "pagination"=>[
            "current_page"=>intval($page),
            "total_records"=>intval($total),
            "total_pages"=>ceil($total/$limit)
        ]
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
$student = $student_result->fetch_assoc();

// Get all scholarships with pagination
$scholarships_query = "SELECT * FROM scholarships WHERE deadline >= CURDATE() ORDER BY deadline ASC LIMIT $limit OFFSET $offset";
$total = $conn->query("SELECT COUNT(*) as count FROM scholarships WHERE deadline >= CURDATE()")->fetch_assoc()['count'];

$scholarships_result = $conn->query($scholarships_query);

$data = [];

while($scholarship = $scholarships_result->fetch_assoc()) {
    // Check eligibility
    $is_eligible = 
        ($scholarship['parent_job_type'] == 'Any' || $student['parent_job_type'] == $scholarship['parent_job_type']) &&
        ($scholarship['income_range'] == 'Any' || $student['income_range'] == $scholarship['income_range']) &&
        ($scholarship['category'] == 'General' || $student['category'] == $scholarship['category']) &&
        ($scholarship['education_level'] == 'Any' || $student['education_level'] == $scholarship['education_level']);
    
    $scholarship['eligibility_status'] = $is_eligible ? 'ELIGIBLE' : 'NOT_ELIGIBLE';
    
    // Check if already applied
    $applied_check = $conn->query("SELECT id FROM applications WHERE student_id = $students_id AND scholarship_id = {$scholarship['id']}");
    $scholarship['already_applied'] = ($applied_check->num_rows > 0);
    
    // Check if saved
    $saved_check = $conn->query("SELECT id FROM saved_scholarships WHERE students_id = $students_id AND scholarship_id = {$scholarship['id']}");
    $scholarship['is_saved'] = ($saved_check->num_rows > 0);
    
    $data[] = $scholarship;
}

echo json_encode([
    "status"=>"success",
    "profile_complete"=>true,
    "count"=>count($data),
    "data"=>$data,
    "student_profile"=>[
        "parent_job_type"=>$student['parent_job_type'],
        "income_range"=>$student['income_range'],
        "category"=>$student['category'],
        "education_level"=>$student['education_level']
    ],
    "pagination"=>[
        "current_page"=>intval($page),
        "total_records"=>intval($total),
        "total_pages"=>ceil($total/$limit)
    ]
]);

$conn->close();
?>