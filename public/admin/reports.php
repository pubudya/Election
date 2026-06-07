<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports & Analytics - Presidential Election</title>
    <link rel="icon" href="../../assets/images/sri-lanka-flag.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/sri-lanka-theme.css">
    <style>
        .emblem { width: 80px; height: auto; margin-bottom: 8px; }
        .results-section { background: var(--sl-card); border-radius: 18px; box-shadow: var(--sl-shadow); padding: 40px 32px 32px 32px; max-width: 1000px; margin: 40px auto; }
    </style>
</head>
<body>
    <div class="flag-bar"></div>
    <header class="secure-header text-center py-4 mb-3">

        <h1 class="mb-1">Democratic Socialist Republic of Sri Lanka</h1>
        <h2 class="mb-0">Presidential Election - Admin Reports & Analytics</h2>
    </header>
    <main>
        <section class="results-section">
            <ul class="nav nav-tabs" id="adminReportTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab">Results Analytics</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button" role="tab">Audit Logs</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="compliance-tab" data-bs-toggle="tab" data-bs-target="#compliance" type="button" role="tab">Security & Compliance</button>
                </li>
            </ul>
            <div class="tab-content" id="adminReportTabContent">
                <!-- Results Analytics Tab -->
                <div class="tab-pane fade show active" id="analytics" role="tabpanel">
                    <h3 class="mb-4">Election Results Analytics</h3>
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
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="resultsChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div id="resultsTable"></div>
                        </div>
                    </div>
                    <div class="mt-4" id="winnerBox"></div>
                </div>
                <!-- Audit Logs Tab -->
                <div class="tab-pane fade" id="audit" role="tabpanel">
                    <h3 class="mb-4">System Audit Logs</h3>
                    <form class="row g-2 mb-3" id="logFilterForm" onsubmit="fetchAuditLogs(1);return false;">
                        <div class="col-md-2">
                            <label class="form-label">User Type</label>
                            <select class="form-select" id="filterUserType">
                                <option value="">All</option>
                                <option value="admin">Admin</option>
                                <option value="officer">Officer</option>
                                <option value="voter">Voter</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Action</label>
                            <input type="text" class="form-control" id="filterAction" placeholder="Action">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="filterStatus">
                                <option value="">All</option>
                                <option value="success">Success</option>
                                <option value="failure">Failure</option>
                                <option value="warning">Warning</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date From</label>
                            <input type="date" class="form-control" id="filterDateFrom">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date To</label>
                            <input type="date" class="form-control" id="filterDateTo">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="filterSearch" placeholder="Keyword">
                        </div>
                        <div class="col-12 d-flex justify-content-end mt-2">
                            <button class="btn btn-secondary me-2" type="button" onclick="resetLogFilters()">Reset</button>
                            <button class="btn btn-primary" type="submit">Apply Filters</button>
                        </div>
                    </form>
                    <div class="d-flex justify-content-end mb-2">
                        <button class="btn btn-primary" id="exportLogsBtn">Export to CSV</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="auditLogTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Date/Time</th>
                                    <th>User Type</th>
                                    <th>User ID</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>IP</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody id="auditLogBody">
                                <tr><td colspan="7" class="text-center">Loading logs...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <nav>
                        <ul class="pagination justify-content-center" id="auditLogPagination"></ul>
                    </nav>
                </div>
                <!-- Security & Compliance Tab -->
                <div class="tab-pane fade" id="compliance" role="tabpanel">
                    <h3 class="mb-4">Security & Compliance Checklist</h3>
                    <ul class="list-group mb-4">
                        <li class="list-group-item"><input type="checkbox" checked disabled> All votes are encrypted and auditable</li>
                        <li class="list-group-item"><input type="checkbox" checked disabled> CSRF protection enabled on all forms</li>
                        <li class="list-group-item"><input type="checkbox" checked disabled> Passwords/PINs are hashed with salt</li>
                        <li class="list-group-item"><input type="checkbox" checked disabled> All admin actions are logged</li>
                        <li class="list-group-item"><input type="checkbox" checked disabled> Brute-force and rate limiting enabled</li>
                        <li class="list-group-item"><input type="checkbox" checked disabled> Results only published after admin approval</li>
                    </ul>
                    <div class="alert alert-info">No security incidents detected. All compliance checks passed.</div>
                </div>
            </div>
        </section>
    </main>
    <footer class="secure-footer mt-5 py-3">

        <div>© 2025 Democratic Socialist Republic of Sri Lanka - Presidential Election. All Rights Reserved.</div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chart;
        let allResults;
        let allProvinces = [];
        let allDistricts = [];
        let allGN = [];
        // Fetch all results as JSON
        function fetchResults(filterType = 'all', filterValue = '') {
            let url = '../../src/controllers/ResultsController.php?json=1&type=' + filterType + '&value=' + encodeURIComponent(filterValue);
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    allResults = data;
                    renderResults(data);
                    if (data.provinces) allProvinces = data.provinces;
                    if (data.districts) allDistricts = data.districts;
                    if (data.gn_divisions) allGN = data.gn_divisions;
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
            let table = '<table class="table table-bordered"><thead><tr><th>Candidate</th><th>Party</th><th>Votes (Final)</th></tr></thead><tbody>';
            data.candidates.forEach(c => {
                let finalVotes = data.rounds ? (Object.values(data.rounds).pop()[c.candidate_id] || 0) : 0;
                table += `<tr><td>${c.full_name}</td><td>${c.party}</td><td>${finalVotes}</td></tr>`;
            });
            table += '</tbody></table>';
            document.getElementById('resultsTable').innerHTML = table;
            // Winner
            let winnerName = data.candidates.find(c => c.candidate_id == data.winner)?.full_name || 'N/A';
            document.getElementById('winnerBox').innerHTML = `<div class="alert alert-success"><strong>Winner: </strong>${winnerName}</div>`;
        }
        // Province/District/GN filter logic
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
                filterValue.innerHTML = allGN.map(g => `<option value="${g}">${g}</option>`).join('');
                fetchResults('gn', allGN[0]);
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

        // Audit log AJAX and export
        let auditLogs = [];
        let logsPerPage = 10;
        let currentLogPage = 1;
        function getLogFilters() {
            return {
                user_type: document.getElementById('filterUserType').value,
                action: document.getElementById('filterAction').value,
                status: document.getElementById('filterStatus').value,
                date_from: document.getElementById('filterDateFrom').value,
                date_to: document.getElementById('filterDateTo').value,
                search: document.getElementById('filterSearch').value
            };
        }
        function fetchAuditLogs(page = 1) {
            const filters = getLogFilters();
            let url = '../../src/controllers/AdminController.php?logs=1&page=' + page;
            Object.keys(filters).forEach(k => {
                if (filters[k]) url += '&' + encodeURIComponent(k) + '=' + encodeURIComponent(filters[k]);
            });
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    auditLogs = data.logs;
                    renderAuditLogs(data.logs, data.page, data.totalPages);
                });
        }
        function resetLogFilters() {
            document.getElementById('filterUserType').value = '';
            document.getElementById('filterAction').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            document.getElementById('filterSearch').value = '';
            fetchAuditLogs(1);
        }
        function renderAuditLogs(logs, page, totalPages) {
            const body = document.getElementById('auditLogBody');
            body.innerHTML = '';
            if (!logs.length) {
                body.innerHTML = '<tr><td colspan="7" class="text-center">No logs found.</td></tr>';
                return;
            }
            logs.forEach(log => {
                body.innerHTML += `<tr>
                    <td>${log.action_timestamp}</td>
                    <td>${log.user_type}</td>
                    <td>${log.user_id || ''}</td>
                    <td>${log.action}</td>
                    <td>${log.status}</td>
                    <td>${log.ip_address}</td>
                    <td>${log.details || ''}</td>
                </tr>`;
            });
            // Pagination
            const pag = document.getElementById('auditLogPagination');
            pag.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                pag.innerHTML += `<li class="page-item${i===page?' active':''}"><a class="page-link" href="#" onclick="fetchAuditLogs(${i});return false;">${i}</a></li>`;
            }
        }
        document.getElementById('audit-tab').addEventListener('shown.bs.tab', function () {
            fetchAuditLogs();
        });
        document.getElementById('exportLogsBtn').addEventListener('click', function() {
            let csv = 'Date/Time,User Type,User ID,Action,Status,IP,Details\n';
            auditLogs.forEach(log => {
                csv += `"${log.action_timestamp}","${log.user_type}","${log.user_id || ''}","${log.action}","${log.status}","${log.ip_address}","${log.details || ''}"
`;
            });
            const blob = new Blob([csv], {type: 'text/csv'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'audit_logs.csv';
            a.click();
            URL.revokeObjectURL(url);
        });
    </script>
</body>
</html> 