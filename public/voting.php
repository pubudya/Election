<?php 
session_start();
require_once __DIR__ . '/../src/middleware/CSRF.php';
$csrf = new CSRF();
$token = $csrf->generateToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote - Sri Lankan Election System</title>
    <link rel="icon" href="../assets/images/election-logo.jpg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/sri-lanka-theme.css">
    <style>
        .candidate-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .candidate-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .candidate-card.selected {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .candidate-card.border-primary {
            border-color: #007bff !important;
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
        }
        
        .candidate-card.border-warning {
            border-color: #ffc107 !important;
            background: linear-gradient(135deg, #fffaf0 0%, #ffffff 100%);
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.35);
        }
        
        .candidate-card.border-success {
            border-color: #28a745 !important;
            background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.3);
        }
        
        .preference-btn {
            transition: all 0.3s ease;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }
        
        .preference-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .preference-btn.active {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.25);
            font-weight: 700;
        }
        
        .preference-btn.active:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        /* Add a pulsing effect for active buttons */
        .preference-btn.active {
            animation: buttonPulse 2s infinite;
            z-index: 10;
        }
        
        @keyframes buttonPulse {
            0% { transform: scale(1.05); }
            50% { transform: scale(1.08); }
            100% { transform: scale(1.05); }
        }
        
        /* Debug: Make active buttons very visible */
        .preference-btn.active::after {
            content: ' ✓';
            font-weight: bold;
            color: inherit;
        }
        
        /* Enhanced active button styles */
        .preference-btn.btn-primary.active {
            background: linear-gradient(135deg, #0056b3 0%, #007bff 100%) !important;
            border-color: #0056b3 !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4) !important;
        }
        
        .preference-btn.btn-warning.active {
            background: linear-gradient(135deg, #e0a800 0%, #ffc107 100%) !important;
            border-color: #e0a800 !important;
            color: #212529 !important;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4) !important;
        }
        
        .preference-btn.btn-success.active {
            background: linear-gradient(135deg, #1e7e34 0%, #28a745 100%) !important;
            border-color: #1e7e34 !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4) !important;
        }
        
        /* Ensure active state overrides Bootstrap */
        .preference-btn.active.btn-primary {
            background: linear-gradient(135deg, #0056b3 0%, #007bff 100%) !important;
            border-color: #0056b3 !important;
            color: white !important;
        }
        
        .preference-btn.active.btn-warning {
            background: linear-gradient(135deg, #e0a800 0%, #ffc107 100%) !important;
            border-color: #e0a800 !important;
            color: #212529 !important;
        }
        
        .preference-btn.active.btn-success {
            background: linear-gradient(135deg, #1e7e34 0%, #28a745 100%) !important;
            border-color: #1e7e34 !important;
            color: white !important;
        }
        
        .candidate-card.selected .card-title {
            color: #007bff;
            font-weight: 700;
        }
        
        .selection-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
            opacity: 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .candidate-card.border-primary .selection-indicator {
            background: #007bff;
            opacity: 1;
        }
        
        .candidate-card.border-warning .selection-indicator {
            background: #ffc107;
            color: #212529;
            opacity: 1;
        }
        
        .candidate-card.border-success .selection-indicator {
            background: #28a745;
            opacity: 1;
        }
        /* Accessibility focus styles */
        .candidate-card:focus-visible {
            outline: 3px solid rgba(0, 123, 255, 0.45);
            outline-offset: 2px;
        }
        .preference-btn:focus-visible {
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.35);
        }
        
        /* Enhanced button click feedback */
        .preference-btn:active {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }
        
        /* Smooth transitions for all button states */
        .preference-btn.btn-outline-primary:hover {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            transform: translateY(-2px);
        }
        
        .preference-btn.btn-outline-warning:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
            transform: translateY(-2px);
        }
        
        .preference-btn.btn-outline-success:hover {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="flag-bar">
        <div class="maroon"></div>
        <div class="gold"></div>
        <div class="green"></div>
        <div class="orange"></div>
    </div>
    <div class="container mt-5">
        <div class="voting-container">
            <div class="voting-header text-center mb-4">
                <img src="../assets/images/election-logo.jpg" alt="Sri Lanka Election" class="logo mb-2">
                <h2>Presidential Election Voting</h2>
                <div class="voting-timer" aria-live="polite">
                    <div class="progress" style="height: 8px; margin-bottom: 8px;">
                        <div id="timeProgress" class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
                    </div>
                    <span id="timeRemaining">05:00</span>
                </div>
            </div>
            <div class="voting-alert alert alert-info">
                <strong>Instructions:</strong> Select your 1st, 2nd, and 3rd preferences for candidates. You must select at least a 1st preference. You have 5 minutes to complete your vote.
            </div>
            <form id="votingForm" method="POST" action="../src/controllers/VoteController.php">
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <div class="candidates-list row" role="list">
                    <?php
                    require_once __DIR__ . '/../src/models/Candidate.php';
                    $candidates = Candidate::getAll();
                    foreach ($candidates as $candidate):
                        $img = !empty($candidate['photo_path']) ? '../assets/images/' . $candidate['photo_path'] : '../assets/images/election-logo.jpg';
                    ?>
                    <div class="col-md-4 mb-3" role="listitem">
                        <div class="card candidate-card" tabindex="0" data-candidate-id="<?php echo $candidate['candidate_id']; ?>">
                            <div class="selection-indicator"></div>
                            <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($candidate['full_name']); ?>" style="width:100px;height:100px;object-fit:cover;border-radius:50%;border:2px solid var(--sl-gold);margin:0 auto;">
                            <?php if (!empty($candidate['party_logo'])): ?>
                                <img src="<?php echo '../assets/images/' . htmlspecialchars($candidate['party_logo']); ?>" alt="Party Logo" style="width:40px;height:40px;object-fit:contain;margin:8px auto 0 auto;display:block;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($candidate['full_name']); ?></h5>
                                <p class="card-text">Party: <?php echo htmlspecialchars($candidate['party']); ?><br>Symbol: <?php echo htmlspecialchars($candidate['party_symbol']); ?><br>Province: <?php echo htmlspecialchars($candidate['province']); ?></p>
                                <div class="btn-group w-100" role="group" aria-label="Select preference">
                                    <button type="button" class="btn btn-outline-primary preference-btn" data-pref="1" aria-pressed="false">1st</button>
                                    <button type="button" class="btn btn-outline-warning preference-btn" data-pref="2" aria-pressed="false">2nd</button>
                                    <button type="button" class="btn btn-outline-success preference-btn" data-pref="3" aria-pressed="false">3rd</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="firstPref" name="firstPref" required>
                <input type="hidden" id="secondPref" name="secondPref">
                <input type="hidden" id="thirdPref" name="thirdPref">
                <div class="selected-preferences mt-4">
                    <h4>Your Selected Preferences:</h4>
                    <div id="selectedPrefsDisplay" class="alert alert-secondary" role="status"></div>
                </div>
                <div class="voting-actions mt-4 d-flex justify-content-between">
                    <button type="button" id="clearVotesBtn" class="btn btn-danger">Clear All Selections</button>
                    <button type="button" id="submitVoteBtn" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmSubmitModal">Submit Vote</button>
                </div>
            </form>
            <!-- Confirm Submit Modal -->
            <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="confirmSubmitLabel">Confirm Your Vote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p>Please confirm your preferences before submitting. You cannot change your vote after submission.</p>
                    <div id="modalPrefsPreview" class="p-2 bg-light border rounded"></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Review</button>
                    <button type="submit" class="btn btn-success" id="confirmSubmitBtn" form="votingForm">Submit Now</button>
                  </div>
                </div>
              </div>
            </div>
            <footer class="voting-footer mt-5">
                <hr>
                <p>&copy; <?php echo date('Y'); ?> Sri Lankan Election Commission. All Rights Reserved.</p>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/security.js"></script>
    <script src="../assets/js/voting.js"></script>
</body>
</html> 