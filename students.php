<?php
require 'config.php';
require 'auth.php';

$search = "";

$sql = "SELECT
            students.*,
            student_types.name AS student_type
        FROM students
        LEFT JOIN student_types
            ON students.student_type_id = student_types.id
        ORDER BY students.id ASC";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {

    $search = trim($_GET['search']);
    $search_safe = mysqli_real_escape_string($conn, $search);

    $sql = "SELECT
                students.*,
                student_types.name AS student_type
            FROM students
            LEFT JOIN student_types
                ON students.student_type_id = student_types.id
            WHERE
                students.first_name LIKE '%$search_safe%'
                OR students.last_name LIKE '%$search_safe%'
                OR students.other_name LIKE '%$search_safe%'
                OR students.phone LIKE '%$search_safe%'
                OR students.course_type LIKE '%$search_safe%'
            ORDER BY students.id DESC";
}

$result = mysqli_query($conn, $sql);
$total_students = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Students</title>

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

    <div class="card">

        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

            <h4 class="mb-0">
                Students (<?php echo $total_students; ?>)
            </h4>

            <a href="add_student.php" class="btn btn-light btn-sm">
                Add Student
            </a>

        </div>

        <div class="card-body">

            <form method="GET" class="mb-4">

                <div class="row">

                    <div class="col-md-10 mb-2">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by name, phone, or course..."
                            value="<?php echo htmlspecialchars($search); ?>">

                    </div>

                    <div class="col-md-2 mb-2">

                        <button class="btn btn-primary w-100">
                            Search
                        </button>

                    </div>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

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
                        <th width="220">Actions</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php if(mysqli_num_rows($result) > 0): ?>

                        <?php $number = 1; ?>

                        <?php while($row = mysqli_fetch_assoc($result)): ?>

                            <tr>

                                <td>
                                    <?php echo $number++; ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $row['first_name'] . ' ' .
                                        $row['last_name'] . ' ' .
                                        $row['other_name']
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['student_type']); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['phone']); ?>
                                </td>

                                <td>
                                    ₦<?php echo number_format($row['amount_charged'], 2); ?>
                                </td>

                                <td>
                                    ₦<?php echo number_format($row['amount_paid'], 2); ?>
                                </td>

                                <td>
                                    ₦<?php echo number_format($row['balance'], 2); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($row['course_type']); ?>
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-2">

                                    <a
                                        href="view_student.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-info btn-sm">

                                        View

                                    </a>

                                    <a
                                        href="edit_student.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-warning btn-sm">

                                        Edit

                                    </a>

                                    <a
                                        href="delete_student.php?id=<?php echo $row['id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete this student?')">

                                        Delete

                                    </a>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="9" class="text-center">
                                No students found.
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
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>