<?php
session_start();
require_once __DIR__ . '/../models/Database.php';

class ResultsCalculator {
    private $db;
    public function __construct() {
        $this->db = new DatabaseModel();
    }
    public function calculateResults($electionId = null) {
        if ($electionId === null) {
            $election = $this->db->query("SELECT * FROM election_config WHERE end_date < NOW() ORDER BY end_date DESC LIMIT 1")->fetch();
        } else {
            $election = $this->db->query("SELECT * FROM election_config WHERE config_id = ?", [$electionId])->fetch();
        }
        if (!$election) {
            return ['success' => false, 'error' => 'No election ready for results'];
        }
        try {
            $this->db->beginTransaction();
            $candidates = $this->db->query("SELECT candidate_id, full_name, party, photo_path FROM candidates WHERE is_active = TRUE ORDER BY candidate_number")->fetchAll();
            if (count($candidates) < 2) throw new Exception('Not enough candidates for election');
            $votes = $this->db->query("SELECT first_preference, second_preference, third_preference FROM votes WHERE is_valid = TRUE AND election_id = ?", [$election['config_id']])->fetchAll();
            $totalVotes = count($votes);
            if ($totalVotes == 0) throw new Exception('No votes recorded for this election');
            $firstPrefCounts = array_fill_keys(array_column($candidates, 'candidate_id'), 0);
            foreach ($votes as $vote) $firstPrefCounts[$vote['first_preference']]++;
            $winner = null;
            foreach ($firstPrefCounts as $candidateId => $count) {
                if ($count > ($totalVotes / 2)) {
                    $winner = $candidateId;
                    break;
                }
            }
            $rounds = [];
            $rounds[1] = $firstPrefCounts;
            if (!$winner) {
                $winner = $this->redistributeVotes($candidates, $votes, $firstPrefCounts, $rounds);
            }
            $this->db->prepare("UPDATE election_config SET results_published = TRUE WHERE config_id = ?")->execute([$election['config_id']]);
            $this->db->commit();
            return [
                'success' => true,
                'winner' => $winner,
                'total_votes' => $totalVotes,
                'rounds' => $rounds,
                'candidates' => $candidates
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    private function redistributeVotes($candidates, $votes, $firstPrefCounts, &$rounds) {
        $totalVotes = count($votes);
        $candidateIds = array_column($candidates, 'candidate_id');
        $activeCandidates = array_fill_keys($candidateIds, true);
        $roundNumber = 2;
        while (count($activeCandidates) > 2) {
            $minVotes = PHP_INT_MAX;
            $eliminated = null;
            foreach ($firstPrefCounts as $candidateId => $count) {
                if (isset($activeCandidates[$candidateId])) {
                    if ($count < $minVotes) {
                        $minVotes = $count;
                        $eliminated = $candidateId;
                    }
                }
            }
            if (!$eliminated) throw new Exception('Cannot determine candidate to eliminate');
            unset($activeCandidates[$eliminated]);
            $redistributedCounts = array_fill_keys($candidateIds, 0);
            foreach ($votes as $vote) {
                if ($vote['first_preference'] == $eliminated) {
                    if ($vote['second_preference'] && isset($activeCandidates[$vote['second_preference']])) {
                        $redistributedCounts[$vote['second_preference']]++;
                    } elseif ($vote['third_preference'] && isset($activeCandidates[$vote['third_preference']])) {
                        $redistributedCounts[$vote['third_preference']]++;
                    }
                }
            }
            foreach ($activeCandidates as $candidateId => $_) {
                $firstPrefCounts[$candidateId] += $redistributedCounts[$candidateId];
            }
            $rounds[$roundNumber] = $firstPrefCounts;
            $roundNumber++;
            foreach ($firstPrefCounts as $candidateId => $count) {
                if (isset($activeCandidates[$candidateId]) && $count > ($totalVotes / 2)) {
                    return $candidateId;
                }
            }
        }
        $maxVotes = 0;
        $winner = null;
        foreach ($firstPrefCounts as $candidateId => $count) {
            if (isset($activeCandidates[$candidateId]) && $count > $maxVotes) {
                $maxVotes = $count;
                $winner = $candidateId;
            }
        }
        return $winner;
    }
}

// Output results as HTML (for results.php)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $json = isset($_GET['json']) && $_GET['json'] == '1';
    $electionId = $_GET['election_id'] ?? null;
    $type = $_GET['type'] ?? 'all';
    $value = $_GET['value'] ?? '';
    $db = new DatabaseModel();
    // For filters
    $provinces = array_map('current', $db->query('SELECT DISTINCT province FROM voters')->fetchAll());
    $districts = array_map('current', $db->query('SELECT DISTINCT district FROM voters')->fetchAll());
    $gn_divisions = array_map('current', $db->query('SELECT DISTINCT division FROM voters')->fetchAll());
    $where = '';
    $params = [];
    if ($type === 'province' && $value) {
        $where = ' AND v.province = ? ';
        $params[] = $value;
    } elseif ($type === 'district' && $value) {
        $where = ' AND v.district = ? ';
        $params[] = $value;
    } elseif ($type === 'gn' && $value) {
        $where = ' AND v.division = ? ';
        $params = [$value];
    }
    // Filter votes and candidates by region
    // Determine election for filtering
    if ($electionId === null) {
        $electionRow = $db->query("SELECT config_id FROM election_config WHERE results_published = 1 ORDER BY end_date DESC LIMIT 1")->fetch();
        if ($electionRow) { $electionId = $electionRow['config_id']; }
    }
    $electionFilterSql = '';
    if ($electionId) {
        $electionFilterSql = ' AND vt.election_id = ? ';
        $params[] = $electionId;
    }
    $votes = $db->query(
        "SELECT vt.first_preference, vt.second_preference, vt.third_preference FROM votes vt JOIN voters v ON vt.voter_id = v.voter_id WHERE vt.is_valid = TRUE $where $electionFilterSql",
        $params
    )->fetchAll();
    $candidateIds = [];
    foreach ($votes as $vote) {
        foreach (['first_preference','second_preference','third_preference'] as $pref) {
            if ($vote[$pref]) $candidateIds[$vote[$pref]] = true;
        }
    }
    if ($candidateIds) {
        $candidates = $db->query(
            "SELECT candidate_id, full_name, party, photo_path FROM candidates WHERE is_active = TRUE AND candidate_id IN (" . implode(',', array_keys($candidateIds)) . ")"
        )->fetchAll();
    } else {
        $candidates = $db->query("SELECT candidate_id, full_name, party, photo_path FROM candidates WHERE is_active = TRUE")->fetchAll();
    }
    // Patch ResultsCalculator to accept custom votes/candidates
    class RegionResultsCalculator extends ResultsCalculator {
        public function calculateRegionResults($candidates, $votes) {
            $totalVotes = count($votes);
            if ($totalVotes == 0) return [ 'success' => false, 'error' => 'No votes recorded for this region', 'candidates' => $candidates, 'rounds' => [], 'winner' => null, 'preference_counts' => [] ];
            $firstPrefCounts = array_fill_keys(array_column($candidates, 'candidate_id'), 0);
            $secondPrefCounts = array_fill_keys(array_column($candidates, 'candidate_id'), 0);
            $thirdPrefCounts = array_fill_keys(array_column($candidates, 'candidate_id'), 0);
            foreach ($votes as $vote) {
                if ($vote['first_preference']) $firstPrefCounts[$vote['first_preference']]++;
                if ($vote['second_preference']) $secondPrefCounts[$vote['second_preference']]++;
                if ($vote['third_preference']) $thirdPrefCounts[$vote['third_preference']]++;
            }
            $winner = null;
            foreach ($firstPrefCounts as $candidateId => $count) {
                if ($count > ($totalVotes / 2)) {
                    $winner = $candidateId;
                    break;
                }
            }
            $rounds = [];
            $rounds[1] = $firstPrefCounts;
            if (!$winner) {
                $winner = $this->redistributeVotes($candidates, $votes, $firstPrefCounts, $rounds);
            }
            return [
                'success' => true,
                'winner' => $winner,
                'total_votes' => $totalVotes,
                'rounds' => $rounds,
                'candidates' => $candidates,
                'preference_counts' => [
                    'first' => $firstPrefCounts,
                    'second' => $secondPrefCounts,
                    'third' => $thirdPrefCounts
                ]
            ];
        }
    }
    $calc = new RegionResultsCalculator();
    $results = $calc->calculateRegionResults($candidates, $votes);
    if ($json) {
        if ($electionId) {
            $election = $db->query('SELECT * FROM election_config WHERE config_id = ?', [$electionId])->fetch();
        } else {
            $election = $db->query('SELECT * FROM election_config WHERE results_published = 1 ORDER BY end_date DESC LIMIT 1')->fetch();
        }
        if (!$election) {
            http_response_code(404);
            echo json_encode(['error' => 'Election not found']);
            exit;
        }
        $response = [
            'success' => $results['success'],
            'winner' => $results['winner'],
            'total_votes' => $results['total_votes'],
            'rounds' => $results['rounds'],
            'candidates' => $results['candidates'],
            'preference_counts' => $results['preference_counts'],
            'results_published' => (bool)$election['results_published'],
            'canPublish' => ($election['end_date'] <= date('Y-m-d H:i:s')),
            'election_id' => $election['config_id'],
        ];
        $response['provinces'] = $provinces;
        $response['districts'] = $districts;
        $response['gn_divisions'] = $gn_divisions;
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        if ($results['success']) {
            echo '<h3>Total Votes: ' . $results['total_votes'] . '</h3>';
            echo '<table class="table table-bordered"><thead><tr><th>Candidate</th><th>Party</th><th>Votes (Final)</th></tr></thead><tbody>';
            foreach ($results['candidates'] as $c) {
                $finalVotes = end($results['rounds'])[ $c['candidate_id'] ] ?? 0;
                echo '<tr><td>' . htmlspecialchars($c['full_name']) . '</td><td>' . htmlspecialchars($c['party']) . '</td><td>' . $finalVotes . '</td></tr>';
            }
            echo '</tbody></table>';
            echo '<h4>Winner: ' . htmlspecialchars($results['winner']) . '</h4>';
        } else {
            echo '<div class="alert alert-danger">' . htmlspecialchars($results['error']) . '</div>';
        }
        exit;
    }
} 