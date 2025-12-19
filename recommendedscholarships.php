<?php
require("db.php");

$students_id = $_GET['students_id'] ?? '';

if (!$students_id) {
    echo json_encode(["status"=>"error","message"=>"Student ID required"]);
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
        "message"=>"Please complete your profile to get recommendations"
    ]);
    exit;
}

$student = $student_result->fetch_assoc();

// Find matching scholarships
$scholarships_query = "
SELECT * FROM scholarships 
WHERE (parent_job_type = '{$student['parent_job_type']}' OR parent_job_type = 'Any')
AND (income_range = '{$student['income_range']}' OR income_range = 'Any')
AND (category = '{$student['category']}' OR category = 'General')
AND (education_level = '{$student['education_level']}' OR education_level = 'Any')
AND deadline >= CURDATE()
ORDER BY deadline ASC
LIMIT 20";

$result = $conn->query($scholarships_query);

$data = [];
while($row = $result->fetch_assoc()){ 
    $data[] = $row; 
}

echo json_encode([
    "status"=>"success",
    "count"=>count($data),
    "data"=>$data,
    "student_profile"=>[
        "parent_job_type"=>$student['parent_job_type'],
        "income_range"=>$student['income_range'],
        "category"=>$student['category'],
        "education_level"=>$student['education_level']
    ]
]);

$conn->close();
?>