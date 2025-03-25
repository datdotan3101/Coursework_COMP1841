<?php
require __DIR__ . '/../includes/config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $course_id = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM subjects WHERE id = :id");
        $stmt->bindParam(':id', $course_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request."]);
}
?>
