<?php
require_once __DIR__ . '/../src/models/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message) {
        $db = new DatabaseModel();
        $stmt = $db->getConnection()->prepare("INSERT INTO reviews_queries (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        header('Location: index.php?success=Thank+you+for+your+feedback!');
        exit;
    } else {
        header('Location: index.php?error=Please+fill+all+required+fields');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
} 