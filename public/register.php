<?php
require_once __DIR__ . '/../src/middleware/CSRF.php';
?>
<?php 
$activePage = ''; 
include 'header.php'; 
?>
<main>
    <section class="form-section">
        <?php $error = $_GET['error'] ?? ''; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <!-- Progress indicator -->
        <div class="progress mb-4" style="height: 8px;">
            <div class="progress-bar bg-success" id="formProgress" role="progressbar" style="width: 0%"></div>
        </div>
        
        <!-- Completion status -->
        <div class="alert alert-info" id="completionStatus">
            <i class="fas fa-info-circle"></i> Please fill in all required fields to complete your registration.
        </div>
        
        <form id="registerForm" method="POST" action="../src/controllers/VoterController.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars((new CSRF())->generateToken()); ?>">
            <input type="hidden" name="step" value="register">
            
            <div class="mb-3">
                <label for="full_name" class="form-label"><?= $tr['full_name'] ?> <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
                <div class="invalid-feedback" id="full_name_feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="nic" class="form-label"><?= $tr['nic_number'] ?> <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nic" name="nic" pattern="^([0-9]{9}[vVxX]|[0-9]{12})$" maxlength="12" required>
                <div class="form-text"><?= $tr['register_form_text_nic'] ?></div>
                <div class="invalid-feedback" id="nic_feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="dob" class="form-label"><?= $tr['dob'] ?> <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="dob" name="dob" required>
                <div class="invalid-feedback" id="dob_feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="age" class="form-label"><?= $tr['age'] ?></label>
                <input type="text" class="form-control" id="age" name="age" readonly>
            </div>
            
            <div class="mb-3">
                <label for="phone" class="form-label"><?= $tr['phone_number'] ?> <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phone" name="phone" pattern="^[0-9]{10}$" maxlength="10" required>
                <div class="invalid-feedback" id="phone_feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label"><?= $tr['email_address'] ?> <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback" id="email_feedback"></div>
                <div class="form-text" id="email_help">We'll use this to send your registration PIN.</div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label"><?= $tr['address'] ?> <span class="text-danger">*</span></label>
                <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                <div class="invalid-feedback" id="address_feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="province" class="form-label"><?= $tr['province'] ?> <span class="text-danger">*</span></label>
                <select class="form-control" id="province" name="province" required>
                    <option value="">Select Province</option>
                    <option value="Western">Western</option>
                    <option value="Central">Central</option>
                    <option value="Southern">Southern</option>
                    <option value="Northern">Northern</option>
                    <option value="Eastern">Eastern</option>
                    <option value="North Western">North Western</option>
                    <option value="North Central">North Central</option>
                    <option value="Uva">Uva</option>
                    <option value="Sabaragamuwa">Sabaragamuwa</option>
                </select>
                <div class="invalid-feedback" id="province_feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="district" class="form-label"><?= $tr['district'] ?> <span class="text-danger">*</span></label>
                <select class="form-control" id="district" name="district" required>
                    <option value="">Select District</option>
                </select>
                <div class="invalid-feedback" id="district_feedback"></div>
            </div>
            
            <div class="mb-3">
                <label for="gn_division" class="form-label"><?= $tr['gn_division'] ?> <span class="text-danger">*</span></label>
                <select class="form-control" id="gn_division" name="division" required>
                    <option value="">Select GN Division</option>
                </select>
                <div class="invalid-feedback" id="gn_division_feedback"></div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="button" class="btn btn-danger" onclick="window.location.href='login.php'"> <?= $tr['already_have_account'] ?> </button>
                <button type="submit" class="btn btn-danger" id="registerBtn"><?= $tr['register_button'] ?></button>
            </div>
            

        </form>
    </section>
</main>

