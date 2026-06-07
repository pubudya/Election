<?php
require_once __DIR__ . '/../../src/middleware/CSRF.php';
$csrf = new CSRF();
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['csrf_token'])) {
    $token = $csrf->generateToken();
} else {
    $token = $_SESSION['csrf_token'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Candidate</title>
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

                <h2>Add Candidate</h2>
            </div>
            <form method="POST" action="../../src/controllers/CandidateController.php" class="needs-validation" novalidate enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <input type="hidden" name="action" value="add_candidate">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label for="nic" class="form-label">NIC Number</label>
                    <input type="text" class="form-control" id="nic" name="nic" maxlength="12" required>
                </div>
                <div class="mb-3">
                    <label for="party" class="form-label">Party</label>
                    <input type="text" class="form-control" id="party" name="party" required>
                </div>
                <div class="mb-3">
                    <label for="party_symbol" class="form-label">Party Symbol</label>
                    <input type="text" class="form-control" id="party_symbol" name="party_symbol">
                </div>
                <div class="mb-3">
                    <label for="candidate_number" class="form-label">Candidate Number</label>
                    <input type="number" class="form-control" id="candidate_number" name="candidate_number">
                </div>
                <div class="mb-3">
                    <label for="province" class="form-label">Province</label>
                    <select class="form-select" id="province" name="province">
                        <option value="">Select Province</option>
                        <option value="Central">Central</option>
                        <option value="Eastern">Eastern</option>
                        <option value="Northern">Northern</option>
                        <option value="North Central">North Central</option>
                        <option value="North Western">North Western</option>
                        <option value="Sabaragamuwa">Sabaragamuwa</option>
                        <option value="Southern">Southern</option>
                        <option value="Uva">Uva</option>
                        <option value="Western">Western</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="photo" class="form-label">Photo</label>
                    <input type="file" class="form-control" id="photo" name="photo">
                </div>
                <div class="mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Add Candidate</button>
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