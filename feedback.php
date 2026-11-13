<?php
session_start();
if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
require __DIR__ . '/db.php';
function e($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$courses = ['Server Administration', 'Network Security', 'Cryptography', 'Web Technologies'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course   = $_POST['course'] ?? '';
    $rating   = (int)($_POST['rating'] ?? 0);
    $comments = trim($_POST['comments'] ?? '');

    if (!in_array($course, $courses, true)) $errors[] = 'Choose a valid course.';
    if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5.';
    if ($comments === '' || strlen($comments) > 500) $errors[] = 'Comments are required (max 500 characters).';

    if (!$errors) {
        $ins = $pdo->prepare('INSERT INTO feedback (user_id, username, course, rating, comments) VALUES (?,?,?,?,?)');
        $ins->execute([$_SESSION['user_id'], $_SESSION['username'], $course, $rating, $comments]);
        $_SESSION['flash'] = 'Feedback submitted successfully.';
        header('Location: feedback.php');
        exit;
    }
}

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
$rows = $pdo->query('SELECT id, username, course, rating, comments, created_at FROM feedback ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Course Feedback</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="card wide">
  <h2>Course Feedback</h2>
  <p>Signed in as <b><?= e($_SESSION['username']) ?></b> | <a href="dashboard.php">Dashboard</a> | <a href="logout.php">Log out</a></p>
  <?php foreach ($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?>
  <?php if ($flash): ?><div class="ok"><?= e($flash) ?></div><?php endif; ?>

  <form method="POST" action="feedback.php">
    <label>Course</label>
    <select name="course" required>
      <?php foreach ($courses as $c): ?><option value="<?= e($c) ?>"><?= e($c) ?></option><?php endforeach; ?>
    </select>
    <label>Rating (1 to 5)</label>
    <input type="number" name="rating" min="1" max="5" required>
    <label>Comments</label>
    <textarea name="comments" rows="4" maxlength="500" required></textarea>
    <button type="submit">Submit Feedback</button>
  </form>

  <h3>All feedback (<?= count($rows) ?>)</h3>
  <div style="overflow-x:auto">
  <table>
    <tr><th>ID</th><th>User</th><th>Course</th><th>Rating</th><th>Comments</th><th>Date</th></tr>
    <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= (int)$r['id'] ?></td>
      <td><?= e($r['username']) ?></td>
      <td><?= e($r['course']) ?></td>
      <td><?= (int)$r['rating'] ?></td>
      <td><?= e($r['comments']) ?></td>
      <td><?= e($r['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  </div>
</div>
</body>
</html>
