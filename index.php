<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$workspace_id = $_SESSION['workspace_id'];
$error = '';

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
            'INSERT INTO tasks (user_id, workspace_id, title, due_date, priority) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$user_id, $workspace_id, $title, $due_date !== '' ? $due_date : null, $priority]);
        header('Location: index.php');
        exit;
    }
}

$stmt = $pdo->prepare(
    'SELECT id, title, due_date, priority, is_done
     FROM tasks WHERE workspace_id = ? ORDER BY is_done, due_date'
);
$stmt->execute([$workspace_id]);
$tasks = $stmt->fetchAll();

$total_tasks = count($tasks);
$completed_tasks = 0;
$pending_tasks = 0;

foreach ($tasks as $task) {
    if ($task['is_done']) { $completed_tasks++; } else { $pending_tasks++; }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - TaskMaster</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="app-container">
        <aside class="sidebar">
            <div class="nav-brand">
                <i class="fa-solid fa-layer-group"></i> TaskMaster
            </div>
            <ul class="nav-links">
                <li class="active"><a href="index.php"><i class="fa-solid fa-border-all"></i> Dashboard</a></li>
                <li><a href="team.php"><i class="fa-solid fa-users-gear"></i> Team Admin</a></li>
            </ul>
            <div class="sidebar-bottom">
                <button onclick="showLogoutModal()" class="logout-btn">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Log out
                </button>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1>Dashboard</h1>
                <div class="header-actions">
                    <button id="theme-toggle" class="btn-icon" title="Toggle Dark Mode" onclick="toggleTheme()">
                        <i class="fa-solid fa-moon" id="theme-icon"></i>
                    </button>
                    <div class="user-profile">
                        <i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars($_SESSION['username']) ?>
                    </div>
                </div>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bg-blue"><i class="fa-solid fa-list-check"></i></div>
                    <div class="stat-info"><h3>Total Tasks</h3><p><?= $total_tasks ?></p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-green"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="stat-info"><h3>Completed</h3><p><?= $completed_tasks ?></p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-orange"><i class="fa-solid fa-clock"></i></div>
                    <div class="stat-info"><h3>Pending</h3><p><?= $pending_tasks ?></p></div>
                </div>
            </div>

            <div class="add-task-container">
                <h2><i class="fa-solid fa-plus-circle"></i> Add a new task</h2>
                <?php if ($error): ?>
                    <p style="color:#e11d48; font-size: 0.9rem; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></p>
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

            <div class="tasks-section">
                <div class="tasks-header">
                    <h2>Your Tasks</h2>
                    <div class="filter-bar">
                        <input type="text" id="searchInput" placeholder="Search tasks..." onkeyup="filterTasks()">
                        <div class="filter-buttons">
                            <button class="filter-btn active" onclick="filterPriority('all', this)">All</button>
                            <button class="filter-btn" onclick="filterPriority('high', this)">High</button>
                            <button class="filter-btn" onclick="filterPriority('medium', this)">Medium</button>
                            <button class="filter-btn" onclick="filterPriority('low', this)">Low</button>
                        </div>
                    </div>
                </div>

                <?php if (count($tasks) === 0): ?>
                    <p style="color: #666; margin-top: 20px;">No tasks yet. Add your first one above.</p>
                <?php else: ?>
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
                                    <a href="edit.php?id=<?= (int)$task['id'] ?>" class="btn-icon btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                    <form method="POST" action="toggle.php" style="display:inline;">
                                        <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
                                        <button type="submit" class="btn-icon btn-toggle" title="<?= $task['is_done'] ? 'Undo' : 'Mark Done' ?>"><i class="fa-solid <?= $task['is_done'] ? 'fa-rotate-left' : 'fa-check' ?>"></i></button>
                                    </form>
                                    <form method="POST" action="delete.php" style="display:inline;" onsubmit="return confirm('Delete this task?');">
                                        <input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- LOGOUT MODAL -->
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
        let currentPriority = 'all';
        function filterTasks() {
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.task-card');
            cards.forEach(card => {
                const title = card.querySelector('h3').innerText.toLowerCase();
                const badge = card.querySelector('.badge');
                let cardPriority = 'all';
                if (badge.classList.contains('priority-high')) cardPriority = 'high';
                if (badge.classList.contains('priority-medium')) cardPriority = 'medium';
                if (badge.classList.contains('priority-low')) cardPriority = 'low';
                const matchesSearch = title.includes(searchText);
                const matchesPriority = (currentPriority === 'all' || cardPriority === currentPriority);
                if (matchesSearch && matchesPriority) { card.style.display = 'flex'; } else { card.style.display = 'none'; }
            });
        }
        function filterPriority(priority, btnElement) {
            currentPriority = priority;
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            btnElement.classList.add('active');
            filterTasks();
        }

        function showLogoutModal() { document.getElementById('logoutModal').classList.add('active'); }
        function hideLogoutModal() { document.getElementById('logoutModal').classList.remove('active'); }
        document.getElementById('logoutModal').addEventListener('click', (e) => { if (e.target.id === 'logoutModal') hideLogoutModal(); });
    </script>
</body>
</html>