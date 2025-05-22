<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';
require_once '../src/functions.php';

// Helper: Get columns for a table
function getTableColumns($conn, $table) {
    $stmt = $conn->prepare("DESCRIBE `$table`");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DETAILED CREATE TABLE HANDLER
    if (isset($_POST['create_table']) && !empty($_POST['table_name']) && !empty($_POST['fields'])) {
        $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name']);
        $fields = $_POST['fields'];
        // Ensure a primary key is selected
        if (!isset($_POST['primary']) || $_POST['primary'] === '') {
            die('Error: You must select a primary key field.');
        }
        $pkIdx = intval($_POST['primary']);
        $sqlFields = [];
        foreach ($fields as $i => $field) {
            $fname = preg_replace('/[^a-zA-Z0-9_]/', '', $field['name']);
            $ftype = preg_replace('/[^a-zA-Z0-9\(\)]/', '', $field['type']);
            $nullable = isset($field['null']) ? 'NULL' : 'NOT NULL';
            $constraint = '';
            if (!empty($field['constraint'])) {
                $constraint = strtoupper($field['constraint']);
                // Handle special cases
                if ($constraint === 'DEFAULT') {
                    $constraint = "DEFAULT ''";
                } elseif ($constraint === 'CHECK') {
                    $constraint = ""; // You can prompt for a condition if you want
                } elseif ($constraint === 'FOREIGN KEY') {
                    $constraint = ""; // Needs reference table/column
                } elseif ($constraint === 'CREATE INDEX') {
                    $constraint = ""; // Indexes are usually created separately
                } elseif ($constraint === 'PRIMARY KEY') {
                    // Handled by radio button, skip here
                    $constraint = "";
                }
            }
            $pk = ($i == $pkIdx) ? 'PRIMARY KEY' : '';
            $sqlFields[] = "`$fname` $ftype $nullable $constraint $pk";
        }
        $sql = "CREATE TABLE `$tableName` (" . implode(',', $sqlFields) . ")";
        $conn->exec($sql);
        header("Location: index.php");
        exit;
    }
    if (isset($_POST['delete_table']) && !empty($_POST['table'])) {
        deleteTable($conn, $_POST['table']);
        header("Location: index.php");
        exit;
    }
    if (isset($_POST['add_row']) && !empty($_POST['table'])) {
        $table = $_POST['table'];
        $columns = getTableColumns($conn, $table);
        $fields = [];
        $placeholders = [];
        $values = [];
        foreach ($columns as $col) {
            $colName = $col['Field'];
            if ($col['Extra'] === 'auto_increment') continue; // skip auto-increment PK
            if (isset($_POST[$colName])) {
                // Hash password if the column is named 'password'
                if (strtolower($colName) === 'password') {
                    $fields[] = "`$colName`";
                    $placeholders[] = "?";
                    $values[] = password_hash($_POST[$colName], PASSWORD_DEFAULT);
                } else {
                    $fields[] = "`$colName`";
                    $placeholders[] = "?";
                    $values[] = $_POST[$colName];
                }
            }
        }
        if ($fields) {
            $sql = "INSERT INTO `$table` (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
            $stmt = $conn->prepare($sql);
            $stmt->execute($values);
        }
        header("Location: index.php?table=" . urlencode($table));
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
$columns = $selectedTable ? getTableColumns($conn, $selectedTable) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>MySQL Table Manager</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Modal styles */
        .create-table-btn {
            background: #eebbc3;
            color: #232946;
            border: none;
            border-radius: 50%;
            width: 38px;
            height: 38px;
            font-size: 1.6em;
            font-weight: bold;
            cursor: pointer;
            margin-top: 18px;
            transition: background 0.2s;
        }
        .create-table-btn:hover {
            background: #fffffe;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(35,41,70,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 32px 24px;
            border-radius: 10px;
            width: 510px;
            max-width: 95vw;
            position: relative;
        }
        .close {
            position: absolute;
            right: 18px;
            top: 12px;
            font-size: 2em;
            color: #232946;
            cursor: pointer;
        }
        #fieldsContainer {
            margin-bottom: 12px;
        }
        .field-row {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
            align-items: center;
        }
        .field-row input[type="text"], .field-row select {
            padding: 4px 6px;
            border-radius: 3px;
            border: 1px solid #b8c1ec;
        }
        .field-row label {
            font-size: 0.98em;
        }
        .field-row .remove-field {
            color: #ff595e;
            background: none;
            border: none;
            font-size: 1.2em;
            cursor: pointer;
        }
        .pk-radio {
            margin-right: 4px;
            accent-color: #eebbc3;
        }
    </style>
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
            <!-- + Button for Create Table -->
            <button class="create-table-btn" title="Create Table" onclick="openCreateTableModal()">+</button>
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
                    <?php foreach ($columns as $col): ?>
                        <?php if ($col['Extra'] === 'auto_increment') continue; ?>
                        <input
                            type="text"
                            name="<?= htmlspecialchars($col['Field']) ?>"
                            placeholder="<?= htmlspecialchars($col['Field']) ?>"
                            <?= $col['Null'] === 'NO' && $col['Default'] === null ? 'required' : '' ?>
                        >
                    <?php endforeach; ?>
                    <button type="submit" name="add_row">Add</button>
                </form>
            <?php else: ?>
                <p>No tables found. Create one to get started!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for Create Table -->
    <div id="createTableModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeCreateTableModal()">&times;</span>
        <h2>Create Table</h2>
        <form id="createTableForm" method="post">
          <input type="hidden" name="create_table" value="1">
          <label>Table Name:</label>
          <input type="text" name="table_name" required style="margin-bottom:10px;">
          <div id="fieldsContainer"></div>
          <button type="button" onclick="addFieldRow()">Add Field</button>
          <button type="submit" style="margin-left:10px;">Create Table</button>
        </form>
      </div>
    </div>
    <script>
    function openCreateTableModal() {
        document.getElementById('createTableModal').style.display = 'flex';
        if (document.getElementsByClassName('field-row').length === 0) {
            addFieldRow();
        }
    }
    function closeCreateTableModal() {
        document.getElementById('createTableModal').style.display = 'none';
        document.getElementById('fieldsContainer').innerHTML = '';
    }
    function addFieldRow() {
        const container = document.getElementById('fieldsContainer');
        const idx = container.children.length;
        const row = document.createElement('div');
        row.className = 'field-row';
        row.innerHTML = `
            <input type="radio" class="pk-radio" name="primary" value="${idx}" title="Primary Key" ${idx === 0 ? 'checked' : ''}>
            <input type="text" name="fields[${idx}][name]" placeholder="Field Name" required>
            <select name="fields[${idx}][type]" required>
                <option value="INT">INT</option>
                <option value="VARCHAR(255)">VARCHAR(255)</option>
                <option value="TEXT">TEXT</option>
                <option value="DATE">DATE</option>
                <option value="DATETIME">DATETIME</option>
                <option value="FLOAT">FLOAT</option>
                <option value="DOUBLE">DOUBLE</option>
                <option value="BOOLEAN">BOOLEAN</option>
            </select>
            <select name="fields[${idx}][constraint]">
                <option value="">No Constraint</option>
                <option value="NOT NULL">NOT NULL</option>
                <option value="UNIQUE">UNIQUE</option>
                <option value="FOREIGN KEY">FOREIGN KEY</option>
                <option value="CHECK">CHECK</option>
                <option value="DEFAULT">DEFAULT</option>
                <option value="CREATE INDEX">CREATE INDEX</option>
            </select>
            <button type="button" class="remove-field" onclick="this.parentElement.remove()">×</button>
        `;
        container.appendChild(row);
    }

    // Prevent form submission if no PK is selected
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('createTableForm').onsubmit = function(e) {
            const primarySelected = document.querySelector('input[name="primary"]:checked');
            if (!primarySelected) {
                alert('Please select a Primary Key field.');
                e.preventDefault();
                return false;
            }
        };
    });
    </script>
</body>
</html>