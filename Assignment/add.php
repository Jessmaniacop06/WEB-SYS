<?php
require __DIR__ . '/config.php';

$courses  = all_courses($pdo);
$errors   = [];
$selected = [];
$in = [
    'firstname' => '', 'lastname' => '', 'middlename' => '',
    'birthdate' => '', 'gender' => '', 'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in       = read_user_input();
    $selected = read_course_ids($pdo);
    $errors   = validate_user($pdo, $in);  

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare(
                "INSERT INTO `user` (firstname, lastname, middlename, birthdate, gender, email)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $in['firstname'], $in['lastname'],
                $in['middlename'] !== '' ? $in['middlename'] : null,
                $in['birthdate'], $in['gender'], $in['email'],
            ]);
            sync_courses($pdo, (int)$pdo->lastInsertId(), $selected);
            $pdo->commit();
            header("Location: index.php?msg=added");
            exit;
        } catch (PDOException $ex) {
            $pdo->rollBack();
           
            if ($ex->errorInfo[1] === 1062) {
                $errors['email'] = 'This email is already registered.';
            } else {
                throw $ex;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="topbar mb-4">
    <div class="container">
        <div class="brand">User<span>&amp;</span>Course Manager</div>
        <div class="sub">Add a new user</div>
    </div>
</div>

<div class="container pb-5" style="max-width: 860px;">
    <?php if ($errors): ?>
        <div class="alert alert-danger border-0 shadow-sm">Please fix the highlighted fields.</div>
    <?php endif; ?>

    <?php
    $buttonLabel = 'Save User';
    include __DIR__ . '/form.php';
    ?>
</div>
</body>
</html>
