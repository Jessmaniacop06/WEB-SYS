<?php
require __DIR__ . '/config.php';


$keyword = trim($_GET['keyword'] ?? '');
$gender  = trim($_GET['gender']  ?? '');
$from    = trim($_GET['from']    ?? '');
$to      = trim($_GET['to']      ?? '');

$where  = [];
$params = [];

if ($keyword !== '') {
 
    $like    = "%$keyword%";
    $where[] = "(u.firstname LIKE ? OR u.lastname LIKE ? OR u.middlename LIKE ?
                 OR EXISTS (
                     SELECT 1 FROM user_course x
                     JOIN course c ON c.course_id = x.course_id
                     WHERE x.user_id = u.user_id
                       AND (c.description LIKE ? OR c.course_code LIKE ?)
                 ))";
    array_push($params, $like, $like, $like, $like, $like);
}
if (in_array($gender, ['Male', 'Female'], true)) {
    $where[]  = "u.gender = ?";
    $params[] = $gender;
}
if ($from !== '') {
    $where[]  = "DATE(u.date_created) >= ?";
    $params[] = $from;
}
if ($to !== '') {
    $where[]  = "DATE(u.date_created) <= ?";
    $params[] = $to;
}

$sql = "SELECT u.* FROM `user` u";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY u.user_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();


$coursesByUser = [];
if ($users) {
    $ids = array_column($users, 'user_id');
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $cs  = $pdo->prepare(
        "SELECT uc.user_id, c.*
         FROM user_course uc
         JOIN course c ON c.course_id = uc.course_id
         WHERE uc.user_id IN ($in)
         ORDER BY c.name"
    );
    $cs->execute($ids);
    foreach ($cs->fetchAll() as $c) {
        $coursesByUser[$c['user_id']][] = $c;
    }
}


$messages = [
    'added'   => 'User added successfully.',
    'updated' => 'User updated successfully.',
    'deleted' => 'User deleted successfully.',
];
$flash = $messages[$_GET['msg'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User &amp; Course Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>

<div class="topbar mb-4">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <div class="brand">User<span>&amp;</span>Course Manager</div>
            <div class="sub">Manage users and their enrolled courses</div>
        </div>
        <a href="add.php" class="btn btn-light fw-bold">+ Add User</a>
    </div>
</div>

<div class="container pb-5">

    <?php if ($flash): ?>
        <div class="alert alert-success border-0 shadow-sm"><?= e($flash) ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <form method="get" class="panel p-3 mb-4">
        <div class="panel-title mb-2">Filter</div>
        <div class="row g-2 align-items-end">
            <div class="col-lg-4 col-md-6">
                <label class="form-label">Keyword</label>
                <input type="text" name="keyword" class="form-control"
                       placeholder="Name, course description or course code"
                       value="<?= e($keyword) ?>">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">All</option>
                    <option value="Male"   <?= $gender === 'Male'   ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <label class="form-label">Created from</label>
                <input type="date" name="from" class="form-control" value="<?= e($from) ?>">
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <label class="form-label">Created to</label>
                <input type="date" name="to" class="form-control" value="<?= e($to) ?>">
            </div>
            <div class="col-lg-2 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-brand flex-fill">Search</button>
                <a href="index.php" class="btn btn-soft">Reset</a>
            </div>
        </div>
    </form>

    <!-- Listing -->
    <div class="panel overflow-hidden">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <div class="panel-title">Users</div>
            <div class="text-muted small"><?= count($users) ?> result<?= count($users) === 1 ? '' : 's' ?></div>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Birthdate</th>
                        <th>Gender</th>
                        <th>Email</th>
                        <th>Courses</th>
                        <th>Date Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$users): ?>
                    <tr><td colspan="7"><div class="empty-state">No match found.</div></td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <?php
                        $full = trim($u['firstname'] . ' ' . ($u['middlename'] ? $u['middlename'] . ' ' : '') . $u['lastname']);
                        $initials = strtoupper(mb_substr($u['firstname'], 0, 1) . mb_substr($u['lastname'], 0, 1));
                        $myCourses = $coursesByUser[$u['user_id']] ?? [];
                        ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar"><?= e($initials) ?></div>
                                    <div>
                                        <div class="user-name"><?= e($full) ?></div>
                                        <div class="user-id">ID #<?= (int)$u['user_id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap"><?= e(date('M d, Y', strtotime($u['birthdate']))) ?></td>
                            <td>
                                <span class="pill <?= $u['gender'] === 'Male' ? 'pill-male' : 'pill-female' ?>">
                                    <?= e($u['gender']) ?>
                                </span>
                            </td>
                            <td><?= e($u['email']) ?></td>
                            <td>
                                <?php if (!$myCourses): ?>
                                    <span class="no-course">No courses</span>
                                <?php else: ?>
                                    <?php foreach ($myCourses as $c): ?>
                                        <div class="course-item <?= $c['status'] === 'off' ? 'is-off' : '' ?>">
                                            <div>
                                                <span class="course-code"><?= e($c['course_code']) ?></span>
                                                <span class="course-name"><?= e($c['name']) ?></span>
                                                <span class="pill pill-<?= e($c['status']) ?> ms-1"><?= strtoupper(e($c['status'])) ?></span>
                                            </div>
                                            <?php if ($c['description'] !== null && $c['description'] !== ''): ?>
                                                <div class="course-desc"><?= e($c['description']) ?></div>
                                            <?php endif; ?>
                                            <div class="course-desc">Created <?= e(date('M d, Y', strtotime($c['date_created']))) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-nowrap small text-muted"><?= e(date('M d, Y g:i A', strtotime($u['date_created']))) ?></td>
                            <td class="text-end text-nowrap">
                                <a href="edit.php?id=<?= (int)$u['user_id'] ?>" class="btn btn-soft btn-sm">Edit</a>
                                <form method="post" action="delete.php" class="d-inline"
                                      onsubmit="return confirm('Delete this user?');">
                                    <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                                    <button type="submit" class="btn btn-soft-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
