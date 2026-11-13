<?php
header('Content-Type: application/json');
$n = filter_var($_GET['n'] ?? null, FILTER_VALIDATE_INT);
if ($n === false || $n === null) {
    http_response_code(400);
    echo json_encode(['error' => 'pass ?n=<integer>']);
    exit;
}
echo json_encode(['function' => 'square', 'n' => $n, 'square' => $n * $n, 'executed_at' => date('c')]);
