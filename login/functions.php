<?php
require '../config/db.php';

function checkEmailIfExist($email, $pdo)
{
    $sql = "SELECT * FROM users WHERE username = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    return $stmt->rowCount() > 0;
}