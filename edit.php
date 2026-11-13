<?php
require __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? $_POST['user_id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM `user` WHERE user_id = ?");
$stmt->execute([$id]);
$existing = $stmt->fetch();

if (!$existing) {
    die("User not found. <a href='index.php'>Back to list</a>");
}

$courses = all_courses($pdo);
$errors  = [];

$in = [
    'firstname'  => $existing['firstname'],
    'lastname'   => $existing['lastname'],
    'middlename' => (string)$existing['middlename'],
    'birthdate'  => $existing['birthdate'],
    'gender'     => $existing['gender'],
    'email'      => $existing['email'],
];

$cs = $pdo->prepare("SELECT course_id FROM user_course WHERE user_id = ?");
$cs->execute([$id]);
$selected = array_map('intval', array_column($cs->fetchAll(), 'course_id'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $in       = read_user_input();
    $selected = read_course_ids($pdo);
    $errors   = validate_user($pdo, $in, $id);  

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $upd = $pdo->prepare(
                "UPDATE `user`
                 SET firstname = ?, lastname = ?, middlename = ?, birthdate = ?, gender = ?, email = ?
                 WHERE user_id = ?"
            );
            $upd->execute([
                $in['firstname'], $in['lastname'],
                $in['middlename'] !== '' ? $in['middlename'] : null,
                $in['birthdate'], $in['gender'], $in['email'], $id,
            ]);
            sync_courses($pdo, $id, $selected);
            $pdo->commit();
            header("Location: index.php?msg=updated");
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
    <title>Edit User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="topbar mb-4">
    <div class="container">
        <div class="brand">User<span>&amp;</span>Course Manager</div>
        <div class="sub">Edit user #<?= (int)$id ?></div>
    </div>
</div>

<div class="container pb-5" style="max-width: 860px;">
    <?php if ($errors): ?>
        <div class="alert alert-danger border-0 shadow-sm">Please fix the highlighted fields.</div>
    <?php endif; ?>

    <?php
    $buttonLabel = 'Update User';
    include __DIR__ . '/form.php';
    ?>
</div>
</body>
</html>
