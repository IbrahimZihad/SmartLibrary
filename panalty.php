<?php
include('dbconnection.php');

// Calculate total penalty per student where return_date > due_date
$query = "
SELECT 
    s.name,
    bh.student_id,
    SUM(
        CASE 
            WHEN bh.return_date > bh.due_date 
            THEN DATEDIFF(bh.return_date, bh.due_date) * 10 
            ELSE 0 
        END
    ) AS total_penalty
FROM 
    borrowhistory bh
JOIN 
    students s ON bh.student_id = s.student_id
GROUP BY 
    bh.student_id
";

$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Penalty Status</title>
    <meta http-equiv="refresh" content="86400">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h2>Penalty Status of Students</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Student ID</th>
                <th>Total Penalty</th>
                <th>Status</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['total_penalty']) ?> TK</td>
                    <td><?= $row['total_penalty'] > 0 ? 'Unpaid' : 'Clear' ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No penalty records found.</p>
    <?php endif; ?>
</body>
</html>
