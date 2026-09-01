<?php

if (isset($_POST['submit'])) {
$db = new PDO("sqlite:resume.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("
CREATE TABLE IF NOT EXISTS resumes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    fullname TEXT,
    jobtitle TEXT,
    phone TEXT,
    email TEXT,
    location TEXT,
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = $_POST["fullname"] ?? "";
    $jobtitle = $_POST["jobtitle"] ?? "";
    $phone = $_POST["phone"] ?? "";
    $email = $_POST["email"] ?? "";
    $location = $_POST["location"] ?? "";
    $linkedin = $_POST["linkedin"] ?? "";
    $objective = $_POST["objective"] ?? "";
    $skills = $_POST["skills"] ?? "";
    $certifications = $_POST["certifications"] ?? "";
    $languages = $_POST["languages"] ?? "";
    $experience = $_POST["experience"] ?? "";
    $education = $_POST["education"] ?? "";
    $projects = $_POST["projects"] ?? "";

    $stmt = $db->prepare("
        INSERT INTO resumes (
            fullname,
            jobtitle,
            phone,
            email,
            location,
            linkedin,
            objective,
            skills,
            certifications,
            languages,
            experience,
            education,
            projects
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $fullname,
        $jobtitle,
        $phone,
        $email,
        $location,
        $linkedin,
        $objective,
        $skills,
        $certifications,
        $languages,
        $experience,
        $education,
        $projects
    ]);

    header("Location: index.php?resume=" . $db->lastInsertId());
    exit;
}

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $objective = $_POST['objective'];
    $skills = $_POST['skills'];
    $certifications = $_POST['certifications'];
    $languages = $_POST['languages'];
    $experience = $_POST['experience'];
    $education = $_POST['education'];
    $projects = $_POST['projects'];
$resume = null;

    $data = "FULL NAME: $fullname
EMAIL: $email
PHONE: $phone
LOCATION: $location
if (isset($_GET["resume"])) {
    $stmt = $db->prepare("SELECT * FROM resumes WHERE id = ?");
    $stmt->execute([$_GET["resume"]]);
    $resume = $stmt->fetch(PDO::FETCH_ASSOC);
}

CAREER OBJECTIVE:
$objective
$savedResumes = $db->query("
    SELECT * FROM resumes
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

KEY SKILLS:
$skills
function lines($text) {
    return preg_split("/\r\n|\r|\n/", $text);
}

?>

CERTIFICATIONS:
$certifications
<!DOCTYPE html>
<html lang="en">

LANGUAGES:
$languages
<head>

EXPERIENCE:
$experience
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

EDUCATION:
$education
<title>Resume Builder</title>

PROJECTS / INTERNSHIPS:
$projects
<style>

----------------------------------------
";
* {
    box-sizing: border-box;
}

    file_put_contents("resume.txt", $data, FILE_APPEND);
body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg, #151329, #302844);
    color: #29243a;
}

    $message = "Information saved successfully!";
.builder {
    width: 900px;
    max-width: 95%;
    margin: 40px auto;
    background: #eee5ed;
    padding: 35px;
    border-radius: 8px;
    box-shadow: 0 20px 50px rgba(0,0,0,.4);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resume Information</title>
.builder h1 {
    text-align: center;
    color: #29223f;
    margin: 0 0 30px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full {
    grid-column: 1 / -1;
}

label {
    font-size: 12px;
    font-weight: bold;
    color: #342c4b;
    margin-bottom: 6px;
}

input,
textarea {
    width: 100%;
    border: 1px solid #b7a8b9;
    border-radius: 5px;
    padding: 11px;
    font-family: Arial, sans-serif;
    background: white;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

input:focus,
textarea:focus {
    outline: none;
    border-color: #d59a18;
    box-shadow: 0 0 0 2px rgba(213,154,24,.15);
}

.save-btn,
.print-btn,
.new-btn {
    border: none;
    padding: 13px;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
}

.save-btn {
    width: 100%;
    margin-top: 25px;
    background: #d99a17;
    color: white;
}

.save-btn:hover {
    background: #bd8110;
}

.actions {
    width: 900px;
    max-width: 95%;
    margin: 20px auto;
    display: flex;
    gap: 10px;
}

.actions .print-btn,
.actions .new-btn {
    flex: 1;
}

.print-btn {
    background: #d99a17;
    color: white;
}

.new-btn {
    background: #30294d;
    color: white;
}

.print-btn:hover {
    background: #bd8110;
}

.new-btn:hover {
    background: #201b37;
}

.resume-container {
    width: 900px;
    max-width: 95%;
    margin: 20px auto 40px;
    background: #eee5ed;
    border: 10px solid #d99a17;
    box-shadow: 0 20px 50px rgba(0,0,0,.5);
}

.resume-header {
    height: 170px;
    background: #27213f;
    color: white;
    display: flex;
    align-items: center;
    position: relative;
    padding-left: 190px;
}

.profile {
    position: absolute;
    left: 40px;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: #cdbbd2;
    overflow: hidden;
}

.profile-head {
    position: absolute;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #30294e;
    top: 20px;
    left: 36px;
}

.profile-body {
    position: absolute;
    width: 65px;
    height: 38px;
    border-radius: 50% 50% 0 0;
    background: #30294e;
    bottom: 7px;
    left: 22px;
}

.resume-header h1 {
    margin: 0;
    font-size: 27px;
    letter-spacing: .5px;
}

.resume-header p {
    margin-top: 8px;
    font-size: 11px;
    color: #d9cede;
}

.resume-body {
    display: grid;
    grid-template-columns: 190px 1fr;
}

.sidebar {
    background: #cdbbd2;
    padding: 25px 18px;
}

.side-section {
    margin-bottom: 28px;
}

.side-title {
    font-size: 10px;
    font-weight: bold;
    letter-spacing: 1px;
    border-bottom: 1px solid #8e8293;
    padding-bottom: 7px;
    margin-bottom: 10px;
}

.side-item {
    font-size: 8px;
    line-height: 1.6;
    margin-bottom: 7px;
    word-break: break-word;
}

.resume-main {
    padding: 25px;
    background: #eee5ed;
}

.resume-section {
    position: relative;
    padding-left: 30px;
    margin-bottom: 25px;
}

.resume-section::before {
    content: "";
    position: absolute;
    left: 0;
    top: 1px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #30294d;
    border: 2px solid #d9cbdc;
}

.resume-section::after {
    content: "";
    position: absolute;
    left: 6px;
    top: 17px;
    width: 1px;
    height: calc(100% + 8px);
    background: #807483;
}

.resume-section:last-child::after {
    display: none;
}

.resume-title {
    font-size: 11px;
    font-weight: 900;
    letter-spacing: 1px;
    margin-bottom: 8px;
    color: #302845;
}

.resume-text {
    font-size: 8px;
    line-height: 1.7;
    color: #4c4451;
}

.resume-text ul {
    margin: 5px 0;
    padding-left: 16px;
}

.resume-text li {
    margin-bottom: 5px;
}

.saved {
    width: 900px;
    max-width: 95%;
    margin: 40px auto;
    background: #eee5ed;
    padding: 25px;
    border-radius: 8px;
}

.saved h2 {
    margin-top: 0;
    color: #302845;
}

.saved-item {
    background: white;
    border-left: 5px solid #d99a17;
    padding: 15px;
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.saved-name {
    font-weight: bold;
    color: #302845;
}

.saved-job {
    font-size: 12px;
    color: #777;
    margin-top: 4px;
}

.saved-actions {
    display: flex;
    gap: 6px;
}

.view-btn,
.delete-btn {
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    color: white;
    font-size: 11px;
    font-weight: bold;
}

.view-btn {
    background: #30294d;
}

.delete-btn {
    background: #b73535;
}

@media print {

    body {
        background: white;
    }

    .builder,
    .actions,
    .saved {
        display: none;
    }

    .resume-container {
        margin: 0;
        width: 100%;
        max-width: none;
        box-shadow: none;
    }
}

@media(max-width:700px) {

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full {
        grid-column: auto;
    }

    .resume-body {
        grid-template-columns: 130px 1fr;
    }

    .resume-header {
        padding-left: 145px;
    }

    .resume-header h1 {
        font-size: 18px;
    }

    .profile {
        left: 25px;
        width: 85px;
        height: 85px;
    }

    .profile-head {
        width: 30px;
        height: 30px;
        left: 27px;
    }

    .profile-body {
        width: 52px;
        height: 30px;
        left: 17px;
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

<h1>Resume Information Form</h1>
<?php if (!$resume): ?>

<?php
if (isset($message)) {
    echo "<p>$message</p>";
}
?>
<div class="builder">

    <h1>Resume Builder</h1>

    <form method="POST">

        <div class="form-grid">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" placeholder="Juan Dela Cruz" required>
            </div>

            <div class="form-group">
                <label>Job Title</label>
                <input type="text" name="jobtitle" placeholder="Computer Science Graduate">
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" placeholder="+63 912 345 6789">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="juan@gmail.com">
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" placeholder="Manila, Philippines">
            </div>

            <div class="form-group">
                <label>LinkedIn</label>
                <input type="text" name="linkedin" placeholder="linkedin.com/in/juan">
            </div>

            <div class="form-group full">
                <label>Career Objective</label>
                <textarea name="objective" placeholder="Write your career objective..."></textarea>
            </div>

            <div class="form-group">
                <label>Key Skills</label>
                <textarea name="skills" placeholder="Programming&#10;Communication&#10;Problem Solving&#10;Teamwork"></textarea>
            </div>

            <div class="form-group">
                <label>Certifications</label>
                <textarea name="certifications" placeholder="NC II&#10;Microsoft Certification&#10;Web Development Certificate"></textarea>
            </div>

            <div class="form-group">
                <label>Languages</label>
                <textarea name="languages" placeholder="English&#10;Filipino&#10;Japanese"></textarea>
            </div>

            <div class="form-group">
                <label>Education</label>
                <textarea name="education" placeholder="BS Computer Science - XYZ University - 2024"></textarea>
            </div>

            <div class="form-group full">
                <label>Experience</label>
                <textarea name="experience" placeholder="IT Intern - ABC Company - 2024&#10;Assisted in website development&#10;Created reports"></textarea>
            </div>

            <div class="form-group full">
                <label>Projects / Internships</label>
                <textarea name="projects" placeholder="Student Management System&#10;PHP, MySQL, HTML, CSS&#10;Created a system for managing student records."></textarea>
            </div>

        </div>

        <button type="submit" class="save-btn">
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

                    <a
                        class="view-btn"
                        href="index.php?resume=<?= $item["id"] ?>"
                    >
                        VIEW
                    </a>

                    <a
                        class="delete-btn"
                        href="index.php?delete=<?= $item["id"] ?>"
                        onclick="return confirm('Delete this resume?')"
                    >
                        DELETE
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<?php else: ?>

<div class="actions">

    <button
        class="print-btn"
        onclick="window.print()"
    >
        PRINT / SAVE AS PDF
    </button>

    <a
        href="index.php"
        class="new-btn"
    >
        + CREATE NEW RESUME
    </a>

</div>

<div class="resume-container">

<form method="POST">
    <div class="resume-header">

    <h2>Personal Information</h2>
        <div class="profile">
            <div class="profile-head"></div>
            <div class="profile-body"></div>
        </div>

    Full Name:<br>
    <input type="text" name="fullname" required><br><br>
        <div>
            <h1>
                <?= htmlspecialchars($resume["fullname"]) ?>
            </h1>

    Email:<br>
    <input type="email" name="email"><br><br>
            <p>
                <?= htmlspecialchars($resume["jobtitle"]) ?>
            </p>
        </div>

    Phone:<br>
    <input type="text" name="phone"><br><br>
    </div>

    Location:<br>
    <input type="text" name="location"><br><br>
    <div class="resume-body">

        <div class="sidebar">

    <h2>Career Objective</h2>
            <div class="side-section">

    <textarea name="objective" rows="5" cols="50"></textarea><br><br>
                <div class="side-title">
                    CONTACT
                </div>

                <div class="side-item">
                    ☎ <?= htmlspecialchars($resume["phone"]) ?>
                </div>

    <h2>Key Skills</h2>
                <div class="side-item">
                    ✉ <?= htmlspecialchars($resume["email"]) ?>
                </div>

    <textarea name="skills" rows="5" cols="50"></textarea><br><br>
                <div class="side-item">
                    📍 <?= htmlspecialchars($resume["location"]) ?>
                </div>

                <div class="side-item">
                    🔗 <?= htmlspecialchars($resume["linkedin"]) ?>
                </div>

    <h2>Certifications</h2>
            </div>

    <textarea name="certifications" rows="5" cols="50"></textarea><br><br>
            <div class="side-section">

                <div class="side-title">
                    CERTIFICATIONS
                </div>

    <h2>Languages</h2>
                <?php foreach (lines($resume["certifications"]) as $item): ?>

    <textarea name="languages" rows="5" cols="50"></textarea><br><br>
                    <?php if (trim($item) !== ""): ?>

                        <div class="side-item">
                            • <?= htmlspecialchars($item) ?>
                        </div>

    <h2>Experience</h2>
                    <?php endif; ?>

    <textarea name="experience" rows="5" cols="50"></textarea><br><br>
                <?php endforeach; ?>

            </div>

    <h2>Education</h2>
            <div class="side-section">

    <textarea name="education" rows="5" cols="50"></textarea><br><br>
                <div class="side-title">
                    LANGUAGES
                </div>

                <?php foreach (lines($resume["languages"]) as $item): ?>

    <h2>Projects / Internships</h2>
                    <?php if (trim($item) !== ""): ?>

    <textarea name="projects" rows="5" cols="50"></textarea><br><br>
                        <div class="side-item">
                            • <?= htmlspecialchars($item) ?>
                        </div>

                    <?php endif; ?>

    <input type="submit" name="submit" value="Save Information">
                <?php endforeach; ?>

</form>
            </div>

        </div>

        <div class="resume-main">

            <div class="resume-section">

                <div class="resume-title">
                    CAREER OBJECTIVE
                </div>

                <div class="resume-text">
                    <?= nl2br(htmlspecialchars($resume["objective"])) ?>
                </div>

            </div>

            <div class="resume-section">

                <div class="resume-title">
                    KEY SKILLS
                </div>

                <div class="resume-text">

                    <ul>

                        <?php foreach (lines($resume["skills"]) as $item): ?>

                            <?php if (trim($item) !== ""): ?>

                                <li>
                                    <?= htmlspecialchars($item) ?>
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

                    <?php foreach (lines($resume["experience"]) as $item): ?>

                        <?php if (trim($item) !== ""): ?>

                            <div>
                                • <?= htmlspecialchars($item) ?>
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

                    <?php foreach (lines($resume["education"]) as $item): ?>

                        <?php if (trim($item) !== ""): ?>

                            <div>
                                • <?= htmlspecialchars($item) ?>
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

                    <?php foreach (lines($resume["projects"]) as $item): ?>

                        <?php if (trim($item) !== ""): ?>

                            <div>
                                • <?= htmlspecialchars($item) ?>
                            </div>

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

</html>
