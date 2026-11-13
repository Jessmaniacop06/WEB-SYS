<?php

function field_class(array $errors, string $f): string {
    return 'form-control' . (isset($errors[$f]) ? ' is-invalid' : '');
}
?>
<form method="post" class="panel p-4" novalidate>
    <div class="panel-title mb-3">User details</div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">First name</label>
            <input type="text" name="firstname" class="<?= field_class($errors, 'firstname') ?>"
                   value="<?= e($in['firstname']) ?>">
            <div class="invalid-feedback"><?= e($errors['firstname'] ?? '') ?></div>
        </div>
        <div class="col-md-4">
            <label class="form-label">Middle name <span class="fw-normal">(optional)</span></label>
            <input type="text" name="middlename" class="form-control"
                   value="<?= e($in['middlename']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Last name</label>
            <input type="text" name="lastname" class="<?= field_class($errors, 'lastname') ?>"
                   value="<?= e($in['lastname']) ?>">
            <div class="invalid-feedback"><?= e($errors['lastname'] ?? '') ?></div>
        </div>

        <div class="col-md-4">
            <label class="form-label">Birthdate</label>
            <input type="date" name="birthdate" max="<?= date('Y-m-d') ?>"
                   class="<?= field_class($errors, 'birthdate') ?>"
                   value="<?= e($in['birthdate']) ?>">
            <div class="invalid-feedback"><?= e($errors['birthdate'] ?? '') ?></div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select<?= isset($errors['gender']) ? ' is-invalid' : '' ?>">
                <option value="">Select gender</option>
                <option value="Male"   <?= $in['gender'] === 'Male'   ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $in['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
            <div class="invalid-feedback"><?= e($errors['gender'] ?? '') ?></div>
        </div>
        <div class="col-md-5">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="<?= field_class($errors, 'email') ?>"
                   value="<?= e($in['email']) ?>">
            <div class="invalid-feedback"><?= e($errors['email'] ?? '') ?></div>
        </div>
    </div>

    <div class="panel-title mb-3">Enrolled courses</div>
    <div class="row g-2 mb-4">
        <?php if (!$courses): ?>
            <div class="col-12 no-course">No courses available yet.</div>
        <?php endif; ?>
        <?php foreach ($courses as $c): ?>
            <div class="col-md-6">
                <label class="course-option">
                    <input type="checkbox" class="form-check-input" name="courses[]"
                           value="<?= (int)$c['course_id'] ?>"
                           <?= in_array((int)$c['course_id'], $selected, true) ? 'checked' : '' ?>>
                    <span class="course-code"><?= e($c['course_code']) ?></span>
                    <span class="course-name"><?= e($c['name']) ?></span>
                    <span class="pill pill-<?= e($c['status']) ?> ms-1"><?= strtoupper(e($c['status'])) ?></span>
                    <?php if ($c['description']): ?>
                        <div class="course-desc mt-1"><?= e($c['description']) ?></div>
                    <?php endif; ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-brand px-4"><?= e($buttonLabel) ?></button>
        <a href="index.php" class="btn btn-soft">Cancel</a>
    </div>
</form>
