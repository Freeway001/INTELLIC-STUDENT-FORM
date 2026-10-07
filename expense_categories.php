<?php
session_start();

$conn = new mysqli("localhost", "root", "password123", "intellicstudentdb");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);

    if ($name !== '' && $category !== '') {
        $stmt = $conn->prepare(
            "INSERT INTO expense_categories (name, category) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $name, $category);
        $stmt->execute();
        $stmt->close();

        header("Location: expense_categories.php?success=added");
        exit;
    }
}

if (isset($_POST['update_category'])) {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);

    if ($name !== '' && $category !== '') {
        $stmt = $conn->prepare(
            "UPDATE expense_categories 
             SET name = ?, category = ? 
             WHERE id = ?"
        );
        $stmt->bind_param("ssi", $name, $category, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: expense_categories.php?success=updated");
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare(
        "DELETE FROM expense_categories WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: expense_categories.php?success=deleted");
    exit;
}

$editCategory = null;

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);

    $stmt = $conn->prepare(
        "SELECT * FROM expense_categories WHERE id = ?"
    );
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $editCategory = $result->fetch_assoc();

    $stmt->close();
}


$result = $conn->query(
    "SELECT * FROM expense_categories ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Expense Categories - Intellic Academy</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f5f7fb;
        }

        .page-title {
            color: #003366;
            font-weight: 700;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
        }

        .btn-primary {
            background: #003366;
            border-color: #003366;
        }

        .btn-primary:hover {
            background: #0056b3;
            border-color: #0056b3;
        }

        .table th {
            background: #003366;
            color: white;
        }
    </style>
</head>

<body>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="page-title">Expense Categories</h2>
            <p class="text-muted mb-0">
                Manage categories used for academy expenses.
            </p>
        </div>
        <div>
        <a href="expenses.php" class="btn btn-outline-primary">
            Expenses
        </a>

        <a href="students.php" class="btn btn-outline-primary">
            Back
        </a>
        </div>

    </div>

    <?php if (isset($_GET['success'])): ?>

        <div class="alert alert-success alert-dismissible fade show">
            <?php
            if ($_GET['success'] === 'added') {
                echo "Expense category added successfully.";
            } elseif ($_GET['success'] === 'updated') {
                echo "Expense category updated successfully.";
            } elseif ($_GET['success'] === 'deleted') {
                echo "Expense category deleted successfully.";
            }
            ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>

    <?php endif; ?>


    
    <div class="card p-4 mb-4">

        <h5 class="mb-3">
            <?= $editCategory ? 'Edit Expense Category' : 'Add Expense Category' ?>
        </h5>

        <form method="POST">

            <?php if ($editCategory): ?>
                <input
                    type="hidden"
                    name="id"
                    value="<?= $editCategory['id'] ?>"
                >
            <?php endif; ?>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Name</label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="e.g. MTN Fiber"
                        value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category</label>

                    <input
                        type="text"
                        name="category"
                        class="form-control"
                        placeholder="e.g. Internet"
                        value="<?= htmlspecialchars($editCategory['category'] ?? '') ?>"
                        required
                    >
                </div>

            </div>

            <div class="mt-3">

                <?php if ($editCategory): ?>

                    <button
                        type="submit"
                        name="update_category"
                        class="btn btn-primary">
                        Update Category
                    </button>

                    <a
                        href="expense_categories.php"
                        class="btn btn-secondary">
                        Cancel
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="add_category"
                        class="btn btn-primary">
                        Add Category
                    </button>

                <?php endif; ?>

            </div>

        </form>
    </div>


    
    <div class="card p-4">

        <h5 class="mb-3">All Expense Categories</h5>

        <div class="table-responsive">

            <table class="table table-bordered align-middle mb-0">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($result->num_rows > 0): ?>

                    <?php $number = 1; ?>

                    <?php while ($row = $result->fetch_assoc()): ?>

                        <tr>

                            <td><?= $number++ ?></td>

                            <td>
                                <?= htmlspecialchars($row['name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['category']) ?>
                            </td>

                            <td>

                                <a
                                    href="expense_categories.php?edit=<?= $row['id'] ?>"
                                    class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <a
                                    href="expense_categories.php?delete=<?= $row['id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this category?');">
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No expense categories found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>