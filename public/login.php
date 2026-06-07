<?php 
$activePage = '';
include 'header.php';
require_once __DIR__ . '/../src/middleware/CSRF.php';
$csrf = new CSRF();
$token = $csrf->generateToken();

// Handle new error type system
$error_type = isset($_GET['error_type']) ? $_GET['error_type'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['registered']) ? $tr['login_success_registered'] : (isset($_GET['success']) ? $_GET['success'] : '');

// Get the entered credentials for repopulating fields
$entered_nic = isset($_GET['nic']) ? $_GET['nic'] : '';
$entered_username = isset($_GET['username']) ? $_GET['username'] : '';

// Determine which field has the error for visual indication
$nic_error = in_array($error_type, ['invalid_nic', 'not_registered', 'pending_approval', 'already_voted', 'inactive_account']);
$pin_error = in_array($error_type, ['invalid_pin']);
$username_error = in_array($error_type, ['invalid_username', 'account_locked']);
$password_error = in_array($error_type, ['invalid_password']);

// Get appropriate error message
$error_message = '';
if ($error_type) {
    switch ($error_type) {
        case 'invalid_nic':
            $error_message = $tr['login_error_invalid_nic'];
            break;
        case 'invalid_pin':
            $error_message = $tr['login_error_invalid_pin'];
            break;
        case 'invalid_username':
            $error_message = $tr['login_error_invalid_username'];
            break;
        case 'invalid_password':
            $error_message = $tr['login_error_invalid_password'];
            break;
        case 'not_registered':
            $error_message = $tr['login_error_not_registered'];
            break;
        case 'pending_approval':
            $error_message = $tr['login_error_pending_approval'];
            break;
        case 'already_voted':
            $error_message = $tr['login_error_already_voted'];
            break;
        case 'inactive_account':
            $error_message = $tr['login_error_inactive_account'];
            break;
        case 'account_locked':
            $error_message = $tr['login_error_account_locked'];
            break;
        case 'system_error':
            $error_message = $tr['login_error_system_error'];
            break;
        default:
            $error_message = $error;
    }
} else {
    $error_message = $error;
}
?>
<div class="container mt-5">
    <style>
        .action-btn { width: 96px; height: 40px; padding: 4px 8px; font-size: 0.85rem; line-height: 1.1; display: inline-flex; align-items: center; justify-content: center; text-align: center; white-space: normal; }
        
        /* Enhanced error styling */
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        /* Success state for valid fields */
        .form-control.is-valid {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        
        .form-control.is-valid:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        
        /* Error alert styling */
        .alert-danger {
            border-left: 4px solid #dc3545;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        /* Form field focus states */
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        /* Smooth transitions for validation states */
        .form-control {
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
    </style>
    <div class="secure-container">
        <div class="secure-header text-center mb-4">
            <img src="../assets/images/election-logo.jpg" alt="Sri Lanka Emblem" class="logo mt-3 mb-2">
            <h2><?= $tr['login_title'] ?></h2>
            <p><?= $tr['login_subtitle'] ?></p>
        </div>
        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form id="loginForm" method="POST" action="../src/controllers/AuthController.php" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
            <div class="mb-3">
                <label for="role" class="form-label"><?= $tr['login_as'] ?></label>
                <select class="form-select" id="role" name="role" required>
                    <option value="voter"><?= $tr['login_voter'] ?></option>
                    <option value="officer"><?= $tr['login_officer'] ?></option>
                    <option value="admin"><?= $tr['login_admin'] ?></option>
                </select>
            </div>
            <!-- Voter Login Fields -->
            <div id="voterFields">
                <div class="mb-3">
                    <label for="voter_nic" class="form-label"><?= $tr['nic_number'] ?></label>
                    <input type="text" class="form-control <?php echo $nic_error ? 'is-invalid' : ''; ?>" id="voter_nic" name="nic_username" pattern="^([0-9]{9}[vVxX]|[0-9]{12})$" maxlength="12" value="<?php echo htmlspecialchars($entered_nic); ?>">
                    <div class="form-text">12 digits or 9 digits + V/X</div>
                    <?php if ($nic_error): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="voter_pin" class="form-label"><?= $tr['pin'] ?></label>
                    <input type="password" class="form-control <?php echo $pin_error ? 'is-invalid' : ''; ?>" id="voter_pin" name="pin_password" maxlength="6" pattern="[0-9]{6}">
                    <div class="form-text"><?= $tr['pin_help'] ?></div>
                    <?php if ($pin_error): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Officer Login Fields -->
            <div id="officerFields" style="display:none;">
                <div class="mb-3">
                    <label for="officer_nic" class="form-label"><?= $tr['nic_number'] ?></label>
                    <input type="text" class="form-control <?php echo $nic_error ? 'is-invalid' : ''; ?>" id="officer_nic" name="officer_nic" maxlength="12" value="<?php echo htmlspecialchars($entered_nic); ?>">
                    <?php if ($nic_error): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="officer_password" class="form-label"><?= $tr['pin'] ?></label>
                                         <input type="password" class="form-control <?php echo $password_error ? 'is-invalid' : ''; ?>" id="officer_password" name="officer_password">
                    <?php if ($password_error): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Admin Login Fields -->
            <div id="adminFields" style="display:none;">
                <div class="mb-3">
                    <label for="admin_username" class="form-label">Username</label>
                    <input type="text" class="form-control <?php echo $username_error ? 'is-invalid' : ''; ?>" id="admin_username" name="admin_username" value="<?php echo htmlspecialchars($entered_username); ?>">
                    <?php if ($username_error): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="admin_password" class="form-label"><?= $tr['password'] ?></label>
                                         <input type="password" class="form-control <?php echo $password_error ? 'is-invalid' : ''; ?>" id="admin_password" name="admin_password">
                    <?php if ($password_error): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn btn-danger btn-sm action-btn" onclick="window.location.href='register.php'"><?= $tr['register_button'] ?></button>
                <button type="submit" class="btn btn-danger btn-sm action-btn" id="loginBtn"><?= $tr['login_button'] ?></button>
            </div>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Dynamic form fields by role
    const roleSelect = document.getElementById('role');
    const voterFields = document.getElementById('voterFields');
    const officerFields = document.getElementById('officerFields');
    const adminFields = document.getElementById('adminFields');
    
    function updateFields() {
        const selectedRole = roleSelect.value;
        
        // Show/hide fields
        voterFields.style.display = selectedRole === 'voter' ? '' : 'none';
        officerFields.style.display = selectedRole === 'officer' ? '' : 'none';
        adminFields.style.display = selectedRole === 'admin' ? '' : 'none';
        
        // Clear all fields when switching roles (only if no errors)
        if (selectedRole !== 'voter' && !<?php echo json_encode($error_type); ?>) {
            document.getElementById('voter_nic').value = '';
            document.getElementById('voter_pin').value = '';
        }
        if (selectedRole !== 'officer' && !<?php echo json_encode($error_type); ?>) {
            document.getElementById('officer_nic').value = '';
            document.getElementById('officer_password').value = '';
        }
        if (selectedRole !== 'admin' && !<?php echo json_encode($error_type); ?>) {
            document.getElementById('admin_username').value = '';
            document.getElementById('admin_password').value = '';
        }
        
        // Set required attributes for visible fields only
        document.getElementById('voter_nic').required = selectedRole === 'voter';
        document.getElementById('voter_pin').required = selectedRole === 'voter';
        document.getElementById('officer_nic').required = selectedRole === 'officer';
        document.getElementById('officer_password').required = selectedRole === 'officer';
        document.getElementById('admin_username').required = selectedRole === 'admin';
        document.getElementById('admin_password').required = selectedRole === 'admin';
        
        // Clear validation states when switching roles
        document.getElementById('voter_nic').classList.remove('is-invalid');
        document.getElementById('voter_pin').classList.remove('is-invalid');
        document.getElementById('officer_nic').classList.remove('is-invalid');
        document.getElementById('officer_password').classList.remove('is-invalid');
        document.getElementById('admin_username').classList.remove('is-invalid');
        document.getElementById('admin_password').classList.remove('is-invalid');
    }
    
    roleSelect.addEventListener('change', updateFields);
    updateFields(); // Set initial state
    
    // Form validation
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const selectedRole = roleSelect.value;
        let isValid = true;
        
        // Validate based on selected role
        if (selectedRole === 'voter') {
            const nic = document.getElementById('voter_nic').value.trim();
            const pin = document.getElementById('voter_pin').value.trim();
            if (!nic || !pin) {
                isValid = false;
            }
        } else if (selectedRole === 'officer') {
            const nic = document.getElementById('officer_nic').value.trim();
            const password = document.getElementById('officer_password').value.trim();
            if (!nic || !password) {
                isValid = false;
            }
        } else if (selectedRole === 'admin') {
            const username = document.getElementById('admin_username').value.trim();
            const password = document.getElementById('admin_password').value.trim();
            if (!username || !password) {
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            e.stopPropagation();
            alert('<?= $tr['login_error_required'] ?>');
        }
        
        this.classList.add('was-validated');
    });
    
    // Real-time validation for better UX
    function addRealTimeValidation() {
        // Voter NIC validation
        const voterNic = document.getElementById('voter_nic');
        if (voterNic) {
            voterNic.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && /^([0-9]{9}[vVxX]|[0-9]{12})$/.test(value)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else if (value) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });
        }
        
        // Voter PIN validation
        const voterPin = document.getElementById('voter_pin');
        if (voterPin) {
            voterPin.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && /^[0-9]{6}$/.test(value)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else if (value) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });
        }
        
        // Officer NIC validation
        const officerNic = document.getElementById('officer_nic');
        if (officerNic) {
            officerNic.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && /^([0-9]{9}[vVxX]|[0-9]{12})$/.test(value)) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else if (value) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });
        }
        
        // Admin username validation
        const adminUsername = document.getElementById('admin_username');
        if (adminUsername) {
            adminUsername.addEventListener('input', function() {
                const value = this.value.trim();
                if (value && value.length >= 3) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else if (value) {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });
        }
    }
    
    // Initialize real-time validation
    addRealTimeValidation();
</script>
</body>
</html> 