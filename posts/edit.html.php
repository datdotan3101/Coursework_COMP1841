<?php
include '../includes/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid post ID!");
}

$id = $_GET['id'];
$query = "SELECT * FROM posts WHERE id = ?";
$statement = $conn->prepare($query);
$statement->execute([$id]);
$post = $statement->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("No posts found!");
}

// Fetch modules
$moduleQuery = "SELECT * FROM subjects";
$moduleStatement = $conn->prepare($moduleQuery);
$moduleStatement->execute();
$modules = $moduleStatement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Question</title>
    <link rel="stylesheet" href="../assets/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-sm p-4">
            <h2 class="text-primary text-center">Edit</h2>
            <form action="../actions/edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">

                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Title:</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>

                <!-- Question -->
                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Question:</label>
                    <textarea name="content" id="content" class="form-control" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
                </div>

                <!-- Module Dropdown -->
                <div class="mb-3">
                    <label for="module" class="form-label fw-bold">Module:</label>
                    <select name="module" id="module" class="form-control" required>
                        <option value="">-- Select Module --</option>
                        <?php foreach ($modules as $module) : ?>
                            <option value="<?= $module['id'] ?>" <?= ($post['id'] == $module['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($module['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Show current image -->
                <?php if (!empty($post['image'])) : ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Image:</label>
                        <div class="text-center">
                            <img src="../<?= htmlspecialchars($post['image']) ?>" alt="Image" class="img-thumbnail shadow-sm" style="max-width: 250px;">
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Choose new image -->
                <div class="mb-3">
                    <label for="image" class="form-label fw-bold">New image (if any):</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                </div>

                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" class="btn btn-success px-4">Update</button>
                    <a href="../index.php" class="btn btn-danger px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>