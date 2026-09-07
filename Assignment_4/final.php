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
    photo TEXT,
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

/* Add missing columns if the database already exists */
$columns = $db->query("PRAGMA table_info(resumes)")
              ->fetchAll(PDO::FETCH_ASSOC);

$columnNames = array_column($columns, "name");

$requiredColumns = [
    "address" => "TEXT",
    "linkedin" => "TEXT",
    "photo" => "TEXT",
    "objective" => "TEXT",
    "skills" => "TEXT",
    "certifications" => "TEXT",
    "languages" => "TEXT",
    "experience" => "TEXT",
    "education" => "TEXT",
    "projects" => "TEXT"
];

foreach ($requiredColumns as $column => $type) {
    if (!in_array($column, $columnNames)) {
        $db->exec("ALTER TABLE resumes ADD COLUMN $column $type");
    }
}

/* Convert textarea lines into array */
function lines($text)
{
    return preg_split("/\r\n|\r|\n/", (string)$text);
}

/* Display error message */
function field_error($errors, $field)
{
    if (!empty($errors[$field])) {
        echo '<span class="field-error">'
            . htmlspecialchars($errors[$field])
            . '</span>';
    }
}

/* Add error class */
function field_class($errors, $field)
{
    return !empty($errors[$field]) ? "has-error" : "";
}

$errors = [];

/* Old form values */
$old = [
    "fullname" => "",
    "jobtitle" => "",
    "phone" => "",
    "email" => "",
    "address" => "",
    "linkedin" => "",
    "objective" => "",
    "skills" => "",
    "certifications" => "",
    "languages" => "",
    "experience" => "",
    "education" => "",
    "projects" => ""
];

/* DELETE RESUME */
if (isset($_GET["delete"])) {

    $id = filter_input(INPUT_GET, "delete", FILTER_VALIDATE_INT);

    if ($id) {

        $stmt = $db->prepare(
            "SELECT photo FROM resumes WHERE id = ?"
        );

        $stmt->execute([$id]);

        $deletedResume = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($deletedResume && !empty($deletedResume["photo"])) {

            if (file_exists($deletedResume["photo"])) {
                unlink($deletedResume["photo"]);
            }
        }

        $stmt = $db->prepare(
            "DELETE FROM resumes WHERE id = ?"
        );

        $stmt->execute([$id]);
    }

    header("Location: final.php");
    exit;
}

