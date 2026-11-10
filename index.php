<?php
session_start();
require 'db.php';

// Only logged-in users can see this page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';

// Add a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';

    if ($title === '') {
        $error = 'Please enter a task title.';
    } elseif (!in_array($priority, ['High', 'Medium', 'Low'], true)) {
        $error = 'Invalid priority.';
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO tasks (user_id, title, due_date, priority) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$user_id, $title, $due_date !== '' ? $due_date : null, $priority]);
        header('Location: index.php');
        exit;
    }
}

// Get only THIS user's tasks
$stmt = $pdo->prepare(
    'SELECT id, title, due_date, priority, is_done
     FROM tasks WHERE user_id = ? ORDER BY is_done, due_date'
);
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Task Manager</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Your Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fa-solid fa-list-check"></i> TaskMaster
        </div>
        <div class="nav-user">
            <span><i class="fa-solid fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Log out</a>
        </div>
    </nav>

    <div class="container">
        
        <h1>Dashboard</h1>
        
        <!-- Add Task Form Card -->
        <div class="add-task-container">
            <h2><i class="fa-solid fa-plus-circle"></i> Add a new task</h2>
            <?php if ($error): ?>
                <p style="color:red; font-size: 0.9rem;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="POST" class="add-task-form">
                <input type="text" name="title" placeholder="What needs to be done?" maxlength="100" required>
                <input type="date" name="due_date">
                <select name="priority">
                    <option>High</option>
                    <option selected>Medium</option>
                    <option>Low</option>
                </select>
                <button type="submit" class="btn-primary">Add Task</button>
            </form>
        </div>

        <h2>Your Tasks</h2>
        <?php if (count($tasks) === 0): ?>
            <p>No tasks yet. Add your first one above.</p>
        <?php else: ?>
            <!-- Modern Grid Layout -->
            <div class="task-grid">
                <?php foreach ($tasks as $task): ?>
                    <div class="task-card <?= $task['is_done'] ? 'completed' : '' ?>">
                        
                        <div class="task-header">
                            <h3><?= htmlspecialchars($task['title']) ?></h3>
                            <span class="badge priority-<?= strtolower($task['priority']) ?>">
                                <?= htmlspecialchars($task['priority']) ?>
                            </span>
                        </div>

                        <div class="task-body">
                            <p><i class="fa-regular fa-calendar"></i> Due: <?= htmlspecialchars($task['due_date'] ?? 'No date') ?></p>
                            <p>Status: <strong><?= $task['is_done'] ? 'Completed' : 'Pending' ?></strong></p>
                        </div>

                        <div class="task-actions">
                            <!-- Edit Button -->
                            <a href="edit.php?id=<?= (int)$task['id'] ?>" class="btn-icon btn-edit" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <!-- Mark Done / Undo Form -->
                            <form method="POST" action="toggle.php" style="display:inline;">
                                <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
                                <button type="submit" class="btn-icon btn-toggle" title="<?= $task['is_done'] ? 'Undo' : 'Mark Done' ?>">
                                    <i class="fa-solid <?= $task['is_done'] ? 'fa-rotate-left' : 'fa-check' ?>"></i>
                                </button>
                            </form>

                            <!-- Delete Form -->
                            <form method="POST" action="delete.php" style="display:inline;" onsubmit="return confirm('Delete this task?');">
                                <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
                                <button type="submit" class="btn-icon btn-delete" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>