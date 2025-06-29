<?php
include 'connectdb.php';

// Step 1: Update individual penalties in borrowhistory
$updateBorrowQuery = "
    UPDATE borrowhistory
    SET penalty = CASE 
        WHEN return_date > due_date 
        THEN DATEDIFF(return_date, due_date) * 10 
        ELSE 0 
    END
";
$conn->query($updateBorrowQuery);

// Step 2: Recalculate total penalties per student
$getStudentIDs = "SELECT DISTINCT student_id FROM borrowhistory";
$result = $conn->query($getStudentIDs);

while ($row = $result->fetch_assoc()) {
    $student_id = $row['student_id'];

    // Sum up all penalties for this student
    $sumQuery = "
        SELECT SUM(penalty) AS total_penalty 
        FROM borrowhistory 
        WHERE student_id = '$student_id'
    ";
    $sumResult = $conn->query($sumQuery);
    $totalPenalty = floatval($sumResult->fetch_assoc()['total_penalty']);

    if ($totalPenalty > 0) {
        // Insert or update the penalty table
        $checkPenalty = $conn->query("SELECT * FROM penalty WHERE student_id = '$student_id'");
        if ($checkPenalty->num_rows > 0) {
            $conn->query("UPDATE penalty SET total_penalty = $totalPenalty, status = 'not paid' WHERE student_id = '$student_id'");
        } else {
            $conn->query("INSERT INTO penalty (student_id, total_penalty, status) VALUES ('$student_id', $totalPenalty, 'not paid')");
        }
    } else {
        // If no penalty exists, mark status as paid (or insert 0 if not exists)
        $checkPenalty = $conn->query("SELECT * FROM penalty WHERE student_id = '$student_id'");
        if ($checkPenalty->num_rows > 0) {
            $conn->query("UPDATE penalty SET total_penalty = 0, status = 'paid' WHERE student_id = '$student_id'");
        } else {
            $conn->query("INSERT INTO penalty (student_id, total_penalty, status) VALUES ('$student_id', 0, 'paid')");
        }
    }
}

?>
