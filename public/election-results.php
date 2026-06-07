<?php 
$activePage = 'results'; 
require_once __DIR__ . '/../src/models/Database.php';
$db = new DatabaseModel();

// Fetch the most recent published election results
$election = $db->query("SELECT * FROM election_config WHERE results_published = 1 ORDER BY end_date DESC LIMIT 1")->fetch();

include 'header.php'; 
?>

<main class="container">
    <div class="main-card" id="election-results">
        <h2 class="section-title"><?= $tr['election_results_title'] ?></h2>
        
        <?php if ($election): ?>
            <div class="election-info mb-4">
                <h3 class="text-center mb-3"><?php echo htmlspecialchars($election['election_name']); ?></h3>
                <div class="row text-center">
                    <div class="col-md-6">
                        <strong><?= $tr['start_date'] ?></strong> <?php echo date('F j, Y g:i A', strtotime($election['start_date'])); ?>
                    </div>
                    <div class="col-md-6">
                        <strong><?= $tr['end_date'] ?></strong> <?php echo date('F j, Y g:i A', strtotime($election['end_date'])); ?>
                    </div>
                </div>
            </div>

            <!-- Winner Section with Picture -->
            <div class="winner-section text-center mb-4">
                <h4 class="mb-3"><?= $tr['election_winner'] ?></h4>
                            <div id="winnerDisplay">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2"><?= $tr['loading_winner_information'] ?></p>
                </div>
            </div>

            </div>

            <!-- Total Votes Summary -->
            <div class="votes-summary mb-4">
                <h4 class="mb-3"><?= $tr['total_votes_summary'] ?></h4>
                <div id="votesSummary">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2"><?= $tr['loading_votes_summary'] ?></p>
                    </div>
                </div>
            </div>

            <!-- 1st Preference Votes Chart by Region -->
            <div class="region-chart mb-4">
                <h4 class="mb-3"><?= $tr['first_preference_votes_by_region'] ?></h4>
                <div class="region-selector mb-3">
                    <label for="regionType" class="form-label"><?= $tr['select_region_type'] ?></label>
                    <select id="regionType" class="form-select">
                        <option value="island"><?= $tr['island_wide'] ?></option>
                        <option value="province"><?= $tr['province'] ?></option>
                        <option value="district"><?= $tr['district'] ?></option>
                        <option value="gn"><?= $tr['gn_division'] ?></option>
                    </select>
                </div>
                <div class="region-value-selector mb-3" id="regionValueSelector" style="display: none;">
                    <label for="regionValue" class="form-label" id="regionValueLabel"><?= $tr['select_region'] ?></label>
                    <select id="regionValue" class="form-select"></select>
                </div>
                <div class="chart-container" style="position: relative; height:400px;">
                    <canvas id="regionChart"></canvas>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-info text-center">
                <h4><?= $tr['no_published_results_available'] ?></h4>
                <p><?= $tr['election_results_not_published'] ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Include Chart.js for the results chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let regionChart;
let allResults;
let allProvinces = [];
let allDistricts = [];
let allGNDivisions = [];

