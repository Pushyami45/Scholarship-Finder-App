<?php
require("db.php");

$students_id = $_GET['students_id'] ?? '';

if (!$students_id) {
    echo json_encode(["status"=>"error","message"=>"Student ID required"]);
    exit;
}

$stmt = $conn->prepare("SELECT basic_completed, academic_completed, parent_completed, income_completed, category_completed FROM students WHERE id = ?");
$stmt->bind_param("i", $students_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $total_steps = 5;
    $completed_steps = $row['basic_completed'] + $row['academic_completed'] + 
                       $row['parent_completed'] + $row['income_completed'] + 
                       $row['category_completed'];
    
    $completion_percentage = ($completed_steps / $total_steps) * 100;
    
    echo json_encode([
        "status"=>"success",
        "data"=>[
            "basic_completed" => (bool)$row['basic_completed'],
            "academic_completed" => (bool)$row['academic_completed'],
            "parent_completed" => (bool)$row['parent_completed'],
            "income_completed" => (bool)$row['income_completed'],
            "category_completed" => (bool)$row['category_completed'],
            "completion_percentage" => $completion_percentage,
            "completed_steps" => $completed_steps,
            "total_steps" => $total_steps
        ]
    ]);
} else {
    echo json_encode(["status"=>"error","message"=>"Student not found"]);
}

$stmt->close();
$conn->close();
?>