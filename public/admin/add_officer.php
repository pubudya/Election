<?php
session_start();
require_once __DIR__ . '/../../src/middleware/CSRF.php';
$csrf = new CSRF();
$token = $csrf->generateToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add GN Officer</title>
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
                <h2>Add GN Officer</h2>
            </div>
            <form method="POST" action="../../src/controllers/OfficerController.php" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <input type="hidden" name="action" value="add_officer">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label for="nic" class="form-label">NIC Number</label>
                    <input type="text" class="form-control" id="nic" name="nic" maxlength="12" required>
                </div>
                <div class="mb-3">
                    <label for="province" class="form-label">Province</label>
                    <select class="form-select" id="province" name="province" required>
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
                    <label for="district" class="form-label">District</label>
                    <select class="form-select" id="district" name="district" required>
                        <option value="">Select District</option>
                        <option value="Ampara">Ampara</option>
                        <option value="Anuradhapura">Anuradhapura</option>
                        <option value="Badulla">Badulla</option>
                        <option value="Batticaloa">Batticaloa</option>
                        <option value="Colombo">Colombo</option>
                        <option value="Galle">Galle</option>
                        <option value="Gampaha">Gampaha</option>
                        <option value="Hambantota">Hambantota</option>
                        <option value="Jaffna">Jaffna</option>
                        <option value="Kalutara">Kalutara</option>
                        <option value="Kandy">Kandy</option>
                        <option value="Kegalle">Kegalle</option>
                        <option value="Kilinochchi">Kilinochchi</option>
                        <option value="Kurunegala">Kurunegala</option>
                        <option value="Mannar">Mannar</option>
                        <option value="Matale">Matale</option>
                        <option value="Matara">Matara</option>
                        <option value="Monaragala">Monaragala</option>
                        <option value="Mullaitivu">Mullaitivu</option>
                        <option value="Nuwara Eliya">Nuwara Eliya</option>
                        <option value="Polonnaruwa">Polonnaruwa</option>
                        <option value="Puttalam">Puttalam</option>
                        <option value="Ratnapura">Ratnapura</option>
                        <option value="Trincomalee">Trincomalee</option>
                        <option value="Vavuniya">Vavuniya</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="gn_division" class="form-label">GN Division</label>
                    <select class="form-select" id="gn_division" name="division" required>
                        <option value="">Select GN Division</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <!-- PIN is auto-generated and emailed; no manual password input -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Add Officer</button>
                </div>
            </form>
        </div>
    </div>
    <footer class="secure-footer mt-5 py-3">
        <img src="../../assets/images/election-logo.png" alt="Election Logo" class="logo mb-2">
        <div>© 2025 Democratic Socialist Republic of Sri Lanka - Presidential Election. All Rights Reserved.</div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Province-District-GN Division mapping (same as voter registration)
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

    // Populate districts when province changes (clear GN list too)
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

    // Populate GN divisions when district changes
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
    // Initialize if a province is preselected (edit scenarios)
    if (provinceSelect.value) {
        provinceSelect.dispatchEvent(new Event('change'));
        if (districtSelect.getAttribute('data-selected')) {
            districtSelect.value = districtSelect.getAttribute('data-selected');
            districtSelect.dispatchEvent(new Event('change'));
        }
    }
    </script>
</body>
</html> 