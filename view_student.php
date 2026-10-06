<?php
require 'config.php';
require 'auth.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: students.php");
    exit;
}

$id = (int) $_GET['id'];

/* Get student information */
$stmt = mysqli_prepare(
    $conn,
    "SELECT students.*, student_types.name AS student_type
     FROM students
     LEFT JOIN student_types
        ON students.student_type_id = student_types.id
     WHERE students.id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: students.php");
    exit;
}

$student = mysqli_fetch_assoc($result);

/* Get all guardians */
$guardian_stmt = mysqli_prepare(
    $conn,
    "SELECT id, guardian_name, guardian_email, guardian_phone
     FROM guardians
     WHERE student_id = ?
     ORDER BY id ASC"
);

mysqli_stmt_bind_param($guardian_stmt, "i", $id);
mysqli_stmt_execute($guardian_stmt);

$guardians_result = mysqli_stmt_get_result($guardian_stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Student Details</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

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

<?php if (isset($_GET['updated'])): ?>

<div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show">
        Student updated successfully.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>
    </div>
</div>

<?php endif; ?>

<div class="container page-container">

    <div class="card">

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                Student Details
            </h4>

            <a href="students.php" class="btn btn-light btn-sm">
                Back
            </a>

        </div>

        <div class="card-body">

            <!-- Student Information -->

            <h3 class="mb-4">
                <?php
                echo htmlspecialchars(
                    $student['first_name'] . ' ' .
                    $student['last_name'] . ' ' .
                    $student['other_name']
                );
                ?>
            </h3>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <span class="details-label">Email:</span><br>

                    <?php
                    echo !empty($student['email'])
                        ? htmlspecialchars($student['email'])
                        : 'Not provided';
                    ?>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="details-label">Phone:</span><br>

                    <?php
                    echo !empty($student['phone'])
                        ? htmlspecialchars($student['phone'])
                        : 'Not provided';
                    ?>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="details-label">Registration Date:</span><br>

                    <?php
                    echo htmlspecialchars($student['registration_date']);
                    ?>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="details-label">Student Type:</span><br>

                    <?php
                    echo !empty($student['student_type'])
                        ? htmlspecialchars($student['student_type'])
                        : 'Not specified';
                    ?>
                </div>

            </div>

            <hr>

            <!-- Payment Information -->

            <h5 class="mb-3">Payment Information</h5>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <span class="details-label">Amount Charged:</span><br>

                    ₦<?php
                    echo number_format(
                        $student['amount_charged'],
                        2
                    );
                    ?>
                </div>

                <div class="col-md-4 mb-3">
                    <span class="details-label">Amount Paid:</span><br>

                    ₦<?php
                    echo number_format(
                        $student['amount_paid'],
                        2
                    );
                    ?>
                </div>

                <div class="col-md-4 mb-3">
                    <span class="details-label">Balance:</span><br>

                    ₦<?php
                    echo number_format(
                        $student['balance'],
                        2
                    );
                    ?>
                </div>

            </div>

            <hr>

            <!-- Course Information -->

            <h5>Course Information</h5>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <span class="details-label">
                        Course Type:
                    </span><br>

                    <?php
                    echo !empty($student['course_type'])
                        ? htmlspecialchars($student['course_type'])
                        : 'Not provided';
                    ?>

                </div>

                <div class="col-md-12 mb-3">

                    <span class="details-label">
                        Course Description:
                    </span><br>

                    <?php
                    echo !empty($student['course_description'])
                        ? nl2br(
                            htmlspecialchars(
                                $student['course_description']
                            )
                        )
                        : 'No course description provided.';
                    ?>

                </div>

            </div>

            <hr>

            <!-- Guardian Information -->

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="mb-0">
                    Guardian Information
                </h5>

                <span class="badge bg-primary">

                    <?php
                    echo mysqli_num_rows($guardians_result);
                    ?>

                    Guardian(s)

                </span>

            </div>

            <?php if (mysqli_num_rows($guardians_result) > 0): ?>

                <div class="row">

                    <?php
                    $guardian_number = 1;

                    while ($guardian = mysqli_fetch_assoc($guardians_result)):
                    ?>

                        <div class="col-md-6 mb-3">

                            <div class="card h-100 border">

                                <div class="card-header bg-light">

                                    <strong>
                                        Guardian
                                        <?php echo $guardian_number++; ?>
                                    </strong>

                                </div>

                                <div class="card-body">

                                    <div class="mb-3">

                                        <span class="details-label">
                                            Name:
                                        </span><br>

                                        <?php
                                        echo htmlspecialchars(
                                            $guardian['guardian_name']
                                        );
                                        ?>

                                    </div>

                                    <div class="mb-3">

                                        <span class="details-label">
                                            Email:
                                        </span><br>

                                        <?php
                                        echo !empty(
                                            $guardian['guardian_email']
                                        )
                                            ? htmlspecialchars(
                                                $guardian['guardian_email']
                                            )
                                            : 'Not provided';
                                        ?>

                                    </div>

                                    <div>

                                        <span class="details-label">
                                            Phone:
                                        </span><br>

                                        <?php
                                        echo htmlspecialchars(
                                            $guardian['guardian_phone']
                                        );
                                        ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>

            <?php else: ?>

                <div class="alert alert-warning">
                    No guardian information has been added
                    for this student.
                </div>

            <?php endif; ?>

            <hr>

            <!-- Action Buttons -->

            <a
                href="edit_student.php?id=<?php echo $student['id']; ?>"
                class="btn btn-warning">

                Edit Student

            </a>

            <a
                href="delete_student.php?id=<?php echo $student['id']; ?>"
                class="btn btn-danger"
                onclick="return confirm('Delete this student?')">

                Delete Student

            </a>

        </div>

    </div>

</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>