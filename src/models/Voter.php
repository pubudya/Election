<?php
require_once __DIR__ . '/Database.php';
class Voter {
    private $db;
    public function __construct() {
        $this->db = new DatabaseModel();
    }
    public function create($data) {
        // Insert voter
    }
    public function read($id) {
        // Get voter by ID
    }
    public function update($id, $data) {
        // Update voter
    }
    public function delete($id) {
        // Delete voter
    }
    public function findByNIC($nic) {
        // Get voter by NIC
    }
    public static function updatePin($voterId, $pinHash) {
        $db = new DatabaseModel();
        $stmt = $db->prepare("UPDATE voters SET pin_hash = ? WHERE voter_id = ?");
        return $stmt->execute([$pinHash, $voterId]);
    }
} 