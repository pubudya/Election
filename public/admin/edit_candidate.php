<?php
require_once __DIR__ . '/../../src/middleware/CSRF.php';
require_once __DIR__ . '/../../src/models/Candidate.php';
$csrf = new CSRF();
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['csrf_token'])) {
    $token = $csrf->generateToken();
} else {
    $token = $_SESSION['csrf_token'];
}
$candidate_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$candidate = null;
if ($candidate_id) {
    $candidate = Candidate::findById($candidate_id);
}
if (!$candidate) {
    echo '<div class="alert alert-danger">Candidate not found.</div>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Candidate</title>
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

                <h2>Edit Candidate</h2>
            </div>
            <form method="POST" action="../../src/controllers/CandidateController.php" class="needs-validation" novalidate enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <input type="hidden" name="action" value="edit_candidate">
                <input type="hidden" name="candidate_id" value="<?php echo $candidate['candidate_id']; ?>">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($candidate['full_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nic" class="form-label">NIC Number</label>
                    <input type="text" class="form-control" id="nic" name="nic" maxlength="12" value="<?php echo htmlspecialchars($candidate['nic']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="party" class="form-label">Party</label>
                    <input type="text" class="form-control" id="party" name="party" value="<?php echo htmlspecialchars($candidate['party']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="party_symbol" class="form-label">Party Symbol</label>
                    <input type="text" class="form-control" id="party_symbol" name="party_symbol" value="<?php echo htmlspecialchars($candidate['party_symbol']); ?>">
                </div>
                <div class="mb-3">
                    <label for="candidate_number" class="form-label">Candidate Number</label>
                    <input type="number" class="form-control" id="candidate_number" name="candidate_number" value="<?php echo htmlspecialchars($candidate['candidate_number']); ?>">
                </div>
                <div class="mb-3">
                    <label for="province" class="form-label">Province</label>
                    <select class="form-select" id="province" name="province">
                        <option value="">Select Province</option>
                        <?php $provinces = ["Central","Eastern","Northern","North Central","North Western","Sabaragamuwa","Southern","Uva","Western"]; foreach($provinces as $prov): ?>
                        <option value="<?php echo $prov; ?>" <?php if($candidate['province']==$prov) echo 'selected'; ?>><?php echo $prov; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php if (!empty($candidate['photo_path'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Photo:</label><br>
                        <img src="../../assets/images/<?php echo htmlspecialchars($candidate['photo_path']); ?>" alt="Candidate Photo" style="width:50px;height:50px;object-fit:cover;border-radius:50%;border:2px solid var(--sl-gold);">
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="photo" class="form-label">Change Photo (optional)</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                </div>
                <?php if (!empty($candidate['party_logo'])): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Party Logo:</label><br>
                        <img src="../../assets/images/<?php echo htmlspecialchars($candidate['party_logo']); ?>" alt="Party Logo" style="width:50px;height:50px;object-fit:contain;">
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="1" <?php if($candidate['is_active']) echo 'selected'; ?>>Active</option>
                        <option value="0" <?php if(!$candidate['is_active']) echo 'selected'; ?>>Inactive</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Update Candidate</button>
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