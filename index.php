<?php

$db = new PDO("sqlite:resume.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("
CREATE TABLE IF NOT EXISTS resumes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    fullname TEXT,
    jobtitle TEXT,
    phone TEXT,
    email TEXT,
    address TEXT,
    linkedin TEXT,
    objective TEXT,
    skills TEXT,
    certifications TEXT,
    languages TEXT,
    experience TEXT,
    education TEXT,
    projects TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");


if (isset($_GET["delete"])) {
    $stmt = $db->prepare("DELETE FROM resumes WHERE id = ?");
    $stmt->execute([$_GET["delete"]]);
    header("Location: index.php");
    exit;
}

function lines($text) {
    return preg_split("/\r\n|\r|\n/", (string)$text);
}

$errors = [];
$old = [
    "fullname"       => "",
    "jobtitle"       => "",
    "phone"          => "",
    "email"          => "",
    "address"       => "",
    "linkedin"       => "",
    "objective"      => "",
    "skills"         => "",
    "certifications" => "",
    "languages"      => "",
    "experience"     => "",
    "education"      => "",
    "projects"       => "",
];


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {

    foreach ($old as $field => $value) {
        $old[$field] = trim($_POST[$field] ?? "");
    }

    
    if ($old["fullname"] === "") {
        $errors["fullname"] = "Full name is required.";
    } elseif (!preg_match("/^[A-Za-z\s.'-]+$/", $old["fullname"])) {
        $errors["fullname"] = "Full name must contain letters only.";
    }

    
    if ($old["jobtitle"] === "") {
        $errors["jobtitle"] = "Job title is required.";
    }

    
    if ($old["phone"] === "") {
        $errors["phone"] = "Phone number is required.";
    } elseif (!preg_match("/^[0-9+\s()-]{7,20}$/", $old["phone"])) {
        $errors["phone"] = "Enter a valid phone number.";
    }

    
    if ($old["email"] === "") {
        $errors["email"] = "Email address is required.";
    } elseif (!filter_var($old["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Enter a valid email address.";
    }

    
    if ($old["address"] === "") {
        $errors["address"] = "address is required.";
    }

    
    if ($old["linkedin"] === "") {
        $errors["linkedin"] = "LinkedIn profile is required.";
    }

    if ($old["objective"] === "") {
        $errors["objective"] = "Career objective is required.";
    }

    
    if ($old["skills"] === "") {
        $errors["skills"] = "At least one skill is required.";
    }

  
    if ($old["certifications"] === "") {
        $errors["certifications"] = "At least one certification is required.";
    }

    
    if ($old["languages"] === "") {
        $errors["languages"] = "At least one language is required.";
    }

    
    if ($old["experience"] === "") {
        $errors["experience"] = "Work experience is required.";
    }

    if ($old["education"] === "") {
        $errors["education"] = "Education is required.";
    }

    if ($old["projects"] === "") {
        $errors["projects"] = "At least one project is required.";
    }

    if (empty($errors)) {
        $stmt = $db->prepare("
            INSERT INTO resumes (
                fullname, jobtitle, phone, email, address, linkedin,
                objective, skills, certifications, languages,
                experience, education, projects
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $old["fullname"], $old["jobtitle"], $old["phone"], $old["email"],
            $old["address"], $old["linkedin"], $old["objective"], $old["skills"],
            $old["certifications"], $old["languages"], $old["experience"],
            $old["education"], $old["projects"],
        ]);

        header("Location: index.php?resume=" . $db->lastInsertId());
        exit;
    }
}


$resume = null;
if (isset($_GET["resume"])) {
    $stmt = $db->prepare("SELECT * FROM resumes WHERE id = ?");
    $stmt->execute([$_GET["resume"]]);
    $resume = $stmt->fetch(PDO::FETCH_ASSOC);
}


$savedResumes = $db->query("
    SELECT * FROM resumes
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

function field_error($errors, $field) {
    if (!empty($errors[$field])) {
        echo '<span class="field-error">' . htmlspecialchars($errors[$field]) . '</span>';
    }
}

function field_class($errors, $field) {
    return !empty($errors[$field]) ? "has-error" : "";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resume Builder</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
    background: #f4f5f7;
    color: #2b2b33;
}

h1, h2 {
    font-family: inherit;
}

.page-heading {
    text-align: center;
    padding: 30px 20px 6px;
    color: #1f2430;
    font-size: 26px;
    font-weight: 700;
    letter-spacing: .3px;
}

.builder {
    width: 900px;
    max-width: 95%;
    margin: 20px auto 40px;
    background: #ffffff;
    padding: 36px 40px;
    border-radius: 10px;
    border: 1px solid #e3e5ea;
    box-shadow: 0 10px 30px rgba(20, 20, 40, .06);
}

.builder h1 {
    text-align: left;
    color: #1f2430;
    margin: 0 0 4px;
    font-size: 22px;
}

.builder .subtitle {
    color: #767b87;
    font-size: 13px;
    margin: 0 0 28px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px 22px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full {
    grid-column: 1 / -1;
}

label {
    font-size: 12.5px;
    font-weight: 600;
    color: #3b3f4a;
    margin-bottom: 6px;
}

label .req {
    color: #c0392b;
}

input,
textarea {
    width: 100%;
    border: 1px solid #d6d9e0;
    border-radius: 6px;
    padding: 11px 12px;
    font-family: inherit;
    font-size: 14px;
    background: #fbfbfd;
    transition: border-color .15s, box-shadow .15s;
}

textarea {
    min-height: 96px;
    resize: vertical;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: #4f6ef7;
    box-shadow: 0 0 0 3px rgba(79,110,247,.12);
    background: #fff;
}

.form-group.has-error input,
.form-group.has-error textarea {
    border-color: #d33d3d;
    background: #fff7f7;
}

.form-group.has-error input:focus,
.form-group.has-error textarea:focus {
    box-shadow: 0 0 0 3px rgba(211,61,61,.12);
}

.field-error {
    color: #d33d3d;
    font-size: 12px;
    font-weight: 600;
    margin-top: 6px;
}

.form-alert {
    background: #fdf2f2;
    border: 1px solid #f3c6c6;
    color: #a4302a;
    padding: 12px 16px;
    border-radius: 6px;
    font-size: 13px;
    margin-bottom: 22px;
}

.save-btn,
.print-btn,
.new-btn {
    border: none;
    padding: 13px;
    font-weight: 700;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    display: inline-block;
}

.save-btn {
    width: 100%;
    margin-top: 26px;
    background: #4f6ef7;
    color: white;
}

.save-btn:hover {
    background: #3b58d9;
}

.actions {
    width: 900px;
    max-width: 95%;
    margin: 24px auto 0;
    display: flex;
    gap: 10px;
}

.actions .print-btn,
.actions .new-btn {
    flex: 1;
}

.print-btn {
    background: #4f6ef7;
    color: white;
}

.new-btn {
    background: #1f2430;
    color: white;
}

.print-btn:hover {
    background: #3b58d9;
}

.new-btn:hover {
    background: #12151c;
}

.resume-container {
    width: 900px;
    max-width: 95%;
    margin: 20px auto 40px;
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(20,20,40,.08);
}

.resume-header {
    min-height: 150px;
    background: #1f2430;
    color: white;
    display: flex;
    align-items: center;
    position: relative;
    padding: 24px 40px 24px 190px;
}

.profile {
    position: absolute;
    left: 40px;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: #4f6ef7;
    overflow: hidden;
}

.profile-head {
    position: absolute;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #1f2430;
    top: 20px;
    left: 36px;
}

.profile-body {
    position: absolute;
    width: 65px;
    height: 38px;
    border-radius: 50% 50% 0 0;
    background: #1f2430;
    bottom: 7px;
    left: 22px;
}

.resume-header h1 {
    margin: 0;
    font-size: 27px;
    letter-spacing: .3px;
}

.resume-header p {
    margin-top: 8px;
    font-size: 13px;
    color: #b9c0d4;
}

.resume-body {
    display: grid;
    grid-template-columns: 220px 1fr;
}

.sidebar {
    background: #f4f5f9;
    padding: 28px 22px;
}

.side-section {
    margin-bottom: 28px;
}

.side-title {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #1f2430;
    border-bottom: 2px solid #4f6ef7;
    padding-bottom: 7px;
    margin-bottom: 12px;
}

.side-item {
    font-size: 12.5px;
    line-height: 1.6;
    margin-bottom: 8px;
    word-break: break-word;
    color: #454a56;
}

.resume-main {
    padding: 30px 34px;
}

.resume-section {
    position: relative;
    padding-left: 22px;
    margin-bottom: 26px;
    border-left: 2px solid #e3e5ea;
}

.resume-section::before {
    content: "";
    position: absolute;
    left: -6px;
    top: 2px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #4f6ef7;
}

.resume-title {
    font-size: 12.5px;
    font-weight: 800;
    letter-spacing: 1px;
    margin-bottom: 8px;
    color: #1f2430;
}

.resume-text {
    font-size: 13px;
    line-height: 1.7;
    color: #454a56;
}

.resume-text ul {
    margin: 5px 0;
    padding-left: 18px;
}

.resume-text li {
    margin-bottom: 5px;
}

.saved {
    width: 900px;
    max-width: 95%;
    margin: 30px auto 50px;
    background: #ffffff;
    padding: 28px 32px;
    border-radius: 10px;
    border: 1px solid #e3e5ea;
}

.saved h2 {
    margin-top: 0;
    color: #1f2430;
    font-size: 18px;
}

.saved-item {
    background: #fbfbfd;
    border: 1px solid #e3e5ea;
    border-left: 4px solid #4f6ef7;
    border-radius: 6px;
    padding: 15px 18px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.saved-name {
    font-weight: 700;
    color: #1f2430;
}

.saved-job {
    font-size: 12.5px;
    color: #767b87;
    margin-top: 4px;
}

.saved-actions {
    display: flex;
    gap: 6px;
}

.view-btn,
.delete-btn {
    padding: 8px 14px;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    font-size: 11.5px;
    font-weight: 700;
}

.view-btn {
    background: #1f2430;
}

.view-btn:hover {
    background: #12151c;
}

.delete-btn {
    background: #d33d3d;
}

.delete-btn:hover {
    background: #b62f2f;
}

@media print {
    body {
        background: white;
    }

    .builder,
    .actions,
    .saved,
    .page-heading {
        display: none;
    }

    .resume-container {
        margin: 0;
        width: 100%;
        max-width: none;
        box-shadow: none;
        border-radius: 0;
    }
}

@media (max-width: 700px) {
    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full {
        grid-column: auto;
    }

    .resume-body {
        grid-template-columns: 1fr;
    }

    .resume-header {
        padding: 130px 24px 24px;
        flex-direction: column;
        align-items: flex-start;
    }

    .profile {
        left: 24px;
        top: 24px;
    }

    .actions {
        flex-direction: column;
    }

    .saved-item {
        flex-direction: column;
        align-items: flex-start;
    }
}

</style>
</head>
<body>

<div class="page-heading">Resume Builder</div>

<?php if (!$resume): ?>

    <div class="builder">

        <h1>Create Your Resume</h1>
        <p class="subtitle">Fill in the fields below. All fields are required.</p>

        <?php if (!empty($errors)): ?>
            <div class="form-alert">
                Please fix the highlighted fields below before submitting.
            </div>
        <?php endif; ?>

        <form method="POST" novalidate>

            <div class="form-grid">

                <div class="form-group <?= field_class($errors, 'fullname') ?>">
                    <label>Full Name <span class="req">*</span></label>
                    <input type="text" name="fullname" placeholder="Juan Dela Cruz"
                           value="<?= htmlspecialchars($old['fullname']) ?>">
                    <?php field_error($errors, 'fullname'); ?>
                </div>

                <div class="form-group <?= field_class($errors, 'jobtitle') ?>">
                    <label>Job Title <span class="req">*</span></label>
                    <input type="text" name="jobtitle" placeholder="Computer Science Graduate"
                           value="<?= htmlspecialchars($old['jobtitle']) ?>">
                    <?php field_error($errors, 'jobtitle'); ?>
                </div>

                <div class="form-group <?= field_class($errors, 'phone') ?>">
                    <label>Phone <span class="req">*</span></label>
                    <input type="text" name="phone" placeholder="+63 912 345 6789"
                           value="<?= htmlspecialchars($old['phone']) ?>">
                    <?php field_error($errors, 'phone'); ?>
                </div>

                <div class="form-group <?= field_class($errors, 'email') ?>">
                    <label>Email <span class="req">*</span></label>
                    <input type="text" name="email" placeholder="juan@gmail.com"
                           value="<?= htmlspecialchars($old['email']) ?>">
                    <?php field_error($errors, 'email'); ?>
                </div>

                <div class="form-group <?= field_class($errors, 'address') ?>">
                    <label>Address <span class="req">*</span></label>
                    <input type="text" name="address" placeholder="Manila, Philippines"
                           value="<?= htmlspecialchars($old['address']) ?>">
                    <?php field_error($errors, 'address'); ?>
                </div>

                <div class="form-group <?= field_class($errors, 'linkedin') ?>">
                    <label>LinkedIn <span class="req">*</span></label>
                    <input type="text" name="linkedin" placeholder="linkedin.com/in/juan"
                           value="<?= htmlspecialchars($old['linkedin']) ?>">
                    <?php field_error($errors, 'linkedin'); ?>
                </div>

                <div class="form-group full <?= field_class($errors, 'objective') ?>">
                    <label>Career Objective <span class="req">*</span></label>
                    <textarea name="objective" placeholder="Write your career objective..."><?= htmlspecialchars($old['objective']) ?></textarea>
                    <?php field_error($errors, 'objective'); ?>
                </div>

                <div class="form-group <?= field_class($errors, 'skills') ?>">
                    <label>Key Skills <span class="req">*</span></label>
                    <textarea name="skills" placeholder="Programming&#10;Communication&#10;Problem Solving&#10;Teamwork"><?= htmlspecialchars($old['skills']) ?></textarea>
                    <?php field_error($errors, 'skills'); ?>
                </div>

                <div class="form-group <?= field_class($errors, 'certifications') ?>">
                    <label>Certifications <span class="req">*</span></label>
                    <textarea name="certifications" placeholder="NC II&#10;Microsoft Certification&#10;Web Development Certificate"><?= htmlspecialchars($old['certifications']) ?></textarea>
                    <?php field_error($errors, 'certifications'); ?>
                </div>

                <div class="form-group <?= field_class($errors, 'languages') ?>">
                    <label>Languages <span class="req">*</span></label>
                    <textarea name="languages" placeholder="English&#10;Filipino&#10;Japanese"><?= htmlspecialchars($old['languages']) ?></textarea>
                    <?php field_error($errors, 'languages'); ?>
                </div>

                <div class="form-group <?= field_class($errors, 'education') ?>">
                    <label>Education <span class="req">*</span></label>
                    <textarea name="education" placeholder="BS Computer Science - XYZ University - 2024"><?= htmlspecialchars($old['education']) ?></textarea>
                    <?php field_error($errors, 'education'); ?>
                </div>

                <div class="form-group full <?= field_class($errors, 'experience') ?>">
                    <label>Experience <span class="req">*</span></label>
                    <textarea name="experience" placeholder="IT Intern - ABC Company - 2024&#10;Assisted in website development&#10;Created reports"><?= htmlspecialchars($old['experience']) ?></textarea>
                    <?php field_error($errors, 'experience'); ?>
                </div>

                <div class="form-group full <?= field_class($errors, 'projects') ?>">
                    <label>Projects / Internships <span class="req">*</span></label>
                    <textarea name="projects" placeholder="Student Management System&#10;PHP, MySQL, HTML, CSS&#10;Created a system for managing student records."><?= htmlspecialchars($old['projects']) ?></textarea>
                    <?php field_error($errors, 'projects'); ?>
                </div>

            </div>

            <button type="submit" name="submit" value="1" class="save-btn">
                SAVE & GENERATE RESUME
            </button>

        </form>

    </div>

    <div class="saved">

        <h2>Saved Resumes</h2>

        <?php if (count($savedResumes) === 0): ?>

            <p>No saved resumes yet.</p>

        <?php else: ?>

            <?php foreach ($savedResumes as $item): ?>

                <div class="saved-item">

                    <div>
                        <div class="saved-name">
                            <?= htmlspecialchars($item["fullname"]) ?>
                        </div>

                        <div class="saved-job">
                            <?= htmlspecialchars($item["jobtitle"]) ?>
                        </div>
                    </div>

                    <div class="saved-actions">

                        <a class="view-btn" href="index.php?resume=<?= $item["id"] ?>">
                            VIEW
                        </a>

                        <a class="delete-btn" href="index.php?delete=<?= $item["id"] ?>"
                           onclick="return confirm('Delete this resume?')">
                            DELETE
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

<?php else: ?>

    <div class="actions">

        <button class="print-btn" onclick="window.print()">
            PRINT / SAVE AS PDF
        </button>

        <a href="index.php" class="new-btn">
            + CREATE NEW RESUME
        </a>

    </div>

    <div class="resume-container">

        <div class="resume-header">
            <div class="profile">
                <div class="profile-head"></div>
                <div class="profile-body"></div>
            </div>
            <div>
                <h1><?= htmlspecialchars($resume["fullname"]) ?></h1>
                <p><?= htmlspecialchars($resume["jobtitle"]) ?></p>
            </div>
        </div>

        <div class="resume-body">

            <div class="sidebar">

                <div class="side-section">
                    <div class="side-title">CONTACT</div>
                    <div class="side-item">☎ <?= htmlspecialchars($resume["phone"]) ?></div>
                    <div class="side-item">✉ <?= htmlspecialchars($resume["email"]) ?></div>
                    <div class="side-item">📍 <?= htmlspecialchars($resume["address"]) ?></div>
                    <div class="side-item">🔗 <?= htmlspecialchars($resume["linkedin"]) ?></div>
                </div>

                <div class="side-section">
                    <div class="side-title">CERTIFICATIONS</div>
                    <?php foreach (lines($resume["certifications"]) as $item): ?>
                        <?php if (trim($item) !== ""): ?>
                            <div class="side-item">• <?= htmlspecialchars($item) ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="side-section">
                    <div class="side-title">LANGUAGES</div>
                    <?php foreach (lines($resume["languages"]) as $item): ?>
                        <?php if (trim($item) !== ""): ?>
                            <div class="side-item">• <?= htmlspecialchars($item) ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

            </div>

            <div class="resume-main">

                <div class="resume-section">
                    <div class="resume-title">CAREER OBJECTIVE</div>
                    <div class="resume-text"><?= nl2br(htmlspecialchars($resume["objective"])) ?></div>
                </div>

                <div class="resume-section">
                    <div class="resume-title">KEY SKILLS</div>
                    <div class="resume-text">
                        <ul>
                            <?php foreach (lines($resume["skills"]) as $item): ?>
                                <?php if (trim($item) !== ""): ?>
                                    <li><?= htmlspecialchars($item) ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <div class="resume-section">
                    <div class="resume-title">EXPERIENCE</div>
                    <div class="resume-text">
                        <?php foreach (lines($resume["experience"]) as $item): ?>
                            <?php if (trim($item) !== ""): ?>
                                <div>• <?= htmlspecialchars($item) ?></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="resume-section">
                    <div class="resume-title">EDUCATION</div>
                    <div class="resume-text">
                        <?php foreach (lines($resume["education"]) as $item): ?>
                            <?php if (trim($item) !== ""): ?>
                                <div>• <?= htmlspecialchars($item) ?></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="resume-section">
                    <div class="resume-title">PROJECTS / INTERNSHIPS</div>
                    <div class="resume-text">
                        <?php foreach (lines($resume["projects"]) as $item): ?>
                            <?php if (trim($item) !== ""): ?>
                                <div>• <?= htmlspecialchars($item) ?></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

<?php endif; ?>

</body>
</html>