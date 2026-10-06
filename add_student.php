<?php
require 'config.php';
require 'auth.php';

$student_types = mysqli_query($conn, "SELECT * FROM student_types ORDER BY name");
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $other_name = trim($_POST['other_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $registration_date = $_POST['registration_date'];
    $student_type_id = $_POST['student_type_id'];

    $amount_charged = (float) $_POST['amount_charged'];
    $amount_paid = (float) $_POST['amount_paid'];
    $balance = $amount_charged - $amount_paid;

    $course_type = trim($_POST['course_type']);
    $course_description = trim($_POST['course_description']);

    $guardian_names = $_POST['guardian_name'] ?? [];
    $guardian_emails = $_POST['guardian_email'] ?? [];
    $guardian_phones = $_POST['guardian_phone'] ?? [];

    $valid_guardians = [];

    for ($i = 0; $i < count($guardian_names); $i++) {

        $name = trim($guardian_names[$i]);
        $guardian_email = trim($guardian_emails[$i] ?? '');
        $guardian_phone = trim($guardian_phones[$i] ?? '');

        if ($name === '' && $guardian_email === '' && $guardian_phone === '') {
            continue;
        }

        if ($name === '' || $guardian_phone === '') {
            $message = "Every guardian must have a name and phone number.";
            break;
        }

        $valid_guardians[] = [
            'name' => $name,
            'email' => $guardian_email,
            'phone' => $guardian_phone
        ];
    }

    if (empty($message) && count($valid_guardians) === 0) {
        $message = "At least one guardian is required.";
    }

    if (empty($message)) {

        mysqli_begin_transaction($conn);

        try {

            $sql = "INSERT INTO students (
                        first_name,
                        last_name,
                        other_name,
                        email,
                        phone,
                        registration_date,
                        student_type_id,
                        amount_charged,
                        amount_paid,
                        balance,
                        course_type,
                        course_description
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            if (!$stmt) {
                throw new Exception(mysqli_error($conn));
            }

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssidddss",
                $first_name,
                $last_name,
                $other_name,
                $email,
                $phone,
                $registration_date,
                $student_type_id,
                $amount_charged,
                $amount_paid,
                $balance,
                $course_type,
                $course_description
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(mysqli_stmt_error($stmt));
            }

            $student_id = mysqli_insert_id($conn);

            $guardian_sql = "INSERT INTO guardians (
                                student_id,
                                guardian_name,
                                guardian_email,
                                guardian_phone
                            ) VALUES (?, ?, ?, ?)";

            $guardian_stmt = mysqli_prepare($conn, $guardian_sql);

            if (!$guardian_stmt) {
                throw new Exception(mysqli_error($conn));
            }

            foreach ($valid_guardians as $guardian) {

                mysqli_stmt_bind_param(
                    $guardian_stmt,
                    "isss",
                    $student_id,
                    $guardian['name'],
                    $guardian['email'],
                    $guardian['phone']
                );

                if (!mysqli_stmt_execute($guardian_stmt)) {
                    throw new Exception(mysqli_stmt_error($guardian_stmt));
                }
            }

            mysqli_commit($conn);
            $message = "Student and guardian information added successfully!";

        } catch (Exception $e) {

            mysqli_rollback($conn);
            $message = "Error adding student: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Add Student</title>

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
                    <a class="nav-link active" href="add_student.php">
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

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Register New Student</h4>
        </div>

        <div class="card-body">

            <?php if (!empty($message)): ?>

                <div class="alert <?php echo strpos($message, 'Error') === 0 ? 'alert-danger' : 'alert-success'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <!-- Student Information -->

                <h5 class="mb-3">Student Information</h5>

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


                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="mb-0">Guardian Information</h5>

                    <button
                        type="button"
                        class="btn btn-outline-primary btn-sm"
                        id="addGuardian">

                        + Add Another Guardian

                    </button>

                </div>

                <div id="guardianContainer">

                    <div class="guardian-card border rounded p-3 mb-3">

                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="mb-0">Guardian 1</h6>
                        </div>

                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Guardian Name</label>
                                <input
                                    type="text"
                                    name="guardian_name[]"
                                    class="form-control"
                                    required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Guardian Email</label>
                                <input
                                    type="email"
                                    name="guardian_email[]"
                                    class="form-control">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Guardian Phone</label>
                                <input
                                    type="text"
                                    name="guardian_phone[]"
                                    class="form-control"
                                    required>
                            </div>

                        </div>

                    </div>

                </div>

                <hr>

                <h5 class="mb-3">Academic Information</h5>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Registration Date
                        </label>

                        <input
                            type="date"
                            name="registration_date"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Student Type
                        </label>

                        <select
                            name="student_type_id"
                            class="form-select"
                            required>

                            <option value="">
                                Select Type
                            </option>

                            <?php while ($type = mysqli_fetch_assoc($student_types)): ?>

                                <option value="<?php echo $type['id']; ?>">
                                    <?php echo htmlspecialchars($type['name']); ?>
                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Amount Charged
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="amount_charged"
                            id="amount_charged"
                            class="form-control"
                            required>

                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Amount Paid
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="amount_paid"
                            id="amount_paid"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Balance
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="balance"
                            id="balance"
                            class="form-control"
                            readonly>

                    </div>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Course Type
                    </label>

                    <input
                        type="text"
                        name="course_type"
                        class="form-control">

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Course Description
                    </label>

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

<script>

let guardianCount = 1;

document.getElementById('addGuardian').addEventListener('click', function () {

    guardianCount++;

    const container = document.getElementById('guardianContainer');

    const card = document.createElement('div');

    card.className = 'guardian-card border rounded p-3 mb-3';

    card.innerHTML = `
        <div class="d-flex justify-content-between mb-3">

            <h6 class="mb-0">
                Guardian ${guardianCount}
            </h6>

            <button
                type="button"
                class="btn btn-outline-danger btn-sm removeGuardian">

                Remove

            </button>

        </div>

        <div class="row">

            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Guardian Name
                </label>

                <input
                    type="text"
                    name="guardian_name[]"
                    class="form-control"
                    required>

            </div>

            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Guardian Email
                </label>

                <input
                    type="email"
                    name="guardian_email[]"
                    class="form-control">

            </div>

            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Guardian Phone
                </label>

                <input
                    type="text"
                    name="guardian_phone[]"
                    class="form-control"
                    required>

            </div>

        </div>
    `;

    container.appendChild(card);

    card.querySelector('.removeGuardian').addEventListener('click', function () {
        card.remove();
    });

});


function calculateBalance() {

    const charged =
        parseFloat(document.getElementById('amount_charged').value) || 0;

    const paid =
        parseFloat(document.getElementById('amount_paid').value) || 0;

    document.getElementById('balance').value =
        (charged - paid).toFixed(2);
}

document
    .getElementById('amount_charged')
    .addEventListener('input', calculateBalance);

document
    .getElementById('amount_paid')
    .addEventListener('input', calculateBalance);

</script>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>