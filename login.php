<?php
session_start();
function e($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    require __DIR__ . '/db.php';
    $stmt = $pdo->prepare('SELECT id, fullname, username, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullname'] = $user['fullname'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>Log in</h2>
  <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
  <form method="POST" action="login.php">
    <label>Username</label><input type="text" name="username" required>
    <label>Password</label><input type="password" name="password" required>
    <button type="submit">Log In</button>
  </form>
  <p class="alt">No account? <a href="signup.php">Sign up</a></p>
</div>
</body>
</html>
