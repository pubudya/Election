<?php
require_once __DIR__ . '/../../src/middleware/CSRF.php';
require_once __DIR__ . '/../../src/models/Candidate.php';
$activePage = '';
$hideNav = true;
$hideFlagBar = true;
$hideOfficialPortal = true;
include '../../public/header.php';
$candidates = Candidate::getAll();
$lang = $_SESSION['lang'] ?? 'en';

// Check if voter is logged in
if (!isset($_SESSION['voter_id']) || !isset($_SESSION['voter_nic'])) {
    header('Location: ../../public/login.php?error=' . urlencode('Please login to access the voter dashboard.'));
    exit;
}

// Check if voter has already voted
require_once __DIR__ . '/../../src/models/Database.php';
$db = new DatabaseModel();
$stmt = $db->prepare('SELECT has_voted FROM voters WHERE voter_id = ?');
$stmt->execute([$_SESSION['voter_id']]);
$voter = $stmt->fetch();

// Fetch current active election by time
$election = $db->query("SELECT * FROM election_config WHERE start_date <= NOW() AND end_date >= NOW() LIMIT 1")->fetch();
$hasVoted = false;
$electionId = null;
if ($election) {
    $electionId = $election['config_id'];
    // Check if voter has already voted in this election
    $stmt = $db->prepare('SELECT vote_id FROM votes WHERE voter_id = ? AND election_id = ?');
    $stmt->execute([$_SESSION['voter_id'], $electionId]);
    $hasVoted = $stmt->rowCount() > 0;
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tr['voter_dashboard_title'] ?> - <?= $tr['presidential_election'] ?></title>
    <link rel="icon" href="../../assets/images/sri-lanka-flag.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/sri-lanka-theme.css">
    <style>
        .emblem { width: 80px; height: auto; margin-bottom: 8px; }
        .dashboard-section { background: var(--sl-card); border-radius: 18px; box-shadow: var(--sl-shadow); padding: 40px 32px 32px 32px; max-width: 800px; margin: 40px auto; }
        .candidate-card { border: 2px solid var(--sl-gold); border-radius: 10px; margin-bottom: 18px; transition: box-shadow 0.2s; cursor: pointer; }
        .candidate-card.selected { box-shadow: 0 0 0 4px var(--sl-green); border-color: var(--sl-green); }
        .candidate-img { width: 60px; height: 60px; object-fit: cover; border-radius: 50%; }
        .lang-btn { margin: 0 4px; font-weight: 500; }
        .summary-list { font-size: 1.1rem; }
        .welcome-message { background: var(--sl-blue); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="flag-bar"></div>
    <header class="secure-header text-center py-4 mb-3">
        <img src="../../assets/images/election-logo.jpg" alt="Sri Lanka Emblem" class="emblem">
        <h1 class="mb-1">Democratic Socialist Republic of Sri Lanka</h1>
        <h2 class="mb-0"><?= $tr['voter_dashboard_subtitle'] ?></h2>
    </header>
    <main>
        <section class="dashboard-section">
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <div class="welcome-message">
                <h4><?= $tr['welcome'] ?>, <?php echo htmlspecialchars($_SESSION['voter_name'] ?? 'Voter'); ?>!</h4>
                <p><?= $tr['nic'] ?>: <?php echo htmlspecialchars($_SESSION['voter_nic']); ?></p>
                <div class="d-flex justify-content-between align-items-center">
                    <span><?= $tr['please_cast_vote'] ?></span>
                    <a href="../../src/controllers/AuthController.php?action=logout" class="btn btn-outline-light btn-sm"><?= $tr['logout'] ?></a>
                </div>
            </div>
            <?php if (!$election): ?>
                <div class="alert alert-warning text-center"><?= $tr['no_election_started'] ?></div>
            <?php elseif ($hasVoted): ?>
                <div class="alert alert-success text-center">You have already voted in this election.</div>
            <?php else: ?>
                <div class="alert alert-info text-center mb-4">
                    <strong>Election:</strong> <?php echo htmlspecialchars($election['election_name'] ?? 'Presidential Election'); ?><br>
                    <strong>Voting Period:</strong> <?php echo htmlspecialchars($election['start_date']); ?> to <?php echo htmlspecialchars($election['end_date']); ?>
                </div>
                <div class="text-center">
                    <a href="../../public/voting.php" class="btn btn-success btn-lg">Vote Now</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <footer class="secure-footer mt-5 py-3">
        <img src="../../assets/images/election-logo.jpg" alt="Election Logo" class="logo mb-2">
        <div>© 2025 Democratic Socialist Republic of Sri Lanka - Presidential Election. All Rights Reserved.</div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Candidates from DB
        const candidates = <?php
            $jsCandidates = [];
            foreach ($candidates as $c) {
                $jsCandidates[] = [
                    'id' => (int)$c['candidate_id'],
                    'name' => [
                        'si' => $c['full_name'],
                        'ta' => $c['full_name'],
                        'en' => $c['full_name']
                    ],
                    'party' => $c['party'],
                    'img' => !empty($c['photo_path']) ? $c['photo_path'] : '../../assets/images/election-logo.jpg'
                ];
            }
            echo json_encode($jsCandidates, JSON_UNESCAPED_UNICODE);
        ?>;
        console.log('Candidates from PHP:', candidates);
        let lang = '<?php echo $lang; ?>';
        let pref1 = null, pref2 = null, pref3 = null;
        function setLang(l) {
            lang = l;
            renderCandidates();
        }
        function renderCandidates() {
            // 1st Preference
            const pref1List = document.getElementById('pref1List');
            pref1List.innerHTML = '';
            candidates.forEach(c => {
                const card = document.createElement('div');
                card.className = 'col-md-4';
                card.innerHTML = `<div class='candidate-card card p-2 text-center ${pref1===c.id?'selected':''}' onclick='selectPref(1,${c.id})'>
                    <img src='${c.img}' class='candidate-img mb-2'>
                    <h5>${c.name[lang]}</h5>
                    <div>${c.party}</div>
                </div>`;
                pref1List.appendChild(card);
            });
            // 2nd Preference
            const pref2List = document.getElementById('pref2List');
            pref2List.innerHTML = '';
            candidates.filter(c=>c.id!==pref1).forEach(c => {
                const card = document.createElement('div');
                card.className = 'col-md-4';
                card.innerHTML = `<div class='candidate-card card p-2 text-center ${pref2===c.id?'selected':''}' onclick='selectPref(2,${c.id})'>
                    <img src='${c.img}' class='candidate-img mb-2'>
                    <h5>${c.name[lang]}</h5>
                    <div>${c.party}</div>
                </div>`;
                pref2List.appendChild(card);
            });
            // 3rd Preference
            const pref3List = document.getElementById('pref3List');
            pref3List.innerHTML = '';
            candidates.filter(c=>c.id!==pref1&&c.id!==pref2).forEach(c => {
                const card = document.createElement('div');
                card.className = 'col-md-4';
                card.innerHTML = `<div class='candidate-card card p-2 text-center ${pref3===c.id?'selected':''}' onclick='selectPref(3,${c.id})'>
                    <img src='${c.img}' class='candidate-img mb-2'>
                    <h5>${c.name[lang]}</h5>
                    <div>${c.party}</div>
                </div>`;
                pref3List.appendChild(card);
            });
            // Summary
            const summary = document.getElementById('summary');
            summary.innerHTML = '';
            if(pref1) summary.innerHTML += `<li>1st: ${candidates.find(c=>c.id===pref1).name[lang]}</li>`;
            if(pref2) summary.innerHTML += `<li>2nd: ${candidates.find(c=>c.id===pref2).name[lang]}</li>`;
            if(pref3) summary.innerHTML += `<li>3rd: ${candidates.find(c=>c.id===pref3).name[lang]}</li>`;
            // Enable submit if at least 1st preference is selected
            document.getElementById('submitVoteBtn').disabled = !pref1;
        }
        function selectPref(pref, id) {
            if(pref===1) { pref1=id; pref2=null; pref3=null; }
            else if(pref===2) { pref2=id; pref3=null; }
            else if(pref===3) { pref3=id; }
            renderCandidates();
        }
        function refreshVote() {
            pref1 = null; pref2 = null; pref3 = null;
            renderCandidates();
        }
        // Render candidates on initial page load
        renderCandidates();
    </script>
    <?php if ($election): ?>
    <!-- Voting countdown timer -->
    <script>
    (function() {
        const end = new Date('<?php echo $election['end_date']; ?>').getTime();
        const timerEl = document.getElementById('votingTimer');
        function updateTimer() {
            const now = new Date().getTime();
            let diff = Math.floor((end - now) / 1000);
            if (diff < 0) diff = 0;
            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            timerEl.textContent = `<?= $tr['time_left'] ?>: ${h}h ${m}m ${s}s`;
            if (diff === 0) {
                timerEl.textContent = '<?= $tr['voting_ended'] ?>';
                document.getElementById('submitVoteBtn').disabled = true;
            }
        }
        updateTimer();
        setInterval(updateTimer, 1000);
    })();
    </script>
    <?php endif; ?>
</body>
</html> 