/* SUBMIT FORM */
if (
    $_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["submit"])
) {

    /* Get old values */
    foreach ($old as $field => $value) {
        $old[$field] = trim($_POST[$field] ?? "");
    }

    /* FULL NAME */
    if ($old["fullname"] === "") {

        $errors["fullname"] =
            "Full name is required.";

    } elseif (
        !preg_match(
            "/^[A-Za-z\s.'-]+$/",
            $old["fullname"]
        )
    ) {

        $errors["fullname"] =
            "Full name must contain letters only.";
    }

    /* JOB TITLE */
    if ($old["jobtitle"] === "") {

        $errors["jobtitle"] =
            "Job title is required.";

    } elseif (
        !preg_match(
            "/^[A-Za-z0-9\s.,&()'-]+$/",
            $old["jobtitle"]
        )
    ) {

        $errors["jobtitle"] =
            "Job title contains invalid characters.";
    }

    /* PHONE */
    if ($old["phone"] === "") {

        $errors["phone"] =
            "Phone number is required.";

    } elseif (
        !preg_match(
            "/^[0-9+\s()-]{7,20}$/",
            $old["phone"]
        )
    ) {

        $errors["phone"] =
            "Enter a valid phone number.";
    }

    /* EMAIL */
    if ($old["email"] === "") {

        $errors["email"] =
            "Email address is required.";

    } elseif (
        !filter_var(
            $old["email"],
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $errors["email"] =
            "Enter a valid email address.";
    }

    /* ADDRESS */
    if ($old["address"] === "") {

        $errors["address"] =
            "Address is required.";
    }

    /* LINKEDIN */
    if ($old["linkedin"] === "") {

        $errors["linkedin"] =
            "LinkedIn profile is required.";

    } elseif (
        !preg_match(
            "/^(https?:\/\/)?(www\.)?linkedin\.com\/in\/[A-Za-z0-9_-]+\/?$/i",
            $old["linkedin"]
        )
    ) {

        $errors["linkedin"] =
            "Enter a valid LinkedIn profile.";
    }

    /* OBJECTIVE */
    if ($old["objective"] === "") {

        $errors["objective"] =
            "Career objective is required.";
    }

    /* SKILLS */
    if ($old["skills"] === "") {

        $errors["skills"] =
            "At least one skill is required.";
    }

    /* CERTIFICATIONS */
    if ($old["certifications"] === "") {

        $errors["certifications"] =
            "At least one certification is required.";
    }

    /* LANGUAGES */
    if ($old["languages"] === "") {

        $errors["languages"] =
            "At least one language is required.";
    }

    /* EXPERIENCE */
    if ($old["experience"] === "") {

        $errors["experience"] =
            "Work experience is required.";
    }

    /* EDUCATION */
    if ($old["education"] === "") {

        $errors["education"] =
            "Education is required.";
    }

    /* PROJECTS */
    if ($old["projects"] === "") {

        $errors["projects"] =
            "At least one project is required.";
    }

    /* PROFILE PICTURE */
    if (
        !isset($_FILES["photo"])
        || $_FILES["photo"]["error"] === UPLOAD_ERR_NO_FILE
    ) {

        $errors["photo"] =
            "Profile picture is required.";

    } elseif (
        $_FILES["photo"]["error"] !== UPLOAD_ERR_OK
    ) {

        $errors["photo"] =
            "There was an error uploading the profile picture.";

    } else {

        $allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];

        $fileType = mime_content_type(
            $_FILES["photo"]["tmp_name"]
        );

        if (!in_array($fileType, $allowedTypes)) {

            $errors["photo"] =
                "Profile picture must be JPG, PNG, or WEBP.";

        } elseif (
            $_FILES["photo"]["size"] > 2 * 1024 * 1024
        ) {

            $errors["photo"] =
                "Profile picture must not exceed 2MB.";
        }
    }

    /* SAVE IMAGE */
    if (empty($errors)) {

        $uploadDir = "uploads/";

        if (!is_dir($uploadDir)) {

            mkdir(
                $uploadDir,
                0777,
                true
            );
        }

        $extension = strtolower(
            pathinfo(
                $_FILES["photo"]["name"],
                PATHINFO_EXTENSION
            )
        );

        $photoName =
            uniqid("profile_", true)
            . "."
            . $extension;

        $photoPath =
            $uploadDir
            . $photoName;

        if (
            !move_uploaded_file(
                $_FILES["photo"]["tmp_name"],
                $photoPath
            )
        ) {

            $errors["photo"] =
                "Failed to save the profile picture.";
        }
    }

    /* INSERT INTO DATABASE */
    if (empty($errors)) {

        $stmt = $db->prepare("
            INSERT INTO resumes (
                fullname,
                jobtitle,
                phone,
                email,
                address,
                linkedin,
                photo,
                objective,
                skills,
                certifications,
                languages,
                experience,
                education,
                projects
            )
            VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");

        $stmt->execute([
            $old["fullname"],
            $old["jobtitle"],
            $old["phone"],
            $old["email"],
            $old["address"],
            $old["linkedin"],
            $photoPath,
            $old["objective"],
            $old["skills"],
            $old["certifications"],
            $old["languages"],
            $old["experience"],
            $old["education"],
            $old["projects"]
        ]);

        header(
            "Location: final.php?resume="
            . $db->lastInsertId()
        );

        exit;
    }
}

/* GET RESUME */
$resume = null;

if (isset($_GET["resume"])) {

    $id = filter_input(
        INPUT_GET,
        "resume",
        FILTER_VALIDATE_INT
    );

    if ($id) {

        $stmt = $db->prepare(
            "SELECT * FROM resumes WHERE id = ?"
        );

        $stmt->execute([$id]);

        $resume =
            $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

/* GET ALL SAVED RESUMES */
$savedResumes =
    $db->query("
        SELECT *
        FROM resumes
        ORDER BY id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Professional Resume Builder</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f1f5f9;
    color: #1e293b;
}

.topbar {
    background: #1e293b;
    color: white;
    padding: 28px 20px;
    text-align: center;
}

.topbar h1 {
    margin: 0;
    font-size: 28px;
}

.topbar p {
    margin: 7px 0 0;
    color: #cbd5e1;
    font-size: 14px;
}

.container {
    width: 900px;
    max-width: 94%;
    margin: 30px auto;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 5px 20px rgba(0,0,0,.07);
    margin-bottom: 25px;
}

.card-title {
    margin: 0;
    font-size: 22px;
}

.card-subtitle {
    margin: 7px 0 25px;
    color: #64748b;
    font-size: 14px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full {
    grid-column: 1 / -1;
}

label {
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 7px;
    color: #334155;
}

.req {
    color: #dc2626;
}

input,
textarea {
    width: 100%;
    padding: 11px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #f8fafc;
    font-family: inherit;
    font-size: 14px;
    transition:
        border-color .2s,
        box-shadow .2s;
}

textarea {
    min-height: 105px;
    resize: vertical;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: #2563eb;
    background: white;
    box-shadow:
        0 0 0 3px rgba(37,99,235,.10);
}

input[type="file"] {
    padding: 10px;
    cursor: pointer;
    background: #f8fafc;
}

.has-error input,
.has-error textarea {
    border-color: #dc2626;
    background: #fff7f7;
}

.field-error {
    display: block;
    color: #dc2626;
    font-size: 12px;
    font-weight: bold;
    margin-top: 6px;
}

.valid-field {
    border-color: #16a34a !important;
    background: #f0fdf4 !important;
}

.invalid-field {
    border-color: #dc2626 !important;
    background: #fff7f7 !important;
}

.form-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    padding: 13px 15px;
    border-radius: 6px;
    margin-bottom: 22px;
    font-size: 13px;
}

.save-btn {
    width: 100%;
    margin-top: 25px;
    padding: 14px;
    border: none;
    border-radius: 7px;
    background: #2563eb;
    color: white;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: .2s;
}

.save-btn:hover {
    background: #1d4ed8;
}

.saved-title {
    margin: 0 0 18px;
    font-size: 19px;
}

.saved-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    padding: 15px;
    margin-bottom: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 7px;
}

.saved-name {
    font-weight: bold;
}

.saved-job {
    margin-top: 4px;
    font-size: 12px;
    color: #64748b;
}

.saved-actions {
    display: flex;
    gap: 7px;
}

.view-btn,
.delete-btn {
    padding: 8px 13px;
    border-radius: 5px;
    color: white;
    text-decoration: none;
    font-size: 11px;
    font-weight: bold;
}

.view-btn {
    background: #1e293b;
}

.delete-btn {
    background: #dc2626;
}

.view-btn:hover {
    background: #0f172a;
}

.delete-btn:hover {
    background: #b91c1c;
}

.action-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.print-btn,
.new-btn {
    flex: 1;
    padding: 12px;
    text-align: center;
    text-decoration: none;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    font-size: 13px;
    cursor: pointer;
}

.print-btn {
    background: #2563eb;
    color: white;
}

.new-btn {
    background: #1e293b;
    color: white;
}

.resume {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,.08);
}

.resume-header {
    background: #1e293b;
    color: white;
    padding: 30px 40px;
    display: flex;
    align-items: center;
    gap: 25px;
}

.profile-picture {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    background: #e2e8f0;
    border: 4px solid white;
    flex-shrink: 0;
}

.profile-picture img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.header-info {
    display: flex;
    flex-direction: column;
}

.resume-header h1 {
    margin: 0;
    font-size: 30px;
}

.resume-header p {
    margin: 8px 0 0;
    color: #cbd5e1;
    font-size: 14px;
}

.resume-body {
    display: grid;
    grid-template-columns: 230px 1fr;
}

.sidebar {
    background: #f8fafc;
    padding: 28px 22px;
    border-right: 1px solid #e2e8f0;
}

.main-content {
    padding: 30px;
}

.side-section {
    margin-bottom: 27px;
}

.side-title,
.resume-title {
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 1px;
    color: #1e293b;
    border-bottom: 2px solid #2563eb;
    padding-bottom: 7px;
    margin-bottom: 12px;
}

.side-item {
    font-size: 12px;
    line-height: 1.6;
    margin-bottom: 7px;
    color: #475569;
    word-break: break-word;
}

.resume-section {
    margin-bottom: 25px;
}

.resume-text {
    font-size: 13px;
    line-height: 1.7;
    color: #475569;
}

.resume-text ul {
    margin: 5px 0;
    padding-left: 20px;
}

.resume-text li {
    margin-bottom: 5px;
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

    .sidebar {
        border-right: none;
        border-bottom: 1px solid #e2e8f0;
    }

    .action-bar {
        flex-direction: column;
    }

    .saved-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .saved-actions {
        width: 100%;
    }

    .view-btn,
    .delete-btn {
        flex: 1;
        text-align: center;
    }

    .resume-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

}

@media print {

    body {
        background: white;
    }

    .topbar,
    .action-bar,
    .saved {
        display: none;
    }

    .container {
        width: 100%;
        max-width: none;
        margin: 0;
    }

    .resume {
        box-shadow: none;
        border-radius: 0;
    }

}

</style>

</head>

<body>

<div class="topbar">

    <h1>Resume Builder</h1>

    <p>
        Create a professional resume
    </p>

</div>

<div class="container">

<?php if (!$resume): ?>

<div class="card">

    <h2 class="card-title">
        Create Your Resume
    </h2>

    <p class="card-subtitle">
        Please complete all required fields.
    </p>

    <?php if (!empty($errors)): ?>

        <div class="form-alert">
            Please fix the errors below
            before submitting the form.
        </div>

    <?php endif; ?>

    <form
        method="POST"
        enctype="multipart/form-data"
        novalidate
    >

        <div class="form-grid">

            <div class="form-group
                <?= field_class($errors, 'photo') ?>">

                <label>
                    Profile Picture
                    <span class="req">*</span>
                </label>

                <input
                    type="file"
                    name="photo"
                    accept="image/jpeg,image/png,image/webp"
                >

                <?php
                field_error($errors, 'photo');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'fullname') ?>">

                <label>
                    Full Name
                    <span class="req">*</span>
                </label>

                <input
                    type="text"
                    name="fullname"
                    placeholder="Jessa Maureen Maniacop"
                    value="<?= htmlspecialchars(
                        $old['fullname']
                    ) ?>"
                >

                <?php
                field_error($errors, 'fullname');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'jobtitle') ?>">

                <label>
                    Job Title
                    <span class="req">*</span>
                </label>

                <input
                    type="text"
                    name="jobtitle"
                    placeholder="IT Graduate"
                    value="<?= htmlspecialchars(
                        $old['jobtitle']
                    ) ?>"
                >

                <?php
                field_error($errors, 'jobtitle');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'phone') ?>">

                <label>
                    Phone
                    <span class="req">*</span>
                </label>

                <input
                    type="text"
                    name="phone"
                    placeholder="+63 912 345 6789"
                    value="<?= htmlspecialchars(
                        $old['phone']
                    ) ?>"
                >

                <?php
                field_error($errors, 'phone');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'email') ?>">

                <label>
                    Email
                    <span class="req">*</span>
                </label>

                <input
                    type="text"
                    name="email"
                    placeholder="jessa@gmail.com"
                    value="<?= htmlspecialchars(
                        $old['email']
                    ) ?>"
                >

                <?php
                field_error($errors, 'email');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'address') ?>">

                <label>
                    Address
                    <span class="req">*</span>
                </label>

                <input
                    type="text"
                    name="address"
                    placeholder="Dagupan City, Philippines"
                    value="<?= htmlspecialchars(
                        $old['address']
                    ) ?>"
                >

                <?php
                field_error($errors, 'address');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'linkedin') ?>">

                <label>
                    LinkedIn
                    <span class="req">*</span>
                </label>

                <input
                    type="text"
                    name="linkedin"
                    placeholder="linkedin.com/in/juan"
                    value="<?= htmlspecialchars(
                        $old['linkedin']
                    ) ?>"
                >

                <?php
                field_error($errors, 'linkedin');
                ?>

            </div>

            <div class="form-group full
                <?= field_class($errors, 'objective') ?>">

                <label>
                    Career Objective
                    <span class="req">*</span>
                </label>

                <textarea
                    name="objective"
                    placeholder="To obtain an entry-level position in the Information Technology field where I can apply my knowledge and skills..."
                ><?= htmlspecialchars(
                    $old['objective']
                ) ?></textarea>

                <?php
                field_error($errors, 'objective');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'skills') ?>">

                <label>
                    Key Skills
                    <span class="req">*</span>
                </label>

                <textarea
                    name="skills"
                    placeholder="Programming
