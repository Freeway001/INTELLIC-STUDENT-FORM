<?php
$conn = new mysqli("localhost", "root", "password123", "intellicstudentdb");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if (isset($_POST['add_expense'])) {
    $category_id = intval($_POST['expense_category_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $cost = floatval($_POST['cost']);

    if ($category_id > 0 && $name !== '' && $cost >= 0) {
        $stmt = $conn->prepare(
            "INSERT INTO expenses
            (expense_category_id, name, description, cost)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "issd",
            $category_id,
            $name,
            $description,
            $cost
        );

        $stmt->execute();
        $stmt->close();

        header("Location: expenses.php?success=added");
        exit;
    }
}

if (isset($_POST['update_expense'])) {
    $id = intval($_POST['id']);
    $category_id = intval($_POST['expense_category_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $cost = floatval($_POST['cost']);

    if ($category_id > 0 && $name !== '' && $cost >= 0) {
        $stmt = $conn->prepare(
            "UPDATE expenses
             SET expense_category_id = ?,
                 name = ?,
                 description = ?,
                 cost = ?
             WHERE id = ?"
        );

        $stmt->bind_param(
            "issdi",
            $category_id,
            $name,
            $description,
            $cost,
            $id
        );

        $stmt->execute();
        $stmt->close();

        header("Location: expenses.php?success=updated");
        exit;
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare(
        "DELETE FROM expenses WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: expenses.php?success=deleted");
    exit;
}

$editExpense = null;

if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);

    $stmt = $conn->prepare(
        "SELECT * FROM expenses WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $editExpense = $result->fetch_assoc();

    $stmt->close();
}

$categories = $conn->query(
    "SELECT * FROM expense_categories ORDER BY category ASC, name ASC"
);

$expenses = $conn->query(
    "SELECT
        expenses.*,
        expense_categories.name AS category_name,
        expense_categories.category AS category_type
     FROM expenses
     INNER JOIN expense_categories
        ON expenses.expense_category_id = expense_categories.id
     ORDER BY expenses.id DESC"
);

$totalResult = $conn->query(
    "SELECT COALESCE(SUM(cost), 0) AS total FROM expenses"
);

$totalExpense = $totalResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Expenses - Intellic Academy</title>

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

        .summary-card {
            background: linear-gradient(135deg, #003366, #0056b3);
            color: white;
        }

        .summary-card h3 {
            font-weight: 700;
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
            <h2 class="page-title">Expenses</h2>
        </div>

        <div>
            <a
                href="expense_categories.php"
                class="btn btn-outline-primary">
                Expense Categories
            </a>

            <a
                href="students.php"
                class="btn btn-outline-secondary">
                Back
            </a>
        </div>

    </div>

    <?php if (isset($_GET['success'])): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <?php
            if ($_GET['success'] === 'added') {
                echo "Expense added successfully.";
            } elseif ($_GET['success'] === 'updated') {
                echo "Expense updated successfully.";
            } elseif ($_GET['success'] === 'deleted') {
                echo "Expense deleted successfully.";
            }
            ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    <?php endif; ?>


    
    <div class="row mb-4">

        <div class="col-md-4">

            <div class="card summary-card p-4">

                <small>Total Expenses</small>

                <h3 class="mb-0">
                    ₦<?= number_format($totalExpense, 2) ?>
                </h3>

            </div>

        </div>

    </div>


    <div class="card p-4 mb-4">

        <h5 class="mb-3">

            <?= $editExpense
                ? 'Edit Expense'
                : 'Add Expense'
            ?>

        </h5>

        <form method="POST">

            <?php if ($editExpense): ?>

                <input
                    type="hidden"
                    name="id"
                    value="<?= $editExpense['id'] ?>"
                >

            <?php endif; ?>


            <div class="row g-3">

                <div class="col-md-6">

                    <label class="form-label">
                        Expense Category
                    </label>

                    <select
                        name="expense_category_id"
                        class="form-select"
                        required>

                        <option value="">
                            Select expense category
                        </option>

                        <?php while ($category = $categories->fetch_assoc()): ?>

                            <option
                                value="<?= $category['id'] ?>"
                                <?= (
                                    isset($editExpense['expense_category_id']) &&
                                    $editExpense['expense_category_id'] == $category['id']
                                ) ? 'selected' : ''
                                ?>
                            >

                                <?= htmlspecialchars($category['category']) ?>
                                -
                                <?= htmlspecialchars($category['name']) ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="e.g. Monthly Internet"
                        value="<?= htmlspecialchars($editExpense['name'] ?? '') ?>"
                        required
                    >

                </div>

                <div class="col-md-8">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        class="form-control"
                        rows="3"
                        placeholder="Enter expense description"><?= htmlspecialchars($editExpense['description'] ?? '') ?></textarea>

                </div>


                <div class="col-md-4">

                    <label class="form-label">
                        Cost
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            ₦
                        </span>

                        <input
                            type="number"
                            name="cost"
                            class="form-control"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                            value="<?= htmlspecialchars($editExpense['cost'] ?? '') ?>"
                            required
                        >

                    </div>

                </div>

            </div>


            <div class="mt-3">

                <?php if ($editExpense): ?>

                    <button
                        type="submit"
                        name="update_expense"
                        class="btn btn-primary">
                        Update Expense
                    </button>

                    <a
                        href="expenses.php"
                        class="btn btn-secondary">
                        Cancel
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        name="add_expense"
                        class="btn btn-primary">
                        Add Expense
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <div class="card p-4">

        <h5 class="mb-3">
            Expense History
        </h5>

        <div class="table-responsive">

            <table class="table table-bordered align-middle mb-0">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Expense Category</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Cost</th>
                        <th>Date</th>
                        <th width="170">Action</th>
                    </tr>

                </thead>

                <tbody>

                <?php if ($expenses->num_rows > 0): ?>

                    <?php $number = 1; ?>

                    <?php while ($expense = $expenses->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?= $number++ ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($expense['category_name']) ?>
                                </strong>

                                <br>

                                <small class="text-muted">
                                    <?= htmlspecialchars($expense['category_type']) ?>
                                </small>
                            </td>

                            <td>
                                <?= htmlspecialchars($expense['name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($expense['description'] ?: '-') ?>
                            </td>

                            <td>
                                <strong>
                                    ₦<?= number_format($expense['cost'], 2) ?>
                                </strong>
                            </td>

                            <td>
                                <?= date(
                                    'd M Y',
                                    strtotime($expense['created_at'])
                                ) ?>
                            </td>

                            <td>

                                <a
                                    href="expenses.php?edit=<?= $expense['id'] ?>"
                                    class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <a
                                    href="expenses.php?delete=<?= $expense['id'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this expense?');">
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="7"
                            class="text-center text-muted py-4">

                            No expenses found.

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