<?php
session_start();
require 'db.php';

// Only logged-in users can use this page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';

// Save the changes when the form is sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = (int)($_POST['task_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
    $priority = $_POST['priority'] ?? 'Medium';

    if ($title === '') {
        $error = 'Please enter a task title.';
    } elseif (!in_array($priority, ['High', 'Medium', 'Low'], true)) {
        $error = 'Invalid priority.';
    } else {
        // Update the task, but ONLY if it belongs to this user
        $stmt = $pdo->prepare(
            'UPDATE tasks SET title = ?, due_date = ?, priority = ? WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$title, $due_date !== '' ? $due_date : null, $priority, $task_id, $user_id]);
        header('Location: index.php');
        exit;
    }
} else {
    $task_id = (int)($_GET['id'] ?? 0);
}

// Load the task (only if it belongs to this user)
$stmt = $pdo->prepare('SELECT id, title, due_date, priority FROM tasks WHERE id = ? AND user_id = ?');
$stmt->execute([$task_id, $user_id]);
$task = $stmt->fetch();

if (!$task) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Task - Task Manager</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Your Custom CSS -->
<link rel="stylesheet" href="style.css"> 
</head>
<body><body>
    <!-- Reuse the Navbar for consistency -->
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fa-solid fa-list-check"></i> TaskMaster
        </div>
        <div class="nav-user">
            <a href="index.php" class="btn-logout" style="background: #f1f5f9; color: #333;"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <!-- Edit Form Card -->
        <div class="add-task-container" style="max-width: 600px; margin: 40px auto;">
            <h2><i class="fa-solid fa-pen-to-square"></i> Edit Task</h2>
            
            <?php if ($error): ?>
                <p style="color:#e11d48; font-size: 0.9rem; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="POST" class="add-task-form" style="flex-direction: column;">
                <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
                
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.9rem; color: #555; margin-bottom: 5px; display: block;">Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required style="width: 100%;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-size: 0.9rem; color: #555; margin-bottom: 5px; display: block;">Due Date</label>
                    <input type="date" name="due_date" value="<?= htmlspecialchars($task['due_date'] ?? '') ?>" style="width: 100%;">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="font-size: 0.9rem; color: #555; margin-bottom: 5px; display: block;">Priority</label>
                    <select name="priority" style="width: 100%;">
                        <option <?= $task['priority'] === 'High' ? 'selected' : '' ?>>High</option>
                        <option <?= $task['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option <?= $task['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-primary" style="flex: 1;">Save Changes</button>
                    <a href="index.php" class="btn-primary" style="flex: 1; text-align: center; text-decoration: none; background: #e2e8f0; color: #333;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</body>
</html>
