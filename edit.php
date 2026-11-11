<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$workspace_id = $_SESSION['workspace_id'];
$error = '';

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
        $stmt = $pdo->prepare('UPDATE tasks SET title = ?, due_date = ?, priority = ? WHERE id = ? AND workspace_id = ?');
        $stmt->execute([$title, $due_date !== '' ? $due_date : null, $priority, $task_id, $workspace_id]);
        header('Location: index.php');
        exit;
    }
} else {
    $task_id = (int)($_GET['id'] ?? 0);
}

$stmt = $pdo->prepare('SELECT id, title, due_date, priority FROM tasks WHERE id = ? AND workspace_id = ?');
$stmt->execute([$task_id, $workspace_id]);
$task = $stmt->fetch();

if (!$task) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Task - TaskMaster</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="nav-brand"><i class="fa-solid fa-layer-group"></i> TaskMaster</div>
            <ul class="nav-links">
                <li><a href="index.php"><i class="fa-solid fa-border-all"></i> Dashboard</a></li>
                <li><a href="team.php"><i class="fa-solid fa-users-gear"></i> Team Admin</a></li>
            </ul>
            <div class="sidebar-bottom">
                <button onclick="showLogoutModal()" class="logout-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log out</button>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1>Edit Task</h1>
                <div class="header-actions">
                    <button id="theme-toggle" class="btn-icon" title="Toggle Dark Mode" onclick="toggleTheme()"><i class="fa-solid fa-moon" id="theme-icon"></i></button>
                    <div class="user-profile"><i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars($_SESSION['username']) ?></div>
                </div>
            </header>

            <div class="add-task-container" style="max-width: 600px;">
                <h2><i class="fa-solid fa-pen-to-square"></i> Update your task</h2>
                <?php if ($error): ?><p style="color:#e11d48; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
                <form method="POST" class="edit-form" id="editForm">
                    <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
                    <div class="input-group">
                        <label>Task Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date" value="<?= htmlspecialchars($task['due_date'] ?? '') ?>">
                    </div>
                    <div class="input-group">
                        <label>Priority Level</label>
                        <div class="custom-select">
                            <select name="priority">
                                <option <?= $task['priority'] === 'High' ? 'selected' : '' ?>>High</option>
                                <option <?= $task['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                                <option <?= $task['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
                            </select>
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Save Changes</button>
                        <a href="index.php" class="btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <div id="customModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <h3 id="modalTitle">Are you sure?</h3>
            <p id="modalMessage">You have unsaved changes.</p>
            <div class="modal-actions">
                <button id="modalCancelBtn" class="btn-secondary">Go Back</button>
                <button id="modalConfirmBtn" class="btn-primary">Yes, Continue</button>
            </div>
        </div>
    </div>

    <div id="logoutModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></div>
            <h3>Log Out?</h3>
            <p>Are you sure you want to log out of your workspace?</p>
            <div class="modal-actions">
                <button onclick="hideLogoutModal()" class="btn-secondary">Cancel</button>
                <a href="logout.php" class="btn-primary" style="background: #ef4444; text-decoration: none; display: flex; align-items: center; justify-content: center;">Yes, Log Out</a>
            </div>
        </div>
    </div>

    <script src="theme.js"></script>
    <script>
        const form = document.getElementById('editForm');
        let formChanged = false;
        const modal = document.getElementById('customModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalConfirmBtn = document.getElementById('modalConfirmBtn');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        let actionToConfirm = null;

        function showModal(title, message, action) { modalTitle.innerText = title; modalMessage.innerText = message; actionToConfirm = action; modal.classList.add('active'); }
        function hideModal() { modal.classList.remove('active'); actionToConfirm = null; }
        form.addEventListener('input', () => { formChanged = true; });
        form.addEventListener('submit', (e) => { e.preventDefault(); showModal("Save Changes?", "Are you sure you want to update this task?", () => { formChanged = false; form.submit(); }); });
        document.querySelector('.btn-secondary').addEventListener('click', (e) => {
            e.preventDefault();
            if (formChanged) { showModal("Discard Changes?", "You have unsaved changes. Are you sure you want to cancel?", () => { window.location.href = 'index.php'; }); } 
            else { window.location.href = 'index.php'; }
        });
        modalConfirmBtn.addEventListener('click', () => { if (actionToConfirm) actionToConfirm(); hideModal(); });
        modalCancelBtn.addEventListener('click', hideModal);
        modal.addEventListener('click', (e) => { if (e.target === modal) hideModal(); });

        function showLogoutModal() { document.getElementById('logoutModal').classList.add('active'); }
        function hideLogoutModal() { document.getElementById('logoutModal').classList.remove('active'); }
        document.getElementById('logoutModal').addEventListener('click', (e) => { if (e.target.id === 'logoutModal') hideLogoutModal(); });
    </script>
</body>
</html>