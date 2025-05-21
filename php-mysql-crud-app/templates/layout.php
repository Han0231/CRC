<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <title>PHP MySQL CRUD Application</title>
</head>
<body>
    <header>
        <h1>PHP MySQL CRUD Application</h1>
        <nav>
            <ul>
                <li><a href="index.php?action=list">List Items</a></li>
                <li><a href="index.php?action=create">Create Item</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <?php include($view); ?>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> My CRUD Application</p>
    </footer>
</body>
</html>