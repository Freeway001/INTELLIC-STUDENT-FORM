<?php
require 'config.php';
require 'auth.php';

$total_students = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM students"
    )
)['total'];


$physical_students = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM students
         INNER JOIN student_types
         ON students.student_type_id = student_types.id
         WHERE student_types.name = 'Physical'"
    )
)['total'];


$online_students = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM students
         INNER JOIN student_types
         ON students.student_type_id = student_types.id
         WHERE student_types.name = 'Online'"
    )
)['total'];


$hybrid_students = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM students
         INNER JOIN student_types
         ON students.student_type_id = student_types.id
         WHERE student_types.name = 'Hybrid'"
    )
)['total'];

$financial_summary = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT
            COALESCE(SUM(amount_charged), 0) AS total_charged,
            COALESCE(SUM(amount_paid), 0) AS total_paid,
            COALESCE(SUM(balance), 0) AS total_balance
         FROM students"
    )
);

$total_charged = $financial_summary['total_charged'];
$total_paid = $financial_summary['total_paid'];
$total_balance = $financial_summary['total_balance'];

$recent_students = mysqli_query(
    $conn,
    "SELECT
        students.*,
        student_types.name AS student_type
     FROM students
     LEFT JOIN student_types
        ON students.student_type_id = student_types.id
     ORDER BY students.id DESC
     LIMIT 5"
);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">

    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            Intellic Academy
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto align-items-center">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="students.php">
                        Students
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="add_student.php">
                        Add Student
                    </a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-light btn-sm ms-2" href="logout.php">
                        Logout
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<div class="container page-container">

    <div class="row g-4">

        <div class="col-md-3">

            <div class="card stats-card">

                <h2>
                    <?php echo $total_students; ?>
                </h2>

                <p>Total Students</p>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stats-card">

                <h2>
                    <?php echo $physical_students; ?>
                </h2>

                <p>Physical</p>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stats-card">

                <h2>
                    <?php echo $online_students; ?>
                </h2>

                <p>Online</p>

            </div>

        </div>


        <div class="col-md-3">

            <div class="card stats-card">

                <h2>
                    <?php echo $hybrid_students; ?>
                </h2>

                <p>Hybrid</p>

            </div>

        </div>

    </div>

    <div class="row g-4 mt-1">

        <div class="col-md-4">

            <div class="card stats-card">

                <h2>
                    ₦<?php echo number_format($total_charged, 2); ?>
                </h2>

                <p>Total Amount Charged</p>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card stats-card">

                <h2>
                    ₦<?php echo number_format($total_paid, 2); ?>
                </h2>

                <p>Total Amount Paid</p>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card stats-card">

                <h2>
                    ₦<?php echo number_format($total_balance, 2); ?>
                </h2>

                <p>Total Balance</p>

            </div>

        </div>

    </div>

    <div class="card mt-4">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                Recently Registered Students
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered">

                    <thead>

                    <tr>

                        <th>Name</th>

                        <th>Student Type</th>

                        <th>Amount Paid</th>

                        <th>Registration Date</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php while ($row = mysqli_fetch_assoc($recent_students)): ?>

                        <tr>

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $row['first_name'] . ' ' .
                                    $row['last_name']
                                );

                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row['student_type']
                                );
                                ?>

                            </td>


                            <td>

                                ₦<?php
                                echo number_format(
                                    $row['amount_paid'],
                                    2
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row['registration_date']
                                );
                                ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>