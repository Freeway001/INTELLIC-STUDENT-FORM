<?php
require 'config.php';
require 'auth.php';

$sql = "SELECT
            students.*,
            student_types.name AS student_type
        FROM students
        LEFT JOIN student_types
            ON students.student_type_id = student_types.id
        WHERE students.balance > 0
        ORDER BY students.balance DESC, students.id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}

$total_debtors = mysqli_num_rows($result);


$totals_sql = "SELECT
                    COALESCE(SUM(amount_charged), 0) AS total_charged,
                    COALESCE(SUM(amount_paid), 0) AS total_paid,
                    COALESCE(SUM(balance), 0) AS total_balance
               FROM students
               WHERE balance > 0";

$totals_result = mysqli_query($conn, $totals_sql);

if (!$totals_result) {
    die("Database error: " . mysqli_error($conn));
}

$totals = mysqli_fetch_assoc($totals_result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Debtors - Intellic Academy</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="style.css">

</head>

<body>


<nav class="navbar navbar-expand-lg navbar-dark bg-primary">

    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            Intellic Academy
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >
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

    
    <div class="row g-3 mb-4">

        
        <div class="col-md-4">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Debtors
                    </h6>

                    <h3 class="fw-bold mb-0">
                        <?php echo $total_debtors; ?>
                    </h3>

                </div>

            </div>

        </div>


        
        <div class="col-md-4">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <h6 class="text-muted">
                        Amount Charged
                    </h6>

                    <h3 class="fw-bold mb-0">
                        ₦<?php echo number_format((float)$totals['total_charged'], 2); ?>
                    </h3>

                </div>

            </div>

        </div>


        
        <div class="col-md-4">

            <div class="card shadow-sm h-100">

                <div class="card-body">

                    <h6 class="text-muted">
                        Total Outstanding
                    </h6>

                    <h3 class="fw-bold text-danger mb-0">
                        ₦<?php echo number_format((float)$totals['total_balance'], 2); ?>
                    </h3>

                </div>

            </div>

        </div>

    </div>


    
    <div class="card shadow-sm">

        <div class="card-header bg-warning d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                Debtors (<?php echo $total_debtors; ?>)
            </h4>

            <div class="d-flex gap-2">

                <a
                    href="students.php"
                    class="btn btn-light btn-sm"
                >
                    All Students
                </a>

                <a
                    href="add_student.php"
                    class="btn btn-light btn-sm"
                >
                    Add Student
                </a>

            </div>

        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>

                        <tr>

                            <th>No.</th>

                            <th>Student Name</th>

                            <th>Student Type</th>

                            <th>Phone</th>

                            <th>Amount Charged</th>

                            <th>Amount Paid</th>

                            <th>Balance</th>

                            <th>Course</th>

                            <th width="100">Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php if ($total_debtors > 0): ?>

                            <?php $number = 1; ?>

                            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                                <tr>

                                    
                                    <td>
                                        <?php echo $number++; ?>
                                    </td>


                                    
                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            trim(
                                                $row['first_name'] . ' ' .
                                                $row['last_name'] . ' ' .
                                                $row['other_name']
                                            )
                                        );

                                        ?>

                                    </td>


                                    
                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $row['student_type'] ?? ''
                                        );

                                        ?>

                                    </td>


                                    
                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $row['phone']
                                        );

                                        ?>

                                    </td>


                                    
                                    <td>

                                        ₦<?php

                                        echo number_format(
                                            (float)$row['amount_charged'],
                                            2
                                        );

                                        ?>

                                    </td>


                                    
                                    <td>

                                        ₦<?php

                                        echo number_format(
                                            (float)$row['amount_paid'],
                                            2
                                        );

                                        ?>

                                    </td>


                                    
                                    <td class="text-danger fw-bold">

                                        ₦<?php

                                        echo number_format(
                                            (float)$row['balance'],
                                            2
                                        );

                                        ?>

                                    </td>


                                    
                                    <td>

                                        <?php

                                        echo htmlspecialchars(
                                            $row['course_type']
                                        );

                                        ?>

                                    </td>


                                    
                                    <td>

                                        <div class="d-flex flex-wrap gap-1">

                                            <a
                                                href="view_student.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-info btn-sm"
                                            >
                                                View
                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center py-4"
                                >
                                    No debtors found.
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>

</body>

</html>