Web Development
Problem Solving
Communication"
                ><?= htmlspecialchars(
                    $old['skills']
                ) ?></textarea>

                <?php
                field_error($errors, 'skills');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'certifications') ?>">

                <label>
                    Certifications
                    <span class="req">*</span>
                </label>

                <textarea
                    name="certifications"
                    placeholder="NC II
Microsoft Certification
Web Development Certificate"
                ><?= htmlspecialchars(
                    $old['certifications']
                ) ?></textarea>

                <?php
                field_error($errors, 'certifications');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'languages') ?>">

                <label>
                    Languages
                    <span class="req">*</span>
                </label>

                <textarea
                    name="languages"
                    placeholder="English
Filipino"
                ><?= htmlspecialchars(
                    $old['languages']
                ) ?></textarea>

                <?php
                field_error($errors, 'languages');
                ?>

            </div>

            <div class="form-group
                <?= field_class($errors, 'education') ?>">

                <label>
                    Education
                    <span class="req">*</span>
                </label>

                <textarea
                    name="education"
                    placeholder="BS Information Technology
Pangasinan State University
2026"
                ><?= htmlspecialchars(
                    $old['education']
                ) ?></textarea>

                <?php
                field_error($errors, 'education');
                ?>

            </div>

            <div class="form-group full
                <?= field_class($errors, 'experience') ?>">

                <label>
                    Experience
                    <span class="req">*</span>
                </label>

                <textarea
                    name="experience"
                    placeholder="IT Intern - ABC Company - 2026
