<?php
session_start();
require_once __DIR__ . '/../../src/middleware/CSRF.php';
require_once __DIR__ . '/../../src/models/Database.php';

// Check if officer is logged in
if (!isset($_SESSION['officer_id'])) {
    header('Location: ../login.php?error=' . urlencode('Please log in again.'));
    exit;
}

$db = new DatabaseModel();
$csrf = new CSRF();
$csrf_token = $csrf->generateToken();

// Get voter ID from URL
$voter_id = $_GET['voter_id'] ?? null;
if (!$voter_id) {
    header('Location: index.php?error=' . urlencode('Voter ID not provided.'));
    exit;
}

// Get voter information
$stmt = $db->prepare('SELECT * FROM voters WHERE voter_id = ? AND division = (SELECT division FROM grama_niladhari WHERE officer_id = ?)');
$stmt->execute([$voter_id, $_SESSION['officer_id']]);
$voter = $stmt->fetch();

if (!$voter) {
    header('Location: index.php?error=' . urlencode('Voter not found or you do not have permission to edit this voter.'));
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !$csrf->validateToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        try {
            $updateStmt = $db->prepare('UPDATE voters SET full_name = ?, phone = ?, email = ?, address = ? WHERE voter_id = ? AND division = (SELECT division FROM grama_niladhari WHERE officer_id = ?)');
            $result = $updateStmt->execute([
                $_POST['full_name'],
                $_POST['phone'],
                $_POST['email'],
                $_POST['address'],
                $voter_id,
                $_SESSION['officer_id']
            ]);
            
            if ($result) {
                header('Location: index.php?success=' . urlencode('Voter information updated successfully.'));
                exit;
            } else {
                $error = 'Failed to update voter information.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred while updating voter information.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Voter - GN Officer Dashboard</title>
    <link rel="icon" href="../../assets/images/sri-lanka-flag.jpg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/sri-lanka-theme.css">
    <style>
        .emblem { width: 80px; height: auto; margin-bottom: 8px; }
        .dashboard-section { background: var(--sl-card); border-radius: 18px; box-shadow: var(--sl-shadow); padding: 40px 32px 32px 32px; max-width: 800px; margin: 40px auto; }
        .btn-back { background: linear-gradient(135deg, #6c757d, #5a6268); color: white; border: none; }
        .btn-update { background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); transition: all 0.3s ease; }
    </style>
</head>
<body>
    <div class="flag-bar"></div>
    <header class="secure-header text-center py-4 mb-3">
        <img src="../../assets/images/election-logo.jpg" alt="Sri Lanka Emblem" class="emblem">
        <h1 class="mb-1">Democratic Socialist Republic of Sri Lanka</h1>
        <h2 class="mb-0">Presidential Election - GN Officer Dashboard</h2>
    </header>
    <main>
        <section class="dashboard-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Edit Voter Information</h4>
                <a href="index.php" class="btn btn-back">← Back to Dashboard</a>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($voter['full_name']) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nic" class="form-label">NIC Number</label>
                        <input type="text" class="form-control" id="nic" value="<?= htmlspecialchars($voter['nic']) ?>" readonly>
                        <small class="form-text text-muted">NIC number cannot be changed</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($voter['phone']) ?>" pattern="^[0-9]{10}$" maxlength="10" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($voter['email']) ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?= htmlspecialchars($voter['address']) ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="province" class="form-label">Province</label>
                        <input type="text" class="form-control" id="province" value="<?= htmlspecialchars($voter['province']) ?>" readonly>
                        <small class="form-text text-muted">Province cannot be changed</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="district" class="form-label">District</label>
                        <input type="text" class="form-control" id="district" value="<?= htmlspecialchars($voter['district']) ?>" readonly>
                        <small class="form-text text-muted">District cannot be changed</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="division" class="form-label">GN Division</label>
                        <input type="text" class="form-control" id="division" value="<?= htmlspecialchars($voter['division']) ?>" readonly>
                        <small class="form-text text-muted">GN Division cannot be changed</small>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-update">Update Voter</button>
                </div>
            </form>
        </section>
    </main>
    <footer class="secure-footer mt-5 py-3">
        <img src="../../assets/images/election-logo.jpg" alt="Election Logo" class="logo mb-2">
        <div>© 2025 Democratic Socialist Republic of Sri Lanka - Presidential Election. All Rights Reserved.</div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
