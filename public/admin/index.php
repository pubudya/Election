<?php
require_once __DIR__ . '/../../src/middleware/CSRF.php';
$csrf = new CSRF();
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php?error=' . urlencode('Please login as admin.'));
    exit;
}
if (!isset($_SESSION['csrf_token'])) {
    $csrf_token = $csrf->generateToken();
} else {
    $csrf_token = $_SESSION['csrf_token'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Presidential Election</title>
    <link rel="icon" href="../../assets/images/sri-lanka-flag.jpg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/sri-lanka-theme.css">
    <style>
        .emblem { width: 80px; height: auto; margin-bottom: 8px; }
        .dashboard-section { background: var(--sl-card); border-radius: 18px; box-shadow: var(--sl-shadow); padding: 40px 32px 32px 32px; max-width: 1100px; margin: 40px auto; }
        .tab-content { margin-top: 32px; }
        .action-btn { min-width: 120px; }
        .election-actions .btn { 
            min-width: 120px; 
            height: 32px; 
            font-size: 12px; 
            padding: 4px 8px; 
            margin: 2px;
        }
        .election-actions .btn-primary { background-color: #007bff; border-color: #007bff; }
        .election-actions .btn-outline-primary { color: #007bff; border-color: #007bff; }
        .election-actions .btn-success { background-color: #28a745; border-color: #28a745; }
        .election-actions .btn-warning { background-color: #ffc107; border-color: #ffc107; color: #212529; }
        .election-actions .btn-secondary { background-color: #6c757d; border-color: #6c757d; }
        .election-actions .btn-danger { background-color: #dc3545; border-color: #dc3545; }
        .election-actions .btn:disabled { 
            opacity: 0.7; 
            cursor: not-allowed; 
            background-color: #6c757d !important; 
            border-color: #6c757d !important; 
            color: #fff !important;
        }
        /* Consistent button styling for all admin tables */
        .table .btn { 
            min-width: 120px; 
            height: 32px; 
            font-size: 12px; 
            padding: 4px 8px; 
            margin: 2px;
        }
        .table .btn-primary { background-color: #007bff; border-color: #007bff; }
        .table .btn-outline-primary { color: #007bff; border-color: #007bff; }
        .table .btn-success { background-color: #28a745; border-color: #28a745; }
        .table .btn-warning { background-color: #ffc107; border-color: #ffc107; color: #212529; }
        .table .btn-secondary { background-color: #6c757d; border-color: #6c757d; }
        .table .btn-danger { background-color: #dc3545; border-color: #dc3545; }
        .table .btn-info { background-color: #17a2b8; border-color: #17a2b8; }
    </style>
</head>
<body>
    <div class="flag-bar"></div>
    <header class="secure-header text-center py-4 mb-3">

        <h1 class="mb-1">Democratic Socialist Republic of Sri Lanka</h1>
        <h2 class="mb-0">Presidential Election - Admin Dashboard</h2>
    </header>
    <main>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success text-center">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>
        <section class="dashboard-section">
            <ul class="nav nav-tabs" id="adminTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="gn-tab" data-bs-toggle="tab" data-bs-target="#gn" type="button" role="tab">Manage GN Officers</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="candidates-tab" data-bs-toggle="tab" data-bs-target="#candidates" type="button" role="tab">Manage Candidates</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="launch-tab" data-bs-toggle="tab" data-bs-target="#launch" type="button" role="tab">Launch Elections</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews & Queries</button>
                </li>
                <!-- Results tab removed per request -->
                <li class="nav-item ms-auto" role="presentation">
                    <a href="../../public/login.php" class="btn btn-danger">Logout</a>
                </li>
            </ul>
            <div class="tab-content" id="adminTabContent">
                <!-- Manage GN Officers Tab -->
                <div class="tab-pane fade show active" id="gn" role="tabpanel">
                    <h4 class="mb-3">Manage GN Officers</h4>
                    <a href="add_officer.php" class="btn btn-success mb-3">Add Officer</a>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>NIC</th>
                                <th>Division</th>
                                <th>District</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once __DIR__ . '/../../src/models/Officer.php';
                            $officers = Officer::getAll();
                            foreach ($officers as $officer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($officer['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($officer['nic']); ?></td>
                                <td><?php echo htmlspecialchars($officer['division']); ?></td>
                                <td><?php echo htmlspecialchars($officer['district']); ?></td>
                                <td><?php echo htmlspecialchars($officer['email']); ?></td>
                                <td><?php echo htmlspecialchars($officer['phone']); ?></td>
                                <td><?php echo $officer['is_active'] ? 'Active' : 'Inactive'; ?></td>
                                <td>
                                    <a href="edit_officer.php?id=<?php echo $officer['officer_id']; ?>" class="btn btn-warning me-2">Edit</a>
                                    <form method="POST" action="../../src/controllers/AdminController.php" style="display:inline;" onsubmit="return confirm('Delete this officer? This cannot be undone.')">
                                        <input type="hidden" name="action" value="delete_officer">
                                        <input type="hidden" name="officer_id" value="<?php echo $officer['officer_id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm action-btn">Delete</button>
                                    </form>
                                    <form method="POST" action="../../src/controllers/AdminController.php" style="display:inline;">
                                        <input type="hidden" name="action" value="resend_officer_pin">
                                        <input type="hidden" name="officer_id" value="<?php echo $officer['officer_id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="btn btn-resend-pin btn-sm action-btn">Resend PIN</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Manage Candidates Tab -->
                <div class="tab-pane fade" id="candidates" role="tabpanel">
                    <h4 class="mb-3">Manage Candidates</h4>
                    <a href="add_candidate.php" class="btn btn-success mb-3">Add Candidate</a>
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>NIC</th>
                                <th>Party</th>
                                <th>Symbol</th>
                                <th>Number</th>
                                <th>Province</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            require_once __DIR__ . '/../../src/models/Candidate.php';
                            $candidates = Candidate::getAll();
                            foreach ($candidates as $candidate): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($candidate['candidate_id']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['nic']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['party']); ?></td>
                                <td><?php echo htmlspecialchars($candidate['party_symbol'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($candidate['candidate_number'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($candidate['province']); ?></td>
                                <td><?php echo $candidate['is_active'] ? 'Active' : 'Inactive'; ?></td>
                                <td>
                                    <a href="edit_candidate.php?id=<?php echo $candidate['candidate_id']; ?>" class="btn btn-warning me-2">Edit</a>
                                    <form method="POST" action="../../src/controllers/CandidateController.php" style="display:inline;" onsubmit="return confirm('Delete this candidate? This cannot be undone.')">
                                        <input type="hidden" name="action" value="delete_candidate">
                                        <input type="hidden" name="candidate_id" value="<?php echo $candidate['candidate_id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm action-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Launch Elections Tab -->
                <div class="tab-pane fade" id="launch" role="tabpanel">
                    <h4 class="mb-3">Launch Elections</h4>
                    <form class="row g-3 mb-4" method="POST" action="../../src/controllers/AdminController.php">
                        <input type="hidden" name="action" value="launch_election">
                        <input type="hidden" name="election_name" value="Presidential Election">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <div class="col-md-4">
                            <label for="votingStart" class="form-label">Voting Start</label>
                            <input type="datetime-local" class="form-control" id="votingStart" name="start_date" required>
                        </div>
                        <div class="col-md-4">
                            <label for="votingEnd" class="form-label">Voting End</label>
                            <input type="datetime-local" class="form-control" id="votingEnd" name="end_date" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-success action-btn">Launch Voting</button>
                        </div>
                    </form>
                    <h5 class="mt-4">Election History</h5>
                    <?php
                    require_once __DIR__ . '/../../src/models/Database.php';
                    $db = new DatabaseModel();
                    $history = $db->query("SELECT * FROM election_config ORDER BY start_date DESC")->fetchAll();
                    if ($history && count($history) > 0) {
                        echo '<table class="table table-bordered table-striped mt-3">';
                        echo '<thead class="table-dark"><tr>';
                        echo '<th>Name</th><th>Start</th><th>End</th><th>Status</th><th>Results</th><th>Votes</th><th>Actions</th>';
                        echo '</tr></thead><tbody>';
                        foreach ($history as $e) {
                            $now = new DateTime('now');
                            $start = new DateTime($e['start_date']);
                            $end = new DateTime($e['end_date']);
                            
                            // Get vote count for this election FIRST
                            $voteCountStmt = $db->prepare('SELECT COUNT(*) as count FROM votes WHERE election_id = ?');
                            $voteCountStmt->execute([$e['config_id']]);
                            $voteCount = $voteCountStmt->fetch()['count'];
                            
                            if ($now < $start) {
                                $status = 'Not Started';
                                $countdown = '<span class="badge bg-info text-dark">Starts in <span class="countdown" data-mode="until" data-time="' . $start->format('Y-m-d H:i:s') . '"></span></span>';
                                $progressHtml = '';
                            } elseif ($now >= $start && $now <= $end) {
                                $status = 'Voting Open';
                                $countdown = '<span class="badge bg-success">Ends in <span class="countdown" data-mode="until" data-time="' . $end->format('Y-m-d H:i:s') . '"></span></span>';
                                $totalWindow = max(1, $end->getTimestamp() - $start->getTimestamp());
                                $elapsed = max(0, min($totalWindow, $now->getTimestamp() - $start->getTimestamp()));
                                $pct = (int) floor(($elapsed / $totalWindow) * 100);
                                $progressHtml = '<div class="progress mt-1" style="height:6px;"><div class="progress-bar bg-success election-progress" role="progressbar" style="width: ' . $pct . '%" aria-valuenow="' . $pct . '" aria-valuemin="0" aria-valuemax="100" data-start="' . $start->format('Y-m-d H:i:s') . '" data-end="' . $end->format('Y-m-d H:i:s') . '"></div></div><div class="small text-muted">' . $pct . '% elapsed</div>';
                            } else {
                                $status = 'Voting Closed';
                                $countdown = '<span class="badge bg-secondary">Ended <span class="countdown" data-mode="since" data-time="' . $end->format('Y-m-d H:i:s') . '"></span> ago</span>';
                                $progressHtml = '';
                            }
                            $resultsStatus = $e['results_published'] ? 'Published' : 'Not Published';
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($e['election_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($e['start_date']) . '</td>';
                            echo '<td>' . htmlspecialchars($e['end_date']) . '</td>';
                            echo '<td data-election-status data-start="' . $start->format('Y-m-d H:i:s') . '" data-end="' . $end->format('Y-m-d H:i:s') . '">';
                            echo '<div><span class="badge ' . ($status === 'Voting Open' ? 'bg-success' : ($status === 'Not Started' ? 'bg-info text-dark' : 'bg-secondary')) . '">' . $status . '</span></div>';
                            echo '<div class="mt-1">' . $countdown . '</div>';
                            echo $progressHtml;
                            echo '</td>';
                            echo '<td>' . $resultsStatus . '</td>';
                            echo '<td>' . $voteCount . '</td>';
                            echo '<td class="election-actions">';
                            // Results buttons
                            if ($now > $end) {
                                echo '<a href="../../public/results.php?election_id=' . $e['config_id'] . '" class="btn btn-primary btn-sm me-1">View Results</a>';
                            } elseif ($now <= $end && $voteCount > 0) {
                                echo '<a href="../../public/results.php?election_id=' . $e['config_id'] . '" class="btn btn-outline-primary btn-sm me-1">Preview Results</a>';
                            }
                            
                            // Publish button (show when there are votes and election is not published)
                            if ($voteCount > 0 && !$e['results_published']) {
                                echo '<form method="POST" action="../../src/controllers/AdminController.php" style="display:inline;">';
                                echo '<input type="hidden" name="action" value="publish_results">';
                                echo '<input type="hidden" name="election_id" value="' . $e['config_id'] . '">';
                                echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">';
                                echo '<button type="submit" class="btn btn-success btn-sm me-1">Publish to Home</button>';
                                echo '</form>';
                            } elseif ($voteCount > 0 && $e['results_published']) {
                                echo '<button type="button" class="btn btn-success btn-sm me-1" disabled>Published</button>';
                            }
                            
                            // Vote count as a button
                            if ($voteCount > 0) {
                                echo '<button type="button" class="btn btn-info btn-sm me-1" disabled>' . $voteCount . ' votes</button>';
                                echo '<form method="POST" action="../../src/controllers/AdminController.php" style="display:inline;" onsubmit="return confirm(\'Delete all votes for this election? This cannot be undone.\')">';
                                echo '<input type="hidden" name="action" value="delete_election_votes">';
                                echo '<input type="hidden" name="election_id" value="' . $e['config_id'] . '">';
                                echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">';
                                echo '<button type="submit" class="btn btn-warning btn-sm me-1">Delete Votes</button>';
                                echo '</form>';
                            }
                            
                            // End Now (force) button when running or not started
                            if ($now <= $end) {
                                echo '<form method="POST" action="../../src/controllers/AdminController.php" style="display:inline;" onsubmit="return confirm(\'End this election now?\')">';
                                echo '<input type="hidden" name="action" value="end_now">';
                                echo '<input type="hidden" name="election_id" value="' . $e['config_id'] . '">';
                                echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">';
                                echo '<button type="submit" class="btn btn-secondary btn-sm me-1">End Now</button>';
                                echo '</form>';
                            }
                            // Delete election button
                            echo '<form method="POST" action="../../src/controllers/AdminController.php" style="display:inline;" onsubmit="return confirm(\'Delete this election? This cannot be undone.\')">';
                            echo '<input type="hidden" name="action" value="delete_election">';
                            echo '<input type="hidden" name="election_id" value="' . $e['config_id'] . '">';
                            echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">';
                            echo '<button type="submit" class="btn btn-danger btn-sm">Delete Election</button>';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<div class="alert alert-secondary">No election history found.</div>';
                    }
                    ?>

                </div>
                <!-- Reviews & Queries Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <h4 class="mb-3">Reviews & Queries</h4>
                    <?php
                    require_once __DIR__ . '/../../src/models/Database.php';
                    $db = new DatabaseModel();
                    $reviews = $db->query("SELECT * FROM reviews_queries ORDER BY created_at DESC")->fetchAll();
                    if ($reviews && count($reviews) > 0) {
                        echo '<table class="table table-bordered table-striped">';
                        echo '<thead class="table-dark"><tr><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Reply</th><th>Actions</th></tr></thead><tbody>';
                        foreach ($reviews as $r) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($r['name']) . '</td>';
                            echo '<td>' . htmlspecialchars($r['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($r['subject']) . '</td>';
                            echo '<td>' . nl2br(htmlspecialchars($r['message'])) . '</td>';
                            echo '<td>';
                            if (!empty($r['reply'])) {
                                echo '<div class="p-2 bg-light">' . nl2br(htmlspecialchars($r['reply'])) . '<br><span class="text-muted" style="font-size:0.9em;">(' . htmlspecialchars($r['replied_at']) . ')</span></div>';
                            } else {
                                echo '<span class="text-muted">No reply yet</span>';
                            }
                            echo '</td>';
                            echo '<td>';
                            if (empty($r['reply'])) {
                                echo '<form method="POST" action="reply_review.php" style="min-width:180px;">';
                                echo '<input type="hidden" name="id" value="' . $r['id'] . '">';
                                echo '<textarea name="reply" class="form-control mb-2" rows="2" placeholder="Type reply..." required></textarea>';
                                echo '<button type="submit" class="btn btn-primary btn-sm">Send Reply</button>';
                                echo '</form>';
                            } else {
                                echo '<span class="badge bg-success">Replied</span>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<div class="alert alert-secondary">No reviews or queries found.</div>';
                    }
                    ?>
                </div>
                <!-- Results tab removed per request. Use the public results page. -->
            </div>
        </section>
    </main>
    <footer class="secure-footer mt-5 py-3">
        <img src="../../assets/images/election-logo.jpg" alt="Election Logo" class="logo mb-2">
        <div>© 2025 Democratic Socialist Republic of Sri Lanka - Presidential Election. All Rights Reserved.</div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart.js Results Example
        const ctx = document.getElementById('resultsChart').getContext('2d');
        const resultsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Candidate 1', 'Candidate 2', 'Candidate 3'],
                datasets: [{
                    label: 'Votes',
                    data: [120, 90, 60],
                    backgroundColor: [
                        'rgba(141, 21, 58, 0.8)',
                        'rgba(0, 106, 78, 0.8)',
                        'rgba(255, 128, 0, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: true, text: 'Election Results (Sample)' }
                }
            }
        });
    </script>
    <script>
        function formatDuration(totalSeconds) {
            var abs = Math.abs(totalSeconds);
            var d = Math.floor(abs / 86400);
            var h = Math.floor((abs % 86400) / 3600);
            var m = Math.floor((abs % 3600) / 60);
            var s = abs % 60;
            return (d > 0 ? d + 'd ' : '') + (h > 0 ? h + 'h ' : '') + (m > 0 ? m + 'm ' : '') + s + 's';
        }
        function updateCountdowns() {
                var now = new Date();
            
            // Update election status badges and text dynamically
            document.querySelectorAll('[data-election-status]').forEach(function(statusCell) {
                var start = new Date((statusCell.getAttribute('data-start') || '').replace(' ', 'T'));
                var end = new Date((statusCell.getAttribute('data-end') || '').replace(' ', 'T'));
                if (isNaN(start.getTime()) || isNaN(end.getTime())) return;
                
                var statusBadge = statusCell.querySelector('.badge');
                var mt1Div = statusCell.querySelector('.mt-1');
                var progressContainer = statusCell.querySelector('.progress');
                var progressLabel = statusCell.querySelector('.small.text-muted');
                
                if (now < start) {
                    // Not Started
                    if (statusBadge && statusBadge.textContent !== 'Not Started') {
                        statusBadge.className = 'badge bg-info text-dark';
                        statusBadge.textContent = 'Not Started';
                    }
                    // Update countdown for "Starts in"
                    if (mt1Div) {
                        var diffSeconds = Math.floor((start - now) / 1000);
                        var timeText = diffSeconds > 0 ? formatDuration(diffSeconds) : '0s';
                        mt1Div.innerHTML = '<span class="badge bg-info text-dark">Starts in ' + timeText + '</span>';
                    }
                    if (progressContainer) progressContainer.remove();
                    if (progressLabel) progressLabel.remove();
                } else if (now >= start && now <= end) {
                    // Voting Open
                    if (statusBadge && statusBadge.textContent !== 'Voting Open') {
                        statusBadge.className = 'badge bg-success';
                        statusBadge.textContent = 'Voting Open';
                    }
                    // Update countdown for "Ends in"
                    if (mt1Div) {
                        var diffSeconds = Math.floor((end - now) / 1000);
                        var timeText = diffSeconds > 0 ? formatDuration(diffSeconds) : '0s';
                        mt1Div.innerHTML = '<span class="badge bg-success">Ends in ' + timeText + '</span>';
                    }
                    // Add progress bar if it doesn't exist
                    if (!progressContainer) {
                        var totalWindow = Math.max(1, (end - start));
                        var elapsed = Math.max(0, now - start);
                        var pct = Math.floor((elapsed / totalWindow) * 100);
                        var progressHtml = '<div class="progress mt-1" style="height:6px;"><div class="progress-bar bg-success election-progress" role="progressbar" style="width: ' + pct + '%" aria-valuenow="' + pct + '" aria-valuemin="0" aria-valuemax="100" data-start="' + start.toISOString().replace('T', ' ').substring(0, 19) + '" data-end="' + end.toISOString().replace('T', ' ').substring(0, 19) + '"></div></div><div class="small text-muted">' + pct + '% elapsed</div>';
                        statusCell.insertAdjacentHTML('beforeend', progressHtml);
                    } else {
                        // Update existing progress bar in real-time
                        var totalWindow = Math.max(1, (end - start));
                        var elapsed = Math.max(0, now - start);
                        var pct = Math.floor((elapsed / totalWindow) * 100);
                        var existingBar = progressContainer.querySelector('.election-progress');
                        if (existingBar) {
                            existingBar.style.width = pct + '%';
                            existingBar.setAttribute('aria-valuenow', pct);
                        }
                        if (progressLabel) {
                            progressLabel.textContent = pct + '% elapsed';
                        }
                    }
                } else {
                    // Voting Closed
                    if (statusBadge && statusBadge.textContent !== 'Voting Closed') {
                        statusBadge.className = 'badge bg-secondary';
                        statusBadge.textContent = 'Voting Closed';
                    }
                    // Update countdown for "Ended X ago"
                    if (mt1Div) {
                        var diffSeconds = Math.floor((now - end) / 1000);
                        var timeText = diffSeconds > 0 ? formatDuration(diffSeconds) : '0s';
                        mt1Div.innerHTML = '<span class="badge bg-secondary">Ended ' + timeText + ' ago</span>';
                    }
                    if (progressContainer) progressContainer.remove();
                    if (progressLabel) progressLabel.remove();
                }
            });
            
            // Update election progress bars (backup method for any existing bars)
            document.querySelectorAll('.election-progress').forEach(function(bar) {
                var start = new Date((bar.getAttribute('data-start') || '').replace(' ', 'T'));
                var end = new Date((bar.getAttribute('data-end') || '').replace(' ', 'T'));
                if (isNaN(start.getTime()) || isNaN(end.getTime()) || now < start || now > end) return;
                var total = Math.max(1, (end - start));
                var elapsed = Math.max(0, now - start);
                var pct = Math.floor((elapsed / total) * 100);
                bar.style.width = pct + '%';
                bar.setAttribute('aria-valuenow', pct);
                var label = bar.parentElement && bar.parentElement.nextElementSibling;
                if (label && label.classList.contains('text-muted')) label.textContent = pct + '% elapsed';
            });
        }
        setInterval(updateCountdowns, 1000);
        updateCountdowns();
        console.log('Election countdown system initialized - updating every second');
        // Inline results removed per request
    </script>
</body>
</html> 