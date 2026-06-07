<?php
require_once __DIR__ . '/Database.php';
class Candidate {
    private $db;
    public function __construct() {
        $this->db = new DatabaseModel();
    }
    public function create($data) {
        // Insert candidate
    }
    public function read($id) {
        // Get candidate by ID
    }
    public function update($id, $data) {
        // Update candidate
    }
    public function delete($id) {
        // Delete candidate
    }
    public static function getAll() {
        $db = (new Database())->getConnection();
        $sql = 'SELECT * FROM candidates WHERE is_active = 1';
        $stmt = $db->prepare($sql);
        if (!$stmt->execute()) {
            error_log('Candidate::getAll SQL error: ' . print_r($stmt->errorInfo(), true));
        }
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log('Candidate::getAll result: ' . print_r($result, true));
        return $result;
    }
    public static function findById($id) {
        $db = new DatabaseModel();
        $stmt = $db->prepare('SELECT * FROM candidates WHERE candidate_id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
} 