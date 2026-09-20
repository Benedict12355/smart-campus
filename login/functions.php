<?php
require 'db_con.php';

function checkEmailIfExist($email, $pdo)
{
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    return $stmt->rowCount() > 0;
}