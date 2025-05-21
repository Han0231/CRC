<?php
require_once '../../config/database.php';
require_once '../Controller/CrudController.php';

$crud = new CrudController();
$item = null;

if (isset($_GET['id'])) {
    $item = $crud->readItem($_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $crud->updateItem($_POST['id'], $_POST['name'], $_POST['description']);
    header("Location: list.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/css/style.css">
    <title>Edit Item</title>
</head>
<body>
    <div class="container">
        <h2>Edit Item</h2>
        <form action="edit.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $item['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo $item['description']; ?></textarea>
            </div>
            <button type="submit">Update Item</button>
        </form>
        <a href="list.php">Back to List</a>
    </div>
</body>
</html>