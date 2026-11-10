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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Tasks - Task Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
    <h1>My Tasks</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!
       <a href="logout.php">Log out</a></p>

    <h2>Add a task</h2>
    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="title" placeholder="Task title" maxlength="100" required>
        <input type="date" name="due_date">
        <select name="priority">
            <option>High</option>
            <option selected>Medium</option>
            <option>Low</option>
        </select>
        <button type="submit">Add</button>
    </form>

    <h2>Your tasks</h2>
    <?php if (count($tasks) === 0): ?>
        <p>No tasks yet. Add your first one above.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr><th>Title</th><th>Due date</th><th>Priority</th><th>Done?</th><th>Action</th><th>Delete</th></tr>
            <?php foreach ($tasks as $task): ?>
                <tr>                    
                    <td><?= htmlspecialchars($task['title']) ?> <a href="edit.php?id=<?= (int)$task['id'] ?>">[Edit]</a></td>                    
                    <td><?= htmlspecialchars($task['due_date'] ?? '-') ?></td>
                    <td class="priority-<?= htmlspecialchars($task['priority']) ?>"><?= htmlspecialchars($task['priority']) ?></td>
                    <td><?= $task['is_done'] ? 'Yes' : 'No' ?></td>
                    <td>
                        <form method="POST" action="toggle.php">
                            <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
                            <button type="submit"><?= $task['is_done'] ? 'Undo' : 'Mark done' ?></button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="delete.php" onsubmit="return confirm('Delete this task?');">
                            <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
                            <button type="submit" class="btn-danger">Delete</button>
                        </form>
                    </td>                
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</body>
</html>