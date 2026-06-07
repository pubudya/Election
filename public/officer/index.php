<?php
session_start();
require_once __DIR__ . '/../../src/middleware/CSRF.php';
require_once __DIR__ . '/../../src/models/Database.php';
$db = new DatabaseModel();
$csrf = new CSRF();
$csrf_token = $csrf->generateToken();

// Determine the logged-in officer's GN division
$officer_id = $_SESSION['officer_id'] ?? null;
if (!$officer_id) {
    header('Location: ../login.php?error=' . urlencode('Please log in again.'));
    exit;
}
$officerStmt = $db->prepare('SELECT division, district, province FROM grama_niladhari WHERE officer_id = ?');
$officerStmt->execute([$officer_id]);
$officer = $officerStmt->fetch();
$officerDivision = $officer['division'] ?? '';
$officerDistrict = $officer['district'] ?? '';
$officerProvince = $officer['province'] ?? '';

// Fetch pending voters in this officer's GN division only
$stmt = $db->prepare('SELECT * FROM voters WHERE is_approved = 0 AND division = ?');
$stmt->execute([$officerDivision]);
$pending_voters = $stmt->fetchAll();
$error = isset($_GET['error']) ? $_GET['error'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GN Officer Dashboard - Presidential Election</title>
    <link rel="icon" href="../../assets/images/sri-lanka-flag.jpg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/sri-lanka-theme.css">
    <style>
        .emblem { width: 80px; height: auto; margin-bottom: 8px; }
        .dashboard-section { background: var(--sl-card); border-radius: 18px; box-shadow: var(--sl-shadow); padding: 40px 32px 32px 32px; max-width: 900px; margin: 40px auto; }
        .tab-content { margin-top: 32px; }
        .action-btn { width: 120px; height: 40px; padding: 4px 8px; font-size: 0.85rem; line-height: 1.1; display: inline-flex; align-items: center; justify-content: center; text-align: center; white-space: normal; }
        
        /* Improved action buttons styling */
        .table .btn {
            width: 120px !important;
            height: 36px !important;
            font-size: 0.8rem !important;
            font-weight: 500 !important;
            border-radius: 8px !important;
            border: none !important;
            margin: 2px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-decoration: none !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }
        
        .table .btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
        }
        
        .table .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
            color: white !important;
        }
        
        .table .btn-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800) !important;
            color: #212529 !important;
        }
        
        .table .btn-resend-pin {
            background: linear-gradient(135deg, #17a2b8, #138496) !important;
            color: white !important;
        }
        
        .table .btn-resend-pin:hover {
            background: linear-gradient(135deg, #138496, #117a8b) !important;
        }
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
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <ul class="nav nav-tabs" id="officerTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Register Voter</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approve-tab" data-bs-toggle="tab" data-bs-target="#approve" type="button" role="tab">Approve/Deny Registrations</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="manage-tab" data-bs-toggle="tab" data-bs-target="#manage" type="button" role="tab">Manage Voters</button>
                </li>
                <li class="nav-item ms-auto" role="presentation">
                    <a href="../../public/login.php" class="btn btn-danger">Logout</a>
                </li>
            </ul>
            <div class="tab-content" id="officerTabContent">
                <!-- Register Voter Tab -->
                <div class="tab-pane fade show active" id="register" role="tabpanel">
                    <h4 class="mb-3">Register New Voter</h4>
                    <form id="registerVoterForm" method="POST" action="../../src/controllers/VoterController.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="action" value="register_voter_by_gn">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nic" class="form-label">NIC Number</label>
                                <input type="text" class="form-control" id="nic" name="nic" pattern="^([0-9]{9}[vVxX]|[0-9]{12})$" maxlength="12" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" pattern="^[0-9]{10}$" maxlength="10" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="province" class="form-label">Province</label>
                                <select class="form-select" id="province" name="province" required>
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
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="district" class="form-label">District</label>
                                <select class="form-select" id="district" name="district" required>
                                    <option value="">Select District</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gn_division" class="form-label">GN Division</label>
                                <select class="form-select" id="gn_division" name="division" required>
                                    <option value="">Select GN Division</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-sm action-btn">Register</button>
                        </div>
                    </form>
                </div>
                <!-- Approve/Deny Registrations Tab -->
                <div class="tab-pane fade" id="approve" role="tabpanel">
                    <h4 class="mb-3">Approve or Deny Voter Registrations</h4>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>NIC</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($pending_voters) === 0): ?>
                            <tr><td colspan="5" class="text-center">No pending registrations.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pending_voters as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['nic']); ?></td>
                                <td><?php echo htmlspecialchars($row['division']); ?></td>
                                <td><?php echo htmlspecialchars($row['district']); ?></td>
                                <td>
                                    <form method="POST" action="../../src/controllers/VoterController.php" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <input type="hidden" name="action" value="approve_voter">
                                        <input type="hidden" name="voter_id" value="<?php echo $row['voter_id']; ?>">
                                        <button type="submit" class="btn btn-primary btn-sm action-btn">Approve</button>
                                    </form>
                                    <form method="POST" action="../../src/controllers/VoterController.php" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <input type="hidden" name="action" value="deny_voter">
                                        <input type="hidden" name="voter_id" value="<?php echo $row['voter_id']; ?>">
                                        <button type="submit" class="btn btn-primary btn-sm action-btn">Deny</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Manage Voters Tab -->
                <div class="tab-pane fade" id="manage" role="tabpanel">
                    <h4 class="mb-3">Manage Voters</h4>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <input type="text" id="searchVoter" class="form-control" placeholder="Search by Name or NIC">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped" id="votersTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>NIC</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Province</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $allStmt = $db->prepare('SELECT * FROM voters WHERE division = ?');
                        $allStmt->execute([$officerDivision]);
                        $all_voters = $allStmt->fetchAll();
                        foreach ($all_voters as $row): ?>
                            <tr data-name="<?= htmlspecialchars(strtolower($row['full_name'])) ?>" data-nic="<?= htmlspecialchars(strtolower($row['nic'])) ?>" data-province="<?= htmlspecialchars($row['province']) ?>" data-district="<?= htmlspecialchars($row['district']) ?>" data-gn="<?= htmlspecialchars($row['division']) ?>">
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= htmlspecialchars($row['nic']) ?></td>
                                <td><?= htmlspecialchars($row['division']) ?></td>
                                <td><?= htmlspecialchars($row['district']) ?></td>
                                <td><?= htmlspecialchars($row['province']) ?></td>
                                <td>
                                    <form method="POST" action="../../src/controllers/VoterController.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this voter?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <input type="hidden" name="action" value="delete_voter">
                                        <input type="hidden" name="voter_id" value="<?= $row['voter_id'] ?>">
                                        <button type="submit" class="btn btn-danger me-1">Delete</button>
                                    </form>
                                    <a href="edit_voter.php?voter_id=<?= $row['voter_id'] ?>" class="btn btn-warning me-1">Edit</a>
                                    <form method="POST" action="../../src/controllers/VoterController.php" style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <input type="hidden" name="action" value="resend_pin">
                                        <input type="hidden" name="voter_id" value="<?= $row['voter_id'] ?>">
                                        <button type="submit" class="btn btn-resend-pin" onclick="return confirm('Are you sure you want to resend the PIN to this voter?')">Resend PIN</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
    <footer class="secure-footer mt-5 py-3">
        <img src="../../assets/images/election-logo.jpg" alt="Election Logo" class="logo mb-2">
        <div>© 2025 Democratic Socialist Republic of Sri Lanka - Presidential Election. All Rights Reserved.</div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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

// Filtering logic for Manage Voters
const searchVoter = document.getElementById('searchVoter');
const votersTable = document.getElementById('votersTable').getElementsByTagName('tbody')[0];

searchVoter.addEventListener('input', filterVoters);

function filterVoters() {
    const search = searchVoter.value.trim().toLowerCase();
    Array.from(votersTable.rows).forEach(function(row) {
        const name = row.getAttribute('data-name');
        const nic = row.getAttribute('data-nic');
        let show = true;
        if (search && !(name.includes(search) || nic.includes(search))) show = false;
        row.style.display = show ? '' : 'none';
    });
}
</script>
</body>
</html> 