Assisted in website development
Created reports"
                ><?= htmlspecialchars(
                    $old['experience']
                ) ?></textarea>

                <?php
                field_error($errors, 'experience');
                ?>

            </div>

            <div class="form-group full
                <?= field_class($errors, 'projects') ?>">

                <label>
                    Projects / Internships
                    <span class="req">*</span>
                </label>

                <textarea
                    name="projects"
                    placeholder="Student Management System
PHP, MySQL, HTML, CSS
Created a system for managing student records."
                ><?= htmlspecialchars(
                    $old['projects']
                ) ?></textarea>

                <?php
                field_error($errors, 'projects');
                ?>

            </div>

        </div>

        <button
            type="submit"
            name="submit"
            value="1"
            class="save-btn"
        >
            SAVE & GENERATE RESUME
        </button>

    </form>

</div>

<div class="card saved">

    <h2 class="saved-title">
        Saved Resumes
    </h2>

    <?php if (count($savedResumes) === 0): ?>

        <p>
            No saved resumes yet.
        </p>

    <?php else: ?>

        <?php foreach ($savedResumes as $item): ?>

            <div class="saved-item">

                <div>

                    <div class="saved-name">
                        <?= htmlspecialchars(
                            $item["fullname"]
                        ) ?>
                    </div>

                    <div class="saved-job">
                        <?= htmlspecialchars(
                            $item["jobtitle"]
                        ) ?>
                    </div>

                </div>

                <div class="saved-actions">

                    <a
                        class="view-btn"
                        href="final.php?resume=<?= $item["id"] ?>"
                    >
                        VIEW
                    </a>

                    <a
                        class="delete-btn"
                        href="final.php?delete=<?= $item["id"] ?>"
                        onclick="return confirm('Delete this resume?');"
                    >
                        DELETE
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php else: ?>