<style>
.form-control.is-incomplete {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.form-control.is-complete {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.field-status {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
}

.field-container {
    position: relative;
}

.completion-indicator {
    display: inline-block;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    text-align: center;
    line-height: 20px;
    font-size: 12px;
    font-weight: bold;
    margin-left: 8px;
}

.completion-indicator.complete {
    background-color: #28a745;
    color: white;
}

.completion-indicator.incomplete {
    background-color: #ffc107;
    color: #212529;
}

.completion-indicator.error {
    background-color: #dc3545;
    color: white;
}
</style>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Form validation and completion tracking
    class RegistrationForm {
        constructor() {
            this.form = document.getElementById('registerForm');
            this.registerBtn = document.getElementById('registerBtn');
            this.progressBar = document.getElementById('formProgress');
            this.completionStatus = document.getElementById('completionStatus');
            this.requiredFields = ['full_name', 'nic', 'dob', 'phone', 'email', 'address', 'province', 'district', 'gn_division'];
            this.fieldStatus = {};
            this.emailCheckTimeout = null;
            
            this.init();
        }
        
        init() {
            this.setupFieldListeners();
            this.setupFormSubmission();
            this.updateProgress();
        }
        
        setupFieldListeners() {
            this.requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('input', () => this.validateField(fieldId));
                    field.addEventListener('blur', () => this.validateField(fieldId));
                    field.addEventListener('change', () => this.validateField(fieldId));
                }
            });
            
            // Special handling for email uniqueness check
            const emailField = document.getElementById('email');
            if (emailField) {
                emailField.addEventListener('input', () => {
                    clearTimeout(this.emailCheckTimeout);
                    this.emailCheckTimeout = setTimeout(() => this.checkEmailUniqueness(), 500);
                });
            }
        }
        
        validateField(fieldId) {
            const field = document.getElementById(fieldId);
            const value = field.value.trim();
            let isValid = true;
            let feedback = '';
            
            console.log(`Validating field: ${fieldId}, value: "${value}"`);
            
            // Remove previous status classes
            field.classList.remove('is-valid', 'is-invalid', 'is-incomplete', 'is-complete');
            
            // Field-specific validation
            switch(fieldId) {
                case 'nic':
                    isValid = /^([0-9]{9}[vVxX]|[0-9]{12})$/.test(value);
                    feedback = isValid ? '' : 'Please enter a valid NIC number (9 digits + V/X or 12 digits)';
                    break;
                    
                case 'phone':
                    isValid = /^[0-9]{10}$/.test(value);
                    feedback = isValid ? '' : 'Please enter a valid 10-digit phone number';
                    break;
                    
                case 'email':
                    isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                    feedback = isValid ? '' : 'Please enter a valid email address';
                    break;
                    
                case 'dob':
                    if (value) {
                        const dob = new Date(value);
                        const today = new Date();
                        const age = today.getFullYear() - dob.getFullYear();
                        const m = today.getMonth() - dob.getMonth();
                        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                            age--;
                        }
                        isValid = age >= 18;
                        feedback = isValid ? '' : 'You must be at least 18 years old to register';
                    } else {
                        isValid = false;
                        feedback = 'Please select your date of birth';
                    }
                    break;
                    
                case 'province':
                case 'district':
                case 'gn_division':
                    // For dropdown fields, check if a valid option is selected (not empty or default)
                    isValid = value.length > 0 && value !== 'Select Province' && value !== 'Select District' && value !== 'Select GN Division';
                    feedback = isValid ? '' : `Please select a ${fieldId.replace('_', ' ')}`;
                    break;
                    
                default:
                    isValid = value.length > 0;
                    feedback = isValid ? '' : 'This field is required';
            }
            
            // Update field status
            this.fieldStatus[fieldId] = {
                isValid: isValid,
                hasValue: value.length > 0,
                feedback: feedback
            };
            
            // Update field appearance
            if (isValid && value.length > 0) {
                field.classList.add('is-complete');
            } else if (value.length > 0 && !isValid) {
                field.classList.add('is-invalid');
            } else if (value.length === 0) {
                field.classList.add('is-incomplete');
            }
            
            // Show/hide feedback
            const feedbackElement = document.getElementById(fieldId + '_feedback');
            if (feedbackElement) {
                feedbackElement.textContent = feedback;
                feedbackElement.style.display = feedback ? 'block' : 'none';
            }
            
            this.updateProgress();
        }
        
        async checkEmailUniqueness() {
            const email = document.getElementById('email').value.trim();
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return;
            
            console.log('Checking email uniqueness for:', email);
            
            try {
                const response = await fetch('../src/controllers/VoterController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=check_email&email=${encodeURIComponent(email)}&csrf_token=${document.querySelector('input[name="csrf_token"]').value}`
                });
                
                const result = await response.text();
                console.log('Email check result:', result);
                
                if (result.includes('already_registered')) {
                    this.fieldStatus.email = {
                        isValid: false,
                        hasValue: true,
                        feedback: 'This email address is already registered'
                    };
                    document.getElementById('email').classList.remove('is-complete');
                    document.getElementById('email').classList.add('is-invalid');
                    document.getElementById('email_feedback').textContent = 'This email address is already registered';
                    document.getElementById('email_feedback').style.display = 'block';
                } else if (result.includes('available')) {
                    // Email is available, clear any previous error
                    const emailField = document.getElementById('email');
                    if (emailField.classList.contains('is-invalid')) {
                        emailField.classList.remove('is-invalid');
                        document.getElementById('email_feedback').style.display = 'none';
                    }
                }
            } catch (error) {
                console.error('Email check failed:', error);
            }
        }
        
        updateProgress() {
            const completedFields = this.requiredFields.filter(fieldId => 
                this.fieldStatus[fieldId] && this.fieldStatus[fieldId].isValid && this.fieldStatus[fieldId].hasValue
            ).length;
            
            const progress = (completedFields / this.requiredFields.length) * 100;
            this.progressBar.style.width = progress + '%';
            
            console.log(`Progress: ${completedFields}/${this.requiredFields.length} fields completed (${progress.toFixed(1)}%)`);
            
            // Update completion status
            if (progress === 100) {
                this.completionStatus.className = 'alert alert-success';
                this.completionStatus.innerHTML = '<i class="fas fa-check-circle"></i> All fields completed! You can now submit your registration.';
                this.registerBtn.classList.remove('btn-secondary');
                this.registerBtn.classList.add('btn-danger');
            } else {
                this.completionStatus.className = 'alert alert-info';
                this.completionStatus.innerHTML = `<i class="fas fa-info-circle"></i> ${completedFields} of ${this.requiredFields.length} fields completed. Please fill in all required fields.`;
                this.registerBtn.classList.remove('btn-danger');
                this.registerBtn.classList.add('btn-secondary');
            }
        }
        
        setupFormSubmission() {
            this.form.addEventListener('submit', (e) => {
                // Force validation of all fields before submission
                this.requiredFields.forEach(fieldId => this.validateField(fieldId));
                
                const allFieldsValid = this.requiredFields.every(fieldId => 
                    this.fieldStatus[fieldId] && this.fieldStatus[fieldId].isValid && this.fieldStatus[fieldId].hasValue
                );
                
                console.log('Form submission check:', {
                    fieldStatus: this.fieldStatus,
                    allFieldsValid: allFieldsValid,
                    requiredFields: this.requiredFields
                });
                
                if (!allFieldsValid) {
                    e.preventDefault();
                    alert('Please complete all required fields before submitting.');
                    return false;
                }
                
                // Show loading state
                this.registerBtn.disabled = true;
                this.registerBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registering...';
            });
        }
        
        // Method to manually validate all fields (for debugging)
        validateAllFields() {
            this.requiredFields.forEach(fieldId => this.validateField(fieldId));
            this.updateProgress();
        }
    }
    
    // Age auto-calc
    document.getElementById('dob').addEventListener('change', function() {
        const dob = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
        }
        document.getElementById('age').value = isNaN(age) ? '' : age;
    });
    
    // Province-District-GN Division mapping
    const locationData = {
        "Western": {
            "Colombo": ["Bambalapitiya", "Kirulapone", "Narahenpita", "Borella", "Wellawatte"],
            "Gampaha": ["Negombo", "Kelaniya", "Kadawatha", "Minuwangoda", "Kiribathgoda"],
            "Kalutara": ["Panadura", "Horana", "Kalutara North", "Bandaragama", "Matugama"]
        },
        "Central": {
            "Kandy": ["Katugastota", "Peradeniya", "Gampola", "Kundasale", "Akurana"],
            "Matale": ["Dambulla", "Matale Town", "Galewela", "Rattota", "Naula"],
            "Nuwara Eliya": ["Nuwara Eliya Town", "Hatton", "Talawakele", "Ginigathena", "Kotmale"]
        },
        "Southern": {
            "Galle": ["Galle Town", "Ambalangoda", "Hikkaduwa", "Baddegama", "Elpitiya"],
            "Matara": ["Matara Town", "Weligama", "Dikwella", "Akuressa", "Hakmana"],
            "Hambantota": ["Hambantota Town", "Tangalle", "Beliatta", "Tissamaharama", "Ambalantota"]
        },
        "Northern": {
            "Jaffna": ["Jaffna Town", "Chavakachcheri", "Nallur", "Point Pedro", "Kopay"],
            "Kilinochchi": ["Kilinochchi Town", "Pallai", "Kandavalai", "Karachchi", "Poonakary"],
            "Mannar": ["Mannar Town", "Nanaddan", "Madhu", "Musali", "Erukkalampiddy"],
            "Mullaitivu": ["Mullaitivu Town", "Oddusuddan", "Puthukudiyiruppu", "Maritimepattu", "Thunukkai"],
            "Vavuniya": ["Vavuniya Town", "Cheddikulam", "Vengalacheddikulam", "Poovarasankulam", "Omanthai"]
        },
        "Eastern": {
            "Batticaloa": ["Batticaloa Town", "Kalkudah", "Eravur", "Valaichchenai", "Kattankudy"],
            "Ampara": ["Ampara Town", "Kalmunai", "Akkaraipattu", "Pottuvil", "Uhana"],
            "Trincomalee": ["Trincomalee Town", "Kinniya", "Kantalai", "Muttur", "Seruwila"]
        },
        "North Western": {
            "Kurunegala": ["Kurunegala Town", "Kuliyapitiya", "Polgahawela", "Narammala", "Panduwasnuwara"],
            "Puttalam": ["Puttalam Town", "Chilaw", "Dankotuwa", "Wennappuwa", "Anamaduwa"]
        },
        "North Central": {
            "Anuradhapura": ["Anuradhapura Town", "Medawachchiya", "Mihintale", "Kekirawa", "Nochchiyagama"],
            "Polonnaruwa": ["Polonnaruwa Town", "Hingurakgoda", "Kaduruwela", "Medirigiriya", "Dimbulagala"]
        },
        "Uva": {
            "Badulla": ["Badulla Town", "Bandarawela", "Hali-Ela", "Passara", "Mahiyanganaya"],
            "Monaragala": ["Monaragala Town", "Wellawaya", "Bibile", "Buttala", "Katharagama"]
        },
        "Sabaragamuwa": {
            "Ratnapura": ["Ratnapura Town", "Balangoda", "Eheliyagoda", "Kuruwita", "Pelmadulla"],
            "Kegalle": ["Kegalle Town", "Mawanella", "Rambukkana", "Warakapola", "Ruwanwella"]
        }
    };

    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const gnDivisionSelect = document.getElementById('gn_division');

    provinceSelect.addEventListener('change', function() {
        const province = this.value;
        districtSelect.innerHTML = '<option value="">Select District</option>';
        gnDivisionSelect.innerHTML = '<option value="">Select GN Division</option>';
        if (province && locationData[province]) {
            Object.keys(locationData[province]).forEach(function(district) {
                const opt = document.createElement('option');
                opt.value = district;
                opt.textContent = district;
                districtSelect.appendChild(opt);
            });
        }
    });

    districtSelect.addEventListener('change', function() {
        const province = provinceSelect.value;
        const district = this.value;
        gnDivisionSelect.innerHTML = '<option value="">Select GN Division</option>';
        if (province && district && locationData[province] && locationData[province][district]) {
            locationData[province][district].forEach(function(gn) {
                const opt = document.createElement('option');
                opt.value = gn;
                opt.textContent = gn;
                gnDivisionSelect.appendChild(opt);
            });
        }
    });
    
    // Initialize the registration form
    document.addEventListener('DOMContentLoaded', function() {
        window.registrationForm = new RegistrationForm();
    });
</script>
</body>
</html> 