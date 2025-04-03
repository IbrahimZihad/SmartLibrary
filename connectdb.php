<?php
$host = "127.0.0.1";  // Change if your database is hosted elsewhere
$user = "root";       // Your database username
$pass = "";           // Your database password
$dbname = "librarydb"; // Replace with your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Uncomment to check if the connection is successful
// echo "Connected successfully";
?>
