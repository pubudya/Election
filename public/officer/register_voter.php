<?php 
    require_once __DIR__ . '/../../src/middleware/CSRF.php';
    require_once __DIR__ . '/../../src/models/Database.php';
    $csrf = new CSRF();
    $token = $csrf->generateToken();
    $error = isset($_GET['error']) ? $_GET['error'] : '';
    $success = isset($_GET['success']) ? $_GET['success'] : '';
    $voter = null;
    if (isset($_GET['voter_id'])) {
        $db = new DatabaseModel();
        $stmt = $db->prepare('SELECT * FROM voters WHERE voter_id = ?');
        $stmt->execute([$_GET['voter_id']]);
        $voter = $stmt->fetch();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $voter ? 'Edit' : 'Register'; ?> Voter - Officer - Sri Lankan Election System</title>
    <link rel="icon" href="../assets/images/sri-lanka-flag.png">
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
                <img src="../assets/images/sri-lanka-flag.png" alt="Sri Lanka Flag" class="logo mb-2">
                <h2><?php echo $voter ? 'Edit' : 'Register'; ?> Voter</h2>
                <p>Officer: <?php echo $_SESSION['officer_name'] ?? 'Officer'; ?></p>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form id="voterRegistrationForm" method="POST" action="../../src/controllers/VoterController.php" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <?php if ($voter): ?>
                    <input type="hidden" name="action" value="edit_voter">
                    <input type="hidden" name="voter_id" value="<?php echo htmlspecialchars($voter['voter_id']); ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="nic" class="form-label">National Identity Card (NIC) Number</label>
                    <input type="text" class="form-control" id="nic" name="nic" pattern="^[0-9]{9}[vVxX]?$" required value="<?php echo $voter ? htmlspecialchars($voter['nic']) : ''; ?>">
                    <div class="invalid-feedback">Please enter a valid NIC number</div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="full_name" required value="<?php echo $voter ? htmlspecialchars($voter['full_name']) : ''; ?>">
                        <div class="invalid-feedback">Please enter the voter's full name</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" required max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>" value="<?php echo $voter ? htmlspecialchars($voter['date_of_birth']) : ''; ?>">
                        <div class="invalid-feedback">Please enter a valid date of birth (must be 18+)</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Permanent Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $voter ? htmlspecialchars($voter['address']) : ''; ?></textarea>
                    <div class="invalid-feedback">Please enter the permanent address</div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="division" class="form-label">Grama Niladhari Division</label>
                        <input type="text" class="form-control" id="division" name="division" required value="<?php echo $voter ? htmlspecialchars($voter['division']) : ''; ?>">
                        <div class="invalid-feedback">Please enter the division</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="district" class="form-label">District</label>
                        <input type="text" class="form-control" id="district" name="district" required value="<?php echo $voter ? htmlspecialchars($voter['district']) : ''; ?>">
                        <div class="invalid-feedback">Please enter the district</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="province" class="form-label">Province</label>
                        <input type="text" class="form-control" id="province" name="province" required value="<?php echo $voter ? htmlspecialchars($voter['province']) : ''; ?>">
                        <div class="invalid-feedback">Please enter the province</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address (Optional)</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $voter ? htmlspecialchars($voter['email']) : ''; ?>">
                        <div class="invalid-feedback">Please enter a valid email address</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number (Optional)</label>
                        <input type="tel" class="form-control" id="phone" name="phone" pattern="[0-9]{10}" value="<?php echo $voter ? htmlspecialchars($voter['phone']) : ''; ?>">
                        <div class="invalid-feedback">Please enter a 10-digit phone number</div>
                    </div>
                </div>
                <?php if (!$voter): ?>
                <div class="mb-3">
                    <label for="pin" class="form-label">Create 4-digit PIN</label>
                    <input type="password" class="form-control" id="pin" name="pin" pattern="[0-9]{4}" maxlength="4" required>
                    <div class="invalid-feedback">Please enter a 4-digit PIN</div>
                    <small class="text-muted">This PIN will be used with the voter's NIC to vote</small>
                </div>
                <div class="mb-3">
                    <label for="confirmPin" class="form-label">Confirm PIN</label>
                    <input type="password" class="form-control" id="confirmPin" name="confirmPin" pattern="[0-9]{4}" maxlength="4" required>
                    <div class="invalid-feedback">PINs must match</div>
                </div>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success"><?php echo $voter ? 'Update Voter' : 'Register Voter'; ?></button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="index.php">Back to Dashboard</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/security.js"></script>
</body>
</html> 