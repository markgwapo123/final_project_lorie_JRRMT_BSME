<?php
$dsn = 'mysql:host=34.168.241.96;dbname=hci-sia_student_info';
$username = 'bary';
$password = '09085610152';

try {
    $pdo = new PDO($dsn, $username, $password);


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $firstName = $_POST['firstname'];
        $lastName = $_POST['lastname'];
        $email = $_POST['email'];
        $contactNumber = $_POST['contact_number'];
        $preferredCourse = $_POST['preferred_course'];
        $address = $_POST['address'];


        $sql = 'INSERT INTO students (firstname, lastname, email, contact_number, preferred_course, address) 
                VALUES (:firstname, :lastname, :email, :contact_number, :preferred_course, :address)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':firstname', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':contact_number', $contactNumber, PDO::PARAM_STR);
        $stmt->bindParam(':preferred_course', $preferredCourse, PDO::PARAM_STR);
        $stmt->bindParam(':address', $address, PDO::PARAM_STR);


        if ($stmt->execute()) {

            echo "<script>alert('Thank you for choosing our service!'); window.location.href = '../index.html';</script>";
        } else {

            echo "<script>alert('Error inserting data. Please try again.');</script>";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>