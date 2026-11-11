<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$workspace_id = $_SESSION['workspace_id'];
$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'promoted') $message = 'Member promoted to Admin.';
    if ($_GET['msg'] === 'demoted') $message = 'Admin demoted to Member.';
    if ($_GET['msg'] === 'removed') $message = 'Member removed from the team.';
}

$stmt = $pdo->prepare('SELECT role FROM workspace_members WHERE user_id = ? AND workspace_id = ?');
$stmt->execute([$user_id, $workspace_id]);
$my_role = $stmt->fetchColumn();

if ($my_role !== 'admin') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Access Denied - TaskMaster</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="auth-wrapper">
            <div class="auth-card" style="text-align: center;">
                <div class="modal-icon" style="background: #fef2f2; color: #ef4444; margin: 0 auto 20px auto;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h1 style="font-size: 1.5rem; margin-bottom: 10px;">Access Denied</h1>
                <p style="color: #666; margin-bottom: 25px;">You do not have Admin privileges to view the Team Management dashboard.</p>
                <a href="index.php" class="btn-primary" style="text-decoration: none; display: block; padding: 12px; border-radius: 6px;">Return to Dashboard</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $target_user_id = (int)$_POST['target_user_id'];
    $action = $_POST['action'];

    if ($target_user_id === $user_id) {
        $error = "You cannot perform this action on yourself.";
    } else {
        if ($action === 'promote') {
            $stmt = $pdo->prepare('UPDATE workspace_members SET role = "admin" WHERE user_id = ? AND workspace_id = ?');
            $stmt->execute([$target_user_id, $workspace_id]);
            header('Location: team.php?msg=promoted');
            exit;
        } elseif ($action === 'demote') {
            $stmt = $pdo->prepare('UPDATE workspace_members SET role = "member" WHERE user_id = ? AND workspace_id = ?');
            $stmt->execute([$target_user_id, $workspace_id]);
            header('Location: team.php?msg=demoted');
            exit;
        } elseif ($action === 'remove') {
            $stmt = $pdo->prepare('DELETE FROM workspace_members WHERE user_id = ? AND workspace_id = ?');
            $stmt->execute([$target_user_id, $workspace_id]);
            header('Location: team.php?msg=removed');
            exit;
        }
    }
}