<div class="action-bar">

    <button
        class="print-btn"
        onclick="window.print()"
    >
        PRINT / SAVE AS PDF
    </button>

    <a
        href="final.php"
        class="new-btn"
    >
        + CREATE NEW RESUME
    </a>

</div>

<div class="resume">

    <div class="resume-header">

        <div class="profile-picture">

            <?php if (
                !empty($resume["photo"])
                && file_exists($resume["photo"])
            ): ?>

                <img
                    src="<?= htmlspecialchars(
                        $resume["photo"]
                    ) ?>"
                    alt="Profile Picture"
                >

            <?php else: ?>

                <div
                    style="
                    width:100%;
                    height:100%;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    color:#64748b;
                    font-size:12px;
                    "
                >
                    No Photo
                </div>

            <?php endif; ?>

        </div>

        <div class="header-info">

            <h1>
                <?= htmlspecialchars(
                    $resume["fullname"]
                ) ?>
            </h1>

            <p>
                <?= htmlspecialchars(
                    $resume["jobtitle"]
                ) ?>
            </p>

        </div>

    </div>

    <div class="resume-body">

        <div class="sidebar">

            <div class="side-section">

                <div class="side-title">
                    CONTACT
                </div>

                <div class="side-item">
                    ☎
                    <?= htmlspecialchars(
                        $resume["phone"]
                    ) ?>
                </div>

                <div class="side-item">
                    ✉
                    <?= htmlspecialchars(
                        $resume["email"]
                    ) ?>
                </div>

                <div class="side-item">
                    📍
                    <?= htmlspecialchars(
                        $resume["address"]
                    ) ?>
                </div>

                <div class="side-item">
                    🔗
                    <?= htmlspecialchars(
                        $resume["linkedin"]
                    ) ?>
                </div>

            </div>

            <div class="side-section">

                <div class="side-title">
                    CERTIFICATIONS
                </div>

                <?php foreach (
                    lines($resume["certifications"])
                    as $item
                ): ?>

                    <?php if (trim($item) !== ""): ?>

                        <div class="side-item">
                            •
                            <?= htmlspecialchars($item) ?>
                        </div>

                    <?php endif; ?>

                <?php endforeach; ?>

            </div>

            <div class="side-section">

                <div class="side-title">
                    LANGUAGES
                </div>

                <?php foreach (
                    lines($resume["languages"])
                    as $item
                ): ?>

                    <?php if (trim($item) !== ""): ?>

                        <div class="side-item">
                            •
                            <?= htmlspecialchars($item) ?>
                        </div>

                    <?php endif; ?>

                <?php endforeach; ?>

            </div>

        </div>

        <div class="main-content">

            <div class="resume-section">

                <div class="resume-title">
                    CAREER OBJECTIVE
                </div>

                <div class="resume-text">
                    <?= nl2br(
                        htmlspecialchars(
                            $resume["objective"]
                        )
                    ) ?>
                </div>

            </div>

            <div class="resume-section">

                <div class="resume-title">
                    KEY SKILLS
                </div>

                <div class="resume-text">

                    <ul>

                        <?php foreach (
                            lines($resume["skills"])
                            as $item
                        ): ?>

                            <?php if (trim($item) !== ""): ?>

                                <li>
                                    <?= htmlspecialchars(
                                        $item
                                    ) ?>
                                </li>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </ul>

                </div>

            </div>

            <div class="resume-section">

                <div class="resume-title">
                    EXPERIENCE
                </div>

                <div class="resume-text">

                    <?php foreach (
                        lines($resume["experience"])
                        as $item
                    ): ?>

                        <?php if (trim($item) !== ""): ?>

                            <div>
                                •
                                <?= htmlspecialchars(
                                    $item
                                ) ?>
                            </div>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>

            </div>

            <div class="resume-section">

                <div class="resume-title">
                    EDUCATION
                </div>

                <div class="resume-text">

                    <?php foreach (
                        lines($resume["education"])
                        as $item
                    ): ?>

                        <?php if (trim($item) !== ""): ?>

                            <div>
                                •
                                <?= htmlspecialchars(
                                    $item
                                ) ?>
                            </div>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>

            </div>

            <div class="resume-section">

                <div class="resume-title">
                    PROJECTS / INTERNSHIPS
                </div>

                <div class="resume-text">

                    <?php foreach (
                        lines($resume["projects"])
                        as $item
                    ): ?>

                        <?php if (trim($item) !== ""): ?>

                            <div>
                                •
                                <?= htmlspecialchars(
                                    $item
                                ) ?>
                            </div>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php endif; ?>

