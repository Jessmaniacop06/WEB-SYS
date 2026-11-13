<?php

$host = "127.0.0.1";
$db   = "user_course_db";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function all_courses(PDO $pdo): array {
    return $pdo->query("SELECT * FROM course ORDER BY name")->fetchAll();
}


function read_user_input(): array {
    return [
        'firstname'  => trim($_POST['firstname']  ?? ''),
        'lastname'   => trim($_POST['lastname']   ?? ''),
        'middlename' => trim($_POST['middlename'] ?? ''),
        'birthdate'  => trim($_POST['birthdate']  ?? ''),
        'gender'     => trim($_POST['gender']     ?? ''),
        'email'      => trim($_POST['email']      ?? ''),
    ];
}

function validate_user(PDO $pdo, array $in, int $ignoreId = 0): array {
    $err = [];

    if ($in['firstname'] === '') $err['firstname'] = 'First name is required.';
    if ($in['lastname']  === '') $err['lastname']  = 'Last name is required.';

    if ($in['birthdate'] === '') {
        $err['birthdate'] = 'Birthdate is required.';
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $in['birthdate']);
        if (!$d || $d->format('Y-m-d') !== $in['birthdate']) {
            $err['birthdate'] = 'Birthdate is not a valid date.';
        } elseif ($in['birthdate'] > date('Y-m-d')) {
            $err['birthdate'] = 'Birthdate cannot be in the future.';
        }
    }

    if (!in_array($in['gender'], ['Male', 'Female'], true)) {
        $err['gender'] = 'Please select a gender.';
    }

    if ($in['email'] === '') {
        $err['email'] = 'Email is required.';
    } elseif (!filter_var($in['email'], FILTER_VALIDATE_EMAIL)) {
        $err['email'] = 'Email format is not valid.';
    } else {
 
        $stmt = $pdo->prepare("SELECT user_id FROM `user` WHERE email = ? AND user_id <> ?");
        $stmt->execute([$in['email'], $ignoreId]);
        if ($stmt->fetch()) {
            $err['email'] = 'This email is already registered.';
        }
    }

    return $err;
}


function read_course_ids(PDO $pdo): array {
    $ids = array_map('intval', (array)($_POST['courses'] ?? []));
    if (!$ids) return [];
    $in   = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT course_id FROM course WHERE course_id IN ($in)");
    $stmt->execute($ids);
    return array_map('intval', array_column($stmt->fetchAll(), 'course_id'));
}

function sync_courses(PDO $pdo, int $userId, array $courseIds): void {
    $pdo->prepare("DELETE FROM user_course WHERE user_id = ?")->execute([$userId]);
    $ins = $pdo->prepare("INSERT INTO user_course (user_id, course_id) VALUES (?, ?)");
    foreach ($courseIds as $cid) {
        $ins->execute([$userId, $cid]);
    }
}
