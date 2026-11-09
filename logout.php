<?php
session_start();

// Remove all session data and destroy the session
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;