</div>

<script>

const form = document.querySelector("form");

if (form) {

    const fields = {

        fullname: function(value) {

            if (value.trim() === "") {
                return "Full name is required.";
            }

            if (!/^[A-Za-z\s.'-]+$/.test(value)) {
                return "Full name must contain letters only.";
            }

            return "";
        },

        jobtitle: function(value) {

            if (value.trim() === "") {
                return "Job title is required.";
            }

            if (!/^[A-Za-z0-9\s.,&()'-]+$/.test(value)) {
                return "Job title contains invalid characters.";
            }

            return "";
        },

        phone: function(value) {

            if (value.trim() === "") {
                return "Phone number is required.";
            }

            if (!/^[0-9+\s()-]{7,20}$/.test(value)) {
                return "Enter a valid phone number.";
            }

            return "";
        },

        email: function(value) {

            if (value.trim() === "") {
                return "Email address is required.";
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                return "Enter a valid email address.";
            }

            return "";
        },

        address: function(value) {

            if (value.trim() === "") {
                return "Address is required.";
            }

            return "";
        },

        linkedin: function(value) {

            if (value.trim() === "") {
                return "LinkedIn profile is required.";
            }

            if (
                !/^(https?:\/\/)?(www\.)?linkedin\.com\/in\/[A-Za-z0-9_-]+\/?$/i.test(
                    value.trim()
                )
            ) {
                return "Enter a valid LinkedIn profile.";
            }

            return "";
        },

        objective: function(value) {

            if (value.trim() === "") {
                return "Career objective is required.";
            }

            return "";
        },

        skills: function(value) {

            if (value.trim() === "") {
                return "At least one skill is required.";
            }

            return "";
        },

        certifications: function(value) {

            if (value.trim() === "") {
                return "At least one certification is required.";
            }

            return "";
        },

        languages: function(value) {

            if (value.trim() === "") {
                return "At least one language is required.";
            }

            return "";
        },

        experience: function(value) {

            if (value.trim() === "") {
                return "Work experience is required.";
            }

            return "";
        },

        education: function(value) {

            if (value.trim() === "") {
                return "Education is required.";
            }

            return "";
        },

        projects: function(value) {

            if (value.trim() === "") {
                return "At least one project is required.";
            }

            return "";
        }

    };

    Object.keys(fields).forEach(function(name) {

        const input =
            form.querySelector(
                '[name="' + name + '"]'
            );

        if (!input) {
            return;
        }

        input.addEventListener(
            "input",
            function() {
                validateField(input);
            }
        );

        input.addEventListener(
            "blur",
            function() {
                validateField(input);
            }
        );

    });

    function validateField(input) {

        const message =
            fields[input.name](
                input.value
            );

        const group =
            input.closest(".form-group");

        let error =
            group.querySelector(".live-error");

        if (!error) {

            error =
                document.createElement("span");

            error.className =
                "field-error live-error";

            group.appendChild(error);
        }

        if (message !== "") {

            input.classList.remove(
                "valid-field"
            );

            input.classList.add(
                "invalid-field"
            );

            error.textContent =
                message;

            error.style.display =
                "block";

        } else {

            input.classList.remove(
                "invalid-field"
            );

            input.classList.add(
                "valid-field"
            );

            error.textContent =
                "";

            error.style.display =
                "none";
        }

    }

    form.addEventListener(
        "submit",
        function(event) {

            let hasError = false;

            Object.keys(fields).forEach(
                function(name) {

                    const input =
                        form.querySelector(
                            '[name="' +
                            name +
                            '"]'
                        );

                    if (!input) {
                        return;
                    }

                    validateField(input);

                    if (
                        fields[name](
                            input.value
                        ) !== ""
                    ) {
                        hasError = true;
                    }

                }
            );

            if (hasError) {
                event.preventDefault();
            }

        }
    );

}

</script>

</body>

</html>
