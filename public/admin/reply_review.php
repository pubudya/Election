<?php
require_once __DIR__ . '/../../src/models/Database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $reply = trim($_POST['reply'] ?? '');
    if ($id > 0 && $reply) {
        $db = new DatabaseModel();
        $stmt = $db->getConnection()->prepare("UPDATE reviews_queries SET reply=?, replied_at=NOW() WHERE id=?");
        $stmt->execute([$reply, $id]);
        header('Location: index.php?success=Reply+sent');
        exit;
    } else {
        header('Location: index.php?error=Missing+reply+or+ID');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
} 