$stmt = $pdo->prepare('
    SELECT u.id, u.username, wm.role 
    FROM users u 
    JOIN workspace_members wm ON u.id = wm.user_id 
    WHERE wm.workspace_id = ?
');
$stmt->execute([$workspace_id]);
$members = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team Admin - TaskMaster</title>
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
                <li class="active"><a href="team.php"><i class="fa-solid fa-users-gear"></i> Team Admin</a></li>
            </ul>
            <div class="sidebar-bottom">
                <button onclick="showLogoutModal()" class="logout-btn">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Log out
                </button>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-header">
                <h1>Team Management</h1>
                <div class="header-actions">
                    <button id="theme-toggle" class="btn-icon" title="Toggle Dark Mode" onclick="toggleTheme()"><i class="fa-solid fa-moon" id="theme-icon"></i></button>
                    <div class="user-profile"><i class="fa-solid fa-circle-user"></i> <?= htmlspecialchars($_SESSION['username']) ?> (Admin)</div>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="toast show" style="position: relative; margin-bottom: 20px; transform: none; border-left-color: #10b981; box-shadow: none; border: 1px solid #eaeaea; border-left: 4px solid #10b981;">
                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                    <div class="toast-content"><h4>Success!</h4><p><?= htmlspecialchars($message) ?></p></div>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="toast show" style="position: relative; margin-bottom: 20px; transform: none; border-left-color: #e11d48; box-shadow: none; border: 1px solid #eaeaea; border-left: 4px solid #e11d48;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #e11d48;"></i>
                    <div class="toast-content"><h4>Error</h4><p><?= htmlspecialchars($error) ?></p></div>
                </div>
            <?php endif; ?>

            <div class="add-task-container" style="text-align: center; background: #111; color: white; border: none;">
                <h2 style="color: white; justify-content: center;"><i class="fa-solid fa-key"></i> Team Join Code</h2>
                <p style="margin-bottom: 15px; opacity: 0.9;">Share this code with your team so they can log in.</p>
                <div style="font-size: 2.5rem; font-weight: 600; letter-spacing: 5px; background: rgba(255,255,255,0.1); padding: 10px 30px; border-radius: 8px; display: inline-block;">
                    <?= $workspace_id ?>
                </div>
            </div>

            <div class="add-task-container" style="padding: 0; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb; border-bottom: 1px solid #eaeaea; text-align: left;">
                            <th style="padding: 15px 20px; color: #333;">Username</th>
                            <th style="padding: 15px 20px; color: #333;">Role</th>
                            <th style="padding: 15px 20px; color: #333; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr style="border-bottom: 1px solid #eaeaea;">
                                <td style="padding: 15px 20px; display: flex; align-items: center; gap: 10px;">
                                    <i class="fa-solid fa-circle-user" style="color: #111; font-size: 1.2rem;"></i>
                                    <strong><?= htmlspecialchars($member['username']) ?></strong>
                                    <?php if ($member['id'] === $user_id): ?>
                                        <span style="font-size: 0.7rem; background: #f0f0f0; color: #111; padding: 2px 8px; border-radius: 10px;">You</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px 20px;">
                                    <span style="background: <?= $member['role'] === 'admin' ? '#f0fdf4' : '#f9fafb' ?>; color: <?= $member['role'] === 'admin' ? '#16a34a' : '#666' ?>; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                                        <?= $member['role'] ?>
                                    </span>
                                </td>
                                <td style="padding: 15px 20px; text-align: right;">
                                    <?php if ($member['id'] !== $user_id): ?>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="target_user_id" value="<?= $member['id'] ?>">
                                            <?php if ($member['role'] === 'member'): ?>
                                                <button type="submit" name="action" value="promote" class="btn-icon" title="Make Admin" style="color: #16a34a;"><i class="fa-solid fa-arrow-up"></i></button>
                                            <?php else: ?>
                                                <button type="submit" name="action" value="demote" class="btn-icon" title="Remove Admin" style="color: #d97706;"><i class="fa-solid fa-arrow-down"></i></button>
                                            <?php endif; ?>
                                            <button type="button" class="btn-icon btn-remove-trigger" title="Kick from Team" style="color: #ef4444;"><i class="fa-solid fa-user-xmark"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="customModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-icon" style="background: #fef2f2; color: #ef4444;"><i class="fa-solid fa-user-xmark"></i></div>
            <h3>Remove Member?</h3>
            <p>Are you sure you want to remove this person from the team? They will lose access to all team tasks.</p>
            <div class="modal-actions">
                <button id="modalCancelBtn" class="btn-secondary">Cancel</button>
                <button id="modalConfirmBtn" class="btn-primary" style="background: #ef4444;">Yes, Remove</button>
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
        const modal = document.getElementById('customModal');
        const modalConfirmBtn = document.getElementById('modalConfirmBtn');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        const removeButtons = document.querySelectorAll('.btn-remove-trigger');
        let formToSubmit = null;

        removeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                formToSubmit = e.target.closest('form');
                modal.classList.add('active');
            });
        });

        modalConfirmBtn.addEventListener('click', () => {
            if (formToSubmit) {
                const hiddenAction = document.createElement('input');
                hiddenAction.type = 'hidden';
                hiddenAction.name = 'action';
                hiddenAction.value = 'remove';
                formToSubmit.appendChild(hiddenAction);
                formToSubmit.submit();
            }
        });

        modalCancelBtn.addEventListener('click', () => { modal.classList.remove('active'); formToSubmit = null; });
        modal.addEventListener('click', (e) => { if (e.target === modal) { modal.classList.remove('active'); formToSubmit = null; } });

        function showLogoutModal() { document.getElementById('logoutModal').classList.add('active'); }
        function hideLogoutModal() { document.getElementById('logoutModal').classList.remove('active'); }
        document.getElementById('logoutModal').addEventListener('click', (e) => { if (e.target.id === 'logoutModal') hideLogoutModal(); });
    </script>
</body>
</html>