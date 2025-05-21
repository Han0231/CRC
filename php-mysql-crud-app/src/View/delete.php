<?php
require_once '../../config/database.php';
require_once '../Controller/CrudController.php';

$crud = new CrudController();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $crud->deleteItem($id);
        header("Location: list.php");
        exit();
    }
    $item = $crud->readItem($id);
} else {
    header("Location: list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <title>Delete Item</title>
</head>
<body>
    <div class="container">
        <h2>Delete Item</h2>
        <?php if ($item): ?>
            <p>Are you sure you want to delete the item: <strong><?php echo htmlspecialchars($item['name']); ?></strong>?</p>
            <form method="POST">
                <button type="submit" class="btn btn-danger">Delete</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php else: ?>
            <p>Item not found.</p>
            <a href="list.php" class="btn btn-secondary">Back to List</a>
        <?php endif; ?>
    </div>
</body>
</html>