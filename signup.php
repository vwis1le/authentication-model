<?php
session_start();
function e($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$errors = [];
$success = '';
$old = ['fullname' => '', 'username' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $k => $v) { $old[$k] = trim($_POST[$k] ?? ''); }
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($old['fullname'] === '') $errors[] = 'Full name is required.';
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $old['username'])) $errors[] = 'Username must be 3-30 letters, numbers or underscores.';
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        require __DIR__ . '/db.php';
        $chk = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $chk->execute([$old['username'], $old['email']]);
        if ($chk->fetch()) {
            $errors[] = 'Username or email is already registered.';
        } else {
            $ins = $pdo->prepare('INSERT INTO users (fullname, username, email, phone, password_hash) VALUES (?,?,?,?,?)');
            $ins->execute([$old['fullname'], $old['username'], $old['email'], $old['phone'], password_hash($password, PASSWORD_DEFAULT)]);
            $success = 'Account created successfully. You can now log in.';
            $old = array_fill_keys(array_keys($old), '');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign Up</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card">
  <h2>Create an account</h2>
  <?php foreach ($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?>
  <?php if ($success): ?><div class="ok"><?= e($success) ?></div><?php endif; ?>
  <form method="POST" action="signup.php">
    <label>Full name</label><input type="text" name="fullname" value="<?= e($old['fullname']) ?>" required>
    <label>Username</label><input type="text" name="username" value="<?= e($old['username']) ?>" required>
    <label>Email</label><input type="email" name="email" value="<?= e($old['email']) ?>" required>
    <label>Phone</label><input type="text" name="phone" value="<?= e($old['phone']) ?>">
    <label>Password</label><input type="password" name="password" required>
    <label>Confirm password</label><input type="password" name="confirm" required>
    <button type="submit">Sign Up</button>
  </form>
  <p class="alt">Already registered? <a href="login.php">Log in</a></p>
</div>
</body>
</html>