// Function to fetch and display election results
async function fetchElectionResults() {
    try {
        const response = await fetch('../src/controllers/ResultsController.php?json=1');
        const data = await response.json();
        
        if (data.success) {
            allResults = data;
            displayWinner(data);
            displayVotesSummary(data);
            createRegionChart('island', '');
            
            // Store region data for filtering
            if (data.provinces) allProvinces = data.provinces;
            if (data.districts) allDistricts = data.districts;
            if (data.gn_divisions) allGNDivisions = data.gn_divisions;
        } else {
            showError('Failed to load results: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error fetching results:', error);
        showError('Failed to load election results. Please try again later.');
    }
}

// Display winner with picture
function displayWinner(data) {
    const winner = data.candidates.find(c => c.candidate_id == data.winner);
    if (winner) {
        console.log('Winner data:', winner);
        console.log('Photo path:', winner.photo_path);
        console.log('Full image URL:', `../assets/images/${winner.photo_path}`);
        const winnerHtml = `
            <div class="winner-card">
                <div class="winner-photo mb-3">
                    ${winner.photo_path ? 
                        `<img src="../assets/images/${winner.photo_path}" alt="${winner.full_name}" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #28a745;">` :
                        `<div class="placeholder-photo rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 150px; height: 150px; background-color: #6c757d; color: white; font-size: 3rem;">
                            <i class="fas fa-user"></i>
                        </div>`
                    }
                </div>
                <h5 class="winner-name text-success">${winner.full_name}</h5>
                <p class="winner-party text-muted">${winner.party}</p>
                <div class="winner-votes">
                    <span class="badge bg-success fs-6">Final Votes: ${data.rounds ? Object.values(data.rounds).pop()[winner.candidate_id] || 0 : 0}</span>
                </div>
            </div>
        `;
        document.getElementById('winnerDisplay').innerHTML = winnerHtml;
    }
}

// Display votes summary
function displayVotesSummary(data) {
    const totalVotes = data.total_votes;
    const totalCandidates = data.candidates.length;
    const winner = data.candidates.find(c => c.candidate_id == data.winner);
    
    const summaryHtml = `
        <div class="row text-center">
            <div class="col-md-4">
                <div class="stat-card bg-primary text-white p-3 rounded">
                    <h5>Total Votes Cast</h5>
                    <h3>${totalVotes}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-success text-white p-3 rounded">
                    <h5>Total Candidates</h5>
                    <h3>${totalCandidates}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-warning text-white p-3 rounded">
                    <h5>Winner</h5>
                    <h6>${winner ? winner.full_name : 'N/A'}</h6>
                </div>
            </div>
        </div>
    `;
    document.getElementById('votesSummary').innerHTML = summaryHtml;
}

// Create region chart with 1st preference votes
function createRegionChart(regionType, regionValue) {
    const ctx = document.getElementById('regionChart').getContext('2d');
    
    if (regionChart) {
        regionChart.destroy();
    }
    
    // Get 1st preference votes for the selected region
    let chartData = getRegionData(regionType, regionValue);
    
    regionChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: '1st Preference Votes',
                data: chartData.votes,
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',   // Green for winner
                    'rgba(13, 110, 253, 0.8)',   // Blue
                    'rgba(255, 193, 7, 0.8)',    // Yellow
                    'rgba(220, 53, 69, 0.8)',    // Red
                    'rgba(108, 117, 125, 0.8)',  // Gray
                    'rgba(102, 16, 242, 0.8)',   // Purple
                    'rgba(253, 126, 20, 0.8)',   // Orange
                    'rgba(25, 135, 84, 0.8)'     // Dark Green
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(13, 110, 253, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)',
                    'rgba(102, 16, 242, 1)',
                    'rgba(253, 126, 20, 1)',
                    'rgba(25, 135, 84, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { 
                    display: true, 
                    text: `1st Preference Votes - ${getRegionTypeLabel(regionType)}${regionValue ? ': ' + regionValue : ''}`,
                    font: { size: 16 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of 1st Preference Votes'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Candidates'
                    }
                }
            }
        }
    });
}

// Get region data for chart
function getRegionData(regionType, regionValue) {
    if (!allResults || !allResults.candidates) {
        return { labels: [], votes: [] };
    }
    
    const labels = allResults.candidates.map(c => c.full_name);
    let votes = [];
    
    if (regionType === 'island') {
        // Island-wide 1st preference votes
        votes = allResults.candidates.map(c => 
            allResults.preference_counts?.first?.[c.candidate_id] || 0
        );
    } else {
        // For specific regions, we'll use the same data for now
        // In a real implementation, you'd fetch region-specific data
        votes = allResults.candidates.map(c => 
            allResults.preference_counts?.first?.[c.candidate_id] || 0
        );
    }
    
    return { labels, votes };
}

// Get region type label
function getRegionTypeLabel(regionType) {
    switch(regionType) {
        case 'island': return 'Island Wide';
        case 'province': return 'Province';
        case 'district': return 'District';
        case 'gn': return 'GN Division';
        default: return 'Region';
    }
}

// Show error message
function showError(message) {
    const errorHtml = `
        <div class="alert alert-danger text-center">
            <h5>Error Loading Results</h5>
            <p>${message}</p>
        </div>
    `;
    document.getElementById('winnerDisplay').innerHTML = errorHtml;
    document.getElementById('votesSummary').innerHTML = errorHtml;
}

// Handle region type change
document.getElementById('regionType').addEventListener('change', function() {
    const regionType = this.value;
    const regionValueSelector = document.getElementById('regionValueSelector');
    const regionValue = document.getElementById('regionValue');
    const regionValueLabel = document.getElementById('regionValueLabel');
    
    if (regionType === 'island') {
        regionValueSelector.style.display = 'none';
        createRegionChart(regionType, '');
    } else {
        regionValueSelector.style.display = 'block';
        
        let options = [];
        let label = '';
        
        switch(regionType) {
            case 'province':
                options = allProvinces;
                label = 'Select Province:';
                break;
            case 'district':
                options = allDistricts;
                label = 'Select District:';
                break;
            case 'gn':
                options = allGNDivisions;
                label = 'Select GN Division:';
                break;
        }
        
        regionValueLabel.textContent = label;
        regionValue.innerHTML = options.map(option => 
            `<option value="${option}">${option}</option>`
        ).join('');
        
        if (options.length > 0) {
            createRegionChart(regionType, options[0]);
        }
    }
});

// Handle region value change
document.getElementById('regionValue').addEventListener('change', function() {
    const regionType = document.getElementById('regionType').value;
    createRegionChart(regionType, this.value);
});

// Load results when page loads
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($election): ?>
        fetchElectionResults();
    <?php endif; ?>
});
</script>

<style>
.winner-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 2rem;
    border-radius: 15px;
    border: 2px solid #28a745;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-card {
    transition: transform 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.stat-card:hover {
    transform: translateY(-5px);
}

.placeholder-photo {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.chart-container {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.winner-name {
    font-size: 1.5rem;
    font-weight: bold;
}

.winner-party {
    font-size: 1.1rem;
}

.winner-votes .badge {
    font-size: 1.1rem;
    padding: 0.5rem 1rem;
}

.region-selector, .region-value-selector {
    max-width: 400px;
    margin: 0 auto;
}
</style>

<?php include 'footer.php'; ?> 