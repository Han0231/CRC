<?php
// filepath: /php-mysql-crud-app/php-mysql-crud-app/src/View/create.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/style.css">
    <title>Create Item</title>
</head>
<body>
    <div class="container">
        <h2>Create New Item</h2>
        <form action="/public/index.php?action=create" method="POST">
            <div class="form-group">
                <label for="name">Item Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <button type="submit">Create Item</button>
        </form>
        <a href="/public/index.php?action=list">Back to List</a>
    </div>
</body>
</html>