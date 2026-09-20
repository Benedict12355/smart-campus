<?php
session_start();
require '../config/db.php';
require 'functions.php';

// Registration process
if (isset($_POST["registration"])) {

    $fname = trim($_POST["firstname"] ?? $_POST["fullname"] ?? "");
    $email = trim($_POST["email"]);
    $password = password_hash($_POST['pswd'], PASSWORD_BCRYPT);

    if (checkEmailIfExist($email, $pdo)) {
        echo "Email already exists!";
    } else {
        try {
            // Step 1: create the faculty_staff profile
            $stmt = $pdo->prepare("INSERT INTO faculty_staff (full_name, department, role_title) VALUES (:name, :dept, :role)");
            $stmt->execute([
                "name" => $fname,
                "dept" => "Unassigned",
                "role" => "Faculty",
            ]);
            $facultyId = $pdo->lastInsertId();

            // Step 2: create the login account, linked to that profile
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, faculty_staff_id) VALUES (:email, :password, :faculty_id)");
            $stmt->execute([
                "email" => $email,
                "password" => $password,
                "faculty_id" => $facultyId,
            ]);

            header("Location: dashboard.php");
            exit;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Login process
if (isset($_POST["login"])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $username]);
    $row = $stmt->fetch();

    if ($row && password_verify($password, $row['password_hash'])) {

        session_regenerate_id(true);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['faculty_staff_id'] = $row['faculty_staff_id'];
        $_SESSION['loggedin'] = true;

        header('Location: dashboard.php');
        exit;

    } else {
        echo "Invalid username or password!";
    }
}

// Logout
if (isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    header('Location: login.php');
    exit;
}