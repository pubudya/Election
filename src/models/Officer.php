<?php
require_once __DIR__ . '/Database.php';
class Officer {
    private $db;
    public function __construct() {
        $this->db = new DatabaseModel();
    }
    public function create($data) {
        // Insert officer
    }
    public function read($id) {
        // Get officer by ID
    }
    public function update($id, $data) {
        // Update officer
    }
    public function delete($id) {
        // Delete officer
    }
    public function findByNIC($nic) {
        // Get officer by NIC
    }
    public static function getAll() {
        $db = new DatabaseModel();
        $stmt = $db->query('SELECT * FROM grama_niladhari');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function findById($id) {
        $db = new DatabaseModel();
        $stmt = $db->prepare('SELECT * FROM grama_niladhari WHERE officer_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
} 