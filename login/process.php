<?php

session_start();

require '../config/db.php';
require 'functions.php';


// Registration Process
if (isset($_POST["registration"])) {

    $fname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $pswd = $_POST["pswd"] ?? "";


    // Check if fields are empty
    if (empty($fname) || empty($email) || empty($pswd)) {

        echo "Please fill in all fields.";
        exit;
    }


    // Hash password
    $password = password_hash($pswd, PASSWORD_BCRYPT);


    // Check if email already exists
    if (checkEmailIfExist($email, $pdo)) {

        echo "Email already exists!";
        exit;

    } else {

        try {

            // Step 1: Create faculty/staff profile
            $stmt = $pdo->prepare("
                INSERT INTO faculty_staff
                (full_name, department, role_title)
                VALUES
                (:name, :dept, :role)
            ");

            $stmt->execute([
                "name" => $fname,
                "dept" => "Unassigned",
                "role" => "Faculty"
            ]);


            // Get the newly created faculty ID
            $facultyId = $pdo->lastInsertId();


            // Step 2: Create login account
            $stmt = $pdo->prepare("
                INSERT INTO users
                (email, password_hash, faculty_staff_id)
                VALUES
                (:email, :password, :faculty_id)
            ");

            $stmt->execute([
                "email" => $email,
                "password" => $password,
                "faculty_id" => $facultyId
            ]);


            // Registration successful
            echo "success";
            exit;


        } catch (PDOException $e) {

            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}



// Login Process
if (isset($_POST["login"])) {

    $username = trim($_POST['username'] ?? "");
    $password = $_POST['password'] ?? "";


    // Check empty fields
    if (empty($username) || empty($password)) {

        echo "Please enter your email and password.";
        exit;
    }


    // Find user by email
    $sql = "
        SELECT *
        FROM users
        WHERE email = :email
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        'email' => $username
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);


    // Verify password
    if ($row && password_verify($password, $row['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['faculty_staff_id'] = $row['faculty_staff_id'];
        $_SESSION['loggedin'] = true;


        echo "success";
        exit;

    } else {

        echo "Invalid username or password!";
        exit;
    }
}



// Logout
if (isset($_GET['logout'])) {

    $_SESSION = array();

    session_destroy();

    header('Location: index.php');

    exit;
}