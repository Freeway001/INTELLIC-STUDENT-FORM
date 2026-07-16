<?php
require 'config.php';
require 'auth.php';

$student_types = mysqli_query(
    $conn,
    "SELECT * FROM student_types ORDER BY name"
);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $other_name = trim($_POST['other_name']);

    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    $guardian_name = trim($_POST['guardian_name']);
    $guardian_email = trim($_POST['guardian_email']);
    $guardian_phone = trim($_POST['guardian_phone']);

    $registration_date = $_POST['registration_date'];
    $student_type_id = $_POST['student_type_id'];

    $amount_paid = $_POST['amount_paid'];

    $course_type = trim($_POST['course_type']);
    $course_description = trim($_POST['course_description']);

    $sql = "INSERT INTO students (
                first_name,
                last_name,
                other_name,
                email,
                phone,
                guardian_name,
                guardian_email,
                guardian_phone,
                registration_date,
                student_type_id,
                amount_paid,
                course_type,
                course_description
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssssidss",
        $first_name,
        $last_name,
        $other_name,
        $email,
        $phone,
        $guardian_name,
        $guardian_email,
        $guardian_phone,
        $registration_date,
        $student_type_id,
        $amount_paid,
        $course_type,
        $course_description
    );

    if (mysqli_stmt_execute($stmt)) {
        $message = "Student added successfully!";
    } else {
        $message = "Error adding student.";
    }
   
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Student</title>

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

<div class="container page-container">

    <div class="card">

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Register New Student</h4>
        </div>

        <div class="card-body">

            <?php if (!empty($message)) : ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">First Name</label>
                        <input
                            type="text"
                            name="first_name"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Last Name</label>
                        <input
                            type="text"
                            name="last_name"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Other Name</label>
                        <input
                            type="text"
                            name="other_name"
                            class="form-control">
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input
                            type="text"
                            name="phone"
                            class="form-control">
                    </div>

                </div>

                <hr>

                <h5>Guardian Information</h5>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Guardian Name</label>
                        <input
                            type="text"
                            name="guardian_name"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Guardian Email</label>
                        <input
                            type="email"
                            name="guardian_email"
                            class="form-control">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Guardian Phone</label>
                        <input
                            type="text"
                            name="guardian_phone"
                            class="form-control"
                            required>
                    </div>

                </div>

                <hr>

                <h5>Academic Information</h5>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Registration Date</label>
                        <input
                            type="date"
                            name="registration_date"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Student Type</label>

                       <select name="student_type_id" class="form-select" required>

                            <option value="">Select Type</option>

                            <?php while($type = mysqli_fetch_assoc($student_types)): ?>

                                <option value="<?php echo $type['id']; ?>">
                                    <?php echo htmlspecialchars($type['name']); ?>
                                </option>

                            <?php endwhile; ?>

                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Amount Paid</label>

                        <input
                            type="number"
                            step="0.01"
                            name="amount_paid"
                            class="form-control"
                            required>
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label">Course Type</label>

                    <input
                        type="text"
                        name="course_type"
                        class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Course Description</label>

                    <textarea
                        name="course_description"
                        rows="4"
                        class="form-control"></textarea>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary">

                    Save Student

                </button>

                <a
                    href="students.php"
                    class="btn btn-secondary">

                    View Students

                </a>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>