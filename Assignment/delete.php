<?php
require __DIR__ . '/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $id = (int)$_POST['user_id'];

 
    $stmt = $pdo->prepare("DELETE FROM `user` WHERE user_id = ?");
    $stmt->execute([$id]);

    header("Location: index.php?msg=deleted");
    exit;
}

header("Location: index.php");
exit;
