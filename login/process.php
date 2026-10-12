<?php
session_start();
require '../config/db.php';
require 'functions.php';

/* ---------- Registration ---------- */
if (isset($_POST["registration"])) {
    $fname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $pswd  = $_POST["pswd"] ?? "";

    if (empty($fname) || empty($email) || empty($pswd)) { echo "Please fill in all fields."; exit; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))     { echo "Please enter a valid email address."; exit; }
    if (strlen($pswd) < 6)                              { echo "Password must be at least 6 characters."; exit; }

    $password = password_hash($pswd, PASSWORD_BCRYPT);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO faculty_staff (full_name, department, role_title) VALUES (:name, :dept, :role)");
        $stmt->execute(["name" => $fname, "dept" => "Unassigned", "role" => "Faculty"]);
        $facultyId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, faculty_staff_id) VALUES (:email, :password, :faculty_id)");
        $stmt->execute(["email" => $email, "password" => $password, "faculty_id" => $facultyId]);

        $pdo->commit();
        echo "success";
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        if ($e->getCode() === '23000') {
            echo "Email already exists!";
        } else {
            error_log('Registration failed: ' . $e->getMessage());
            echo "Registration failed. Please try again.";
        }
        exit;
    }
}

/* ---------- Login ---------- */
if (isset($_POST["login"])) {
    $username = trim($_POST['username'] ?? "");
    $password = $_POST['password'] ?? "";

    if (empty($username) || empty($password)) { echo "Please enter your email and password."; exit; }

    $stmt = $pdo->prepare("
        SELECT u.*, fs.id AS fs_id
        FROM users u
        LEFT JOIN faculty_staff fs ON fs.id = u.faculty_staff_id
        WHERE u.email = :email
    ");
    $stmt->execute(['email' => $username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password_hash'])) {
        if (empty($row['faculty_staff_id']) || empty($row['fs_id'])) {
            echo "Your account is no longer linked to a faculty profile. Please contact an administrator.";
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user_id']          = $row['id'];
        $_SESSION['email']            = $row['email'];
        $_SESSION['faculty_staff_id'] = $row['faculty_staff_id'];
        $_SESSION['loggedin']         = true;

        echo "success";
        exit;
    }

    echo "Invalid username or password!";
    exit;
}

/* ---------- Logout ---------- */
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}