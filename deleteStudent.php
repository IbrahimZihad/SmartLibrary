<?php
include 'connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = $conn->real_escape_string($_POST['student_id']);

    // Delete from related tables first due to FK constraints
    $conn->query("DELETE FROM studentimage WHERE student_id = '$student_id'");
    $conn->query("DELETE FROM borrowhistory WHERE student_id = '$student_id'");
    $conn->query("DELETE FROM penalty WHERE student_id = '$student_id'");

    // Then delete from students
    $conn->query("DELETE FROM students WHERE student_id = '$student_id'");

    // Redirect back to student list
    header("Location: studentList.php");
    exit;
} else {
    // Redirect if accessed directly
    header("Location: studentList.php");
    exit;
}
?>