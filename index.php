<?php

if (isset($_POST['submit'])) {

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

    $data = "FULL NAME: $fullname
EMAIL: $email
PHONE: $phone
LOCATION: $location

CAREER OBJECTIVE:
$objective

KEY SKILLS:
$skills

CERTIFICATIONS:
$certifications

LANGUAGES:
$languages

EXPERIENCE:
$experience

EDUCATION:
$education

PROJECTS / INTERNSHIPS:
$projects

----------------------------------------
";

    file_put_contents("resume.txt", $data, FILE_APPEND);

    $message = "Information saved successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resume Information</title>
</head>

<body>

<h1>Resume Information Form</h1>

<?php
if (isset($message)) {
    echo "<p>$message</p>";
}
?>

<form method="POST">

    <h2>Personal Information</h2>

    Full Name:<br>
    <input type="text" name="fullname" required><br><br>

    Email:<br>
    <input type="email" name="email"><br><br>

    Phone:<br>
    <input type="text" name="phone"><br><br>

    Location:<br>
    <input type="text" name="location"><br><br>


    <h2>Career Objective</h2>

    <textarea name="objective" rows="5" cols="50"></textarea><br><br>


    <h2>Key Skills</h2>

    <textarea name="skills" rows="5" cols="50"></textarea><br><br>


    <h2>Certifications</h2>

    <textarea name="certifications" rows="5" cols="50"></textarea><br><br>


    <h2>Languages</h2>

    <textarea name="languages" rows="5" cols="50"></textarea><br><br>


    <h2>Experience</h2>

    <textarea name="experience" rows="5" cols="50"></textarea><br><br>


    <h2>Education</h2>

    <textarea name="education" rows="5" cols="50"></textarea><br><br>


    <h2>Projects / Internships</h2>

    <textarea name="projects" rows="5" cols="50"></textarea><br><br>


    <input type="submit" name="submit" value="Save Information">

</form>

</body>
</html>