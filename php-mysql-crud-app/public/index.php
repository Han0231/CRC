<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';
require_once '../src/functions.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_table']) && !empty($_POST['table_name'])) {
        createTable($conn, $_POST['table_name']);
        header("Location: index.php");
        exit;
    }
    if (isset($_POST['delete_table']) && !empty($_POST['table'])) {
        deleteTable($conn, $_POST['table']);
        header("Location: index.php");
        exit;
    }
    if (isset($_POST['add_row']) && !empty($_POST['name']) && !empty($_POST['description']) && !empty($_POST['table'])) {
        addRow($conn, $_POST['table'], $_POST['name'], $_POST['description']);
        header("Location: index.php?table=" . urlencode($_POST['table']));
        exit;
    }
    if (isset($_POST['delete_row']) && !empty($_POST['id']) && !empty($_POST['table'])) {
        deleteRow($conn, $_POST['table'], $_POST['id']);
        header("Location: index.php?table=" . urlencode($_POST['table']));
        exit;
    }
}

// Get tables and selected table
$tables = getTables($conn);
$selectedTable = isset($_GET['table']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['table']) : (count($tables) ? $tables[0] : null);
$rows = $selectedTable ? getRows($conn, $selectedTable) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>MySQL Table Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h1>MySQL Table Manager</h1>
            <div class="user-info">
                <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>
                <br>
                (ID: <?= htmlspecialchars($_SESSION['user_id']) ?>,
                Role: <?= htmlspecialchars($_SESSION['user_role']) ?>)
                <form method="post" action="logout.php" style="margin-top:10px;">
                    <button type="submit">Logout</button>
                </form>
            </div>
            <h2>Tables</h2>
            <?php foreach ($tables as $table): ?>
                <div class="table-link<?= $table == $selectedTable ? ' active' : '' ?>">
                    <a href="?table=<?= urlencode($table) ?>"><?= htmlspecialchars($table) ?></a>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete table <?= htmlspecialchars($table) ?>?')">
                        <input type="hidden" name="table" value="<?= htmlspecialchars($table) ?>">
                        <button type="submit" name="delete_table" class="delete-btn" title="Delete Table">&times;</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <form method="post" class="create-table-form">
                <input type="text" name="table_name" placeholder="New table name" required>
                <button type="submit" name="create_table">Create Table</button>
            </form>
        </div>
        <div class="main-content">
            <?php if ($selectedTable): ?>
                <h2>Rows in <span class="selected-table"><?= htmlspecialchars($selectedTable) ?></span></h2>
                <table>
                    <tr>
                        <?php if (!empty($rows)): ?>
                            <?php foreach (array_keys($rows[0]) as $col): ?>
                                <th><?= htmlspecialchars($col) ?></th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($row as $val): ?>
                                <td><?= htmlspecialchars($val) ?></td>
                            <?php endforeach; ?>
                            <td>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="table" value="<?= htmlspecialchars($selectedTable) ?>">
                                    <button type="submit" name="delete_row" class="delete-btn" title="Delete Row">&times;</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <h3>Add Row</h3>
                <form method="post" class="add-row-form">
                    <input type="hidden" name="table" value="<?= htmlspecialchars($selectedTable) ?>">
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="text" name="description" placeholder="Description" required>
                    <button type="submit" name="add_row">Add</button>
                </form>
            <?php else: ?>
                <p>No tables found. Create one to get started!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>