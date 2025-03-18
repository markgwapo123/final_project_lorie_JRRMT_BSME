<?php
include '../enrollment/connection.php';

// Update student details in the database
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];  // This is the student's unique ID
    $firstName = $_POST['firstname'];
    $lastName = $_POST['lastname'];
    $course = $_POST['course'];
    $email = $_POST['email'];

    try {
        // Update student by ID (using the unique identifier)
        $sql = 'UPDATE students SET firstname = :firstname, lastname = :lastname, preferred_course = :course, email = :email WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':firstname', $firstName, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastName, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Send success message back to JavaScript
        echo json_encode(['status' => 'success', 'message' => 'Student updated successfully']);
    } catch (PDOException $e) {
        // Send error message back to JavaScript
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
