<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
function e($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Dashboard</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>Welcome, <?= e($_SESSION['fullname']) ?></h2>
  <div class="ok">Login successful. You are signed in as <b><?= e($_SESSION['username']) ?></b>.</div>
  <p class="alt"><a href="feedback.php">Course feedback form</a> | <a href="logout.php">Log out</a></p>
</div>
</body>
</html>
