# TaskMaster - Task Manager App

A modern, full-stack task management web app where users can securely sign up, log in, and manage their daily tasks. Built with PHP, MariaDB, and JavaScript.

## 📸 App Showcase

| Dashboard & Filtering | Login Page |
| :---: | :---: |
| <img src="Screenshots/dashboard.png" width="400" alt="Dashboard"> | <img src="Screenshots/login.png" width="400" alt="Login"> |

| Register Page | Edit Task |
| :---: | :---: |
| <img src="Screenshots/register.png" width="400" alt="Register"> | <img src="Screenshots/edit.png" width="400" alt="Edit Task"> |

## Features
- **Authentication:** Secure signup and login (passwords hashed with `password_hash`).
- **CRUD Operations:** Add, edit, delete, and mark tasks as complete.
- **Real-time Filtering:** Instantly search tasks and filter by priority (High, Medium, Low) using JavaScript.
- **Modern UI:** A clean, minimalist, responsive design using custom CSS and FontAwesome icons.
- **Data Isolation:** Each user has a private dashboard and can only see their own tasks.

## Tech Stack
- **Backend:** PHP 8
- **Database:** MariaDB (accessed securely using PDO)
- **Frontend:** HTML, CSS, JavaScript
- **Server:** XAMPP (local development)

## Security Practices
- Passwords hashed with `password_hash` and checked with `password_verify`.
- Prepared statements (PDO) to prevent SQL injection.
- Output escaped with `htmlspecialchars` to prevent XSS.
- Session ID regenerated after login to prevent session hijacking.
- Every edit, delete, and update is checked against the logged-in user's ID to prevent unauthorized access.

## How to Run Locally
1. Install XAMPP and start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Put this project folder inside `C:\xampp\htdocs\` (or create a junction link).
3. Open `http://localhost/phpmyadmin`, create a database named `task_manager`, and run this SQL:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    due_date DATE NULL,
    priority ENUM('High','Medium','Low') NOT NULL DEFAULT 'Medium',
    is_done TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);