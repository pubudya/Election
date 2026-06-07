<?php
require_once __DIR__ . '/../src/models/Database.php';
$db = new DatabaseModel();
$requestedId = $_GET['election_id'] ?? null;
if ($requestedId) {
    $election = $db->query("SELECT * FROM election_config WHERE config_id = ?", [$requestedId])->fetch();
} else {
    $election = $db->query("SELECT * FROM election_config WHERE results_published = 1 ORDER BY end_date DESC LIMIT 1")->fetch();
}
$now = date('Y-m-d H:i:s');
if (!$election) {
    header('Location: admin/index.php?error=' . urlencode('Election not found.'));
    exit;
}
// Allow preview before publish if admin links with election_id, but block if future end time and no preview intent
if ($now < $election['end_date'] && !$election['results_published']) {
    // Show a lightweight preview page instead of redirecting
    // Keep same layout below; the JS will still fetch counts; mark banner
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Election Results - Presidential Election</title>
        <link rel="icon" href="../assets/images/election-logo.jpg">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="../assets/css/sri-lanka-theme.css">
        <style>
            .emblem { width: 80px; height: auto; margin-bottom: 8px; }
            .results-section { background: var(--sl-card); border-radius: 18px; box-shadow: var(--sl-shadow); padding: 28px; max-width: 1100px; margin: 24px auto; }
            .results-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start; }
            @media (max-width: 992px) { .results-grid { grid-template-columns: 1fr; } }
            #resultsChart { max-height: 320px; }
            #resultsTable table { background: #fff; border-radius: 10px; overflow: hidden; }
            #winnerBox { position: sticky; bottom: 0; background: transparent; }
        </style>
    </head>
    <body>
        <div class="flag-bar"></div>
        <header class="secure-header text-center py-4 mb-3">
            <img src="../assets/images/election-logo.jpg" alt="Sri Lanka Emblem" class="emblem">
            <h1 class="mb-1">Democratic Socialist Republic of Sri Lanka</h1>
            <h2 class="mb-0">Presidential Election - Results</h2>
        </header>
        <main>
            <section class="results-section">
                <h3 class="mb-4">Final Results</h3>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="filterType" class="form-label">Filter by:</label>
                        <select id="filterType" class="form-select mb-2">
                            <option value="all">All Island</option>
                            <option value="province">Province</option>
                            <option value="district">District</option>
                            <option value="gn">GN Division</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="filterValueCol" style="display:none;">
                        <label for="filterValue" class="form-label" id="filterValueLabel"></label>
                        <select id="filterValue" class="form-select mb-2"></select>
                    </div>
                </div>
                <div class="results-grid">
                    <div><canvas id="resultsChart"></canvas></div>
                    <div id="resultsTable"></div>
                </div>
                <div class="mt-4" id="winnerBox"></div>
            </section>
        </main>
        <footer class="secure-footer mt-5 py-3">
            <img src="../assets/images/election-logo.jpg" alt="Election Logo" class="logo mb-2">
            <div>© 2025 Democratic Socialist Republic of Sri Lanka - Presidential Election. All Rights Reserved.</div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let chart;
            let allResults;
            let allProvinces = [];
            let allDistricts = [];
            // Fetch all results as JSON
            function fetchResults(filterType = 'all', filterValue = '') {
                const eid = new URLSearchParams(window.location.search).get('election_id') || '';
                let url = '../src/controllers/ResultsController.php?json=1&type=' + filterType + '&value=' + encodeURIComponent(filterValue) + (eid ? ('&election_id=' + encodeURIComponent(eid)) : '');
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        allResults = data;
                        renderResults(data);
                        if (data.provinces) allProvinces = data.provinces;
                        if (data.districts) allDistricts = data.districts;
                        if (data.gn_divisions) allGNDivisions = data.gn_divisions;
                    });
            }
            function renderResults(data) {
                // Chart
                const ctx = document.getElementById('resultsChart').getContext('2d');
                const labels = data.candidates.map(c => c.full_name);
                const votes = data.candidates.map(c => data.rounds ? (Object.values(data.rounds).pop()[c.candidate_id] || 0) : 0);
                if (chart) chart.destroy();
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Votes',
                            data: votes,
                            backgroundColor: [
                                'rgba(141, 21, 58, 0.8)',
                                'rgba(0, 106, 78, 0.8)',
                                'rgba(255, 128, 0, 0.8)',
                                'rgba(0, 51, 102, 0.8)',
                                'rgba(255, 215, 0, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            title: { display: true, text: 'Election Results' }
                        }
                    }
                });
                // Table
                let table = '<table class="table table-bordered"><thead><tr><th>Candidate</th><th>Party</th><th>1st Pref</th><th>2nd Pref</th><th>3rd Pref</th><th>Votes (Final)</th></tr></thead><tbody>';
                data.candidates.forEach(c => {
                    let finalVotes = data.rounds ? (Object.values(data.rounds).pop()[c.candidate_id] || 0) : 0;
                    let first = data.preference_counts?.first?.[c.candidate_id] || 0;
                    let second = data.preference_counts?.second?.[c.candidate_id] || 0;
                    let third = data.preference_counts?.third?.[c.candidate_id] || 0;
                    table += `<tr><td>${c.full_name}</td><td>${c.party}</td><td>${first}</td><td>${second}</td><td>${third}</td><td>${finalVotes}</td></tr>`;
                });
                table += '</tbody></table>';
                document.getElementById('resultsTable').innerHTML = table;
                // Winner
                let winnerName = data.candidates.find(c => c.candidate_id == data.winner)?.full_name || 'N/A';
                document.getElementById('winnerBox').innerHTML = `<div class="alert alert-success"><strong>Winner: </strong>${winnerName}</div>`;
            }
            // Province/District/GN filter logic
            let allGNDivisions = [];
            document.getElementById('filterType').addEventListener('change', function() {
                const type = this.value;
                const filterValueCol = document.getElementById('filterValueCol');
                const filterValue = document.getElementById('filterValue');
                const filterValueLabel = document.getElementById('filterValueLabel');
                if (type === 'province') {
                    filterValueCol.style.display = '';
                    filterValueLabel.textContent = 'Select Province:';
                    filterValue.innerHTML = allProvinces.map(p => `<option value="${p}">${p}</option>`).join('');
                    fetchResults('province', allProvinces[0]);
                } else if (type === 'district') {
                    filterValueCol.style.display = '';
                    filterValueLabel.textContent = 'Select District:';
                    filterValue.innerHTML = allDistricts.map(d => `<option value="${d}">${d}</option>`).join('');
                    fetchResults('district', allDistricts[0]);
                } else if (type === 'gn') {
                    filterValueCol.style.display = '';
                    filterValueLabel.textContent = 'Select GN Division:';
                    filterValue.innerHTML = allGNDivisions.map(g => `<option value="${g}">${g}</option>`).join('');
                    fetchResults('gn', allGNDivisions[0]);
                } else {
                    filterValueCol.style.display = 'none';
                    fetchResults('all', '');
                }
            });
            document.getElementById('filterValue').addEventListener('change', function() {
                const type = document.getElementById('filterType').value;
                fetchResults(type, this.value);
            });
            // Initial fetch
            fetchResults();
        </script>
    </body>
    </html>
<?php
?> 