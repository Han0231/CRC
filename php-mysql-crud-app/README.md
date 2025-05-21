# PHP MySQL CRUD Application

This project is a simple PHP application that demonstrates how to perform CRUD (Create, Read, Update, Delete) operations using MySQL. It provides a user-friendly interface for managing items in a database.

## Project Structure

```
php-mysql-crud-app
├── config
│   └── database.php        # Database connection settings
├── public
│   ├── index.php          # Entry point of the application
│   └── css
│       └── style.css      # CSS styles for the application
├── src
│   ├── Controller
│   │   └── CrudController.php  # Handles CRUD operations
│   ├── Model
│   │   └── Item.php       # Data model for items
│   └── View
│       ├── create.php     # Form for creating a new item
│       ├── edit.php       # Form for editing an existing item
│       ├── list.php       # Displays a list of all items
│       └── delete.php     # Confirms deletion of an item
├── templates
│   └── layout.php         # Layout template for views
└── README.md              # Project documentation
```

## Setup Instructions

1. **Clone the Repository**: 
   Clone this repository to your local machine using:
   ```
   git clone <repository-url>
   ```

2. **Configure Database**:
   - Navigate to the `config/database.php` file.
   - Update the database connection settings (host, username, password, database name) to match your MySQL configuration.

3. **Create Database**:
   - Create a MySQL database that matches the name specified in `database.php`.
   - Create a table for items with appropriate fields (e.g., id, name, description).

4. **Run the Application**:
   - Start your local server (e.g., XAMPP, WAMP).
   - Open your web browser and navigate to `http://localhost/php-mysql-crud-app/public/index.php`.

## Usage

- **Create**: Use the "Create" form to add new items to the database.
- **Read**: View the list of items to see all entries in the database.
- **Update**: Edit existing items by selecting them from the list.
- **Delete**: Confirm deletion of items from the list.

## Contributing

Feel free to contribute to this project by submitting issues or pull requests. Your feedback and suggestions are welcome!

## License

This project is open-source and available under the MIT License.