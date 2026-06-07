<?php
require_once __DIR__ . '/../../src/middleware/CSRF.php';
require_once __DIR__ . '/../../src/models/Officer.php';
$csrf = new CSRF();
$token = $csrf->generateToken();
$officer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$officer = null;
if ($officer_id) {
    $officer = Officer::findById($officer_id);
}
if (!$officer) {
    echo '<div class="alert alert-danger">Officer not found.</div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit GN Officer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/sri-lanka-theme.css">
</head>
<body>
    <div class="flag-bar">
        <div class="maroon"></div>
        <div class="gold"></div>
        <div class="green"></div>
        <div class="orange"></div>
    </div>
    <div class="container mt-5">
        <div class="secure-container">
            <div class="secure-header text-center mb-4">

                <h2>Edit GN Officer</h2>
            </div>
            <form method="POST" action="../../src/controllers/OfficerController.php" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <input type="hidden" name="action" value="edit_officer">
                <input type="hidden" name="officer_id" value="<?php echo $officer['officer_id']; ?>">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($officer['full_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nic" class="form-label">NIC Number</label>
                    <input type="text" class="form-control" id="nic" name="nic" maxlength="12" value="<?php echo htmlspecialchars($officer['nic']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="division" class="form-label">GN Division</label>
                    <input type="text" class="form-control" id="division" name="division" value="<?php echo htmlspecialchars($officer['division']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="district" class="form-label">District</label>
                    <input type="text" class="form-control" id="district" name="district" value="<?php echo htmlspecialchars($officer['district']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="province" class="form-label">Province</label>
                    <input type="text" class="form-control" id="province" name="province" value="<?php echo htmlspecialchars($officer['province']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($officer['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($officer['phone']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="1" <?php if($officer['is_active']) echo 'selected'; ?>>Active</option>
                        <option value="0" <?php if(!$officer['is_active']) echo 'selected'; ?>>Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Change Password (optional)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Update Officer</button>
                </div>
            </form>
        </div>
    </div>
    <footer class="secure-footer mt-5 py-3">

        <div>© 2025 Democratic Socialist Republic of Sri Lanka - Presidential Election. All Rights Reserved.</div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 