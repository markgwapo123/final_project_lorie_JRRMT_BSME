<?php
$dsn = 'mysql:host=34.168.241.96;dbname=hci-sia_student_info';
$username = 'bary';
$password = '09085610152';

try {
    $pdo = new PDO($dsn, $username, $password);
    $sql = 'SELECT * FROM students';
    $stmt = $pdo->query($sql);

    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}