<?php
require 'config.php';
require 'auth.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: students.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        students.*,
        student_types.name AS student_type_id
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Student Details</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="students.php">Students</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="add_student.php">Add Student</a>
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

<?php if(isset($_GET['updated'])): ?>

    <div class="alert alert-success alert-dismissible fade show">

        Student updated successfully.

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php endif; ?>

<div class="container page-container">

    <div class="card">

        <div class="card-header bg-primary text-white d-flex justify-content-between">

            <h4 class="mb-0">
                Student Details
            </h4>

            <a href="students.php" class="btn btn-light btn-sm">
                Back
            </a>

        </div>

        <div class="card-body">

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
                    <?php echo htmlspecialchars($student['email']); ?>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="details-label">Phone:</span><br>
                    <?php echo htmlspecialchars($student['phone']); ?>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="details-label">Registration Date:</span><br>
                    <?php echo htmlspecialchars($student['registration_date']); ?>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="details-label">Student Type:</span><br>
                    <?php echo htmlspecialchars($student['student_type_id']); ?>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="details-label">Amount Paid:</span><br>
                    ₦<?php echo number_format($student['amount_paid'], 2); ?>
                </div>

                <div class="col-md-6 mb-3">
                    <span class="details-label">Course Type:</span><br>
                    <?php echo htmlspecialchars($student['course_type']); ?>
                </div>

            </div>

            <hr>

            <h5>Guardian Information</h5>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <span class="details-label">Guardian Name:</span><br>
                    <?php echo htmlspecialchars($student['guardian_name']); ?>
                </div>

                <div class="col-md-4 mb-3">
                    <span class="details-label">Guardian Email:</span><br>
                    <?php echo htmlspecialchars($student['guardian_email']); ?>
                </div>

                <div class="col-md-4 mb-3">
                    <span class="details-label">Guardian Phone:</span><br>
                    <?php echo htmlspecialchars($student['guardian_phone']); ?>
                </div>

            </div>

            <hr>

            <h5>Course Description</h5>

            <p>
                <?php
                echo nl2br(
                    htmlspecialchars($student['course_description'])
                );
                ?>
            </p>

            <hr>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>