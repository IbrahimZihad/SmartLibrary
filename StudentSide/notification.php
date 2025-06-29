<?php
// send_notifications.php
include('../dbconnection.php');

// Days before due date to notify
$days_before_due = 3;

// Get today's date
$today = date("Y-m-d");

// Corrected SQL to use 'booklist' instead of 'books'
$query = "
SELECT s.name, s.email, b.book_id, b.due_date, bl.book_name
FROM borrowhistory b
JOIN students s ON b.student_id = s.student_id
JOIN booklist bl ON b.book_id = bl.book_id
WHERE DATEDIFF(b.due_date, '$today') BETWEEN 0 AND $days_before_due
";

$result = mysqli_query($con, $query);

// Check if query succeeded
if (!$result) {
    die("Query Failed: " . mysqli_error($con));
}

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $to = $row['email'];
        $subject = "Library Due Date Reminder";
        $message = "Dear " . $row['name'] . ",\n\n"
                 . "This is a reminder that your borrowed book \"" . $row['book_name'] . "\" is due on " . $row['due_date'] . ".\n"
                 . "Please return it on time to avoid penalties.\n\n"
                 . "Regards,\nLibrary Management System";

        $headers = "From: library@yourdomain.com";

        // Send email
        mail($to, $subject, $message, $headers);
    }

    echo "Notifications sent.";
} else {
    echo "No due books within $days_before_due days.";
}
?>
