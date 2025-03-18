<?php
include '../enrollment/connection.php';

// Delete student from the database
if (isset($_POST['id'])) {
    $id = $_POST['id'];  // Get the student ID passed from the request

    try {
        // Delete student by ID from the database
        $sql = 'DELETE FROM students WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Send success message back to JavaScript
        echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully']);
    } catch (PDOException $e) {
        // Send error message back to JavaScript
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
