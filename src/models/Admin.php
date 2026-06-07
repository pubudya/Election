<?php
require_once __DIR__ . '/Database.php';
class Admin {
    private $db;
    public function __construct() {
        $this->db = new DatabaseModel();
    }
    public function create($data) {
        // Insert admin
    }
    public function read($id) {
        // Get admin by ID
    }
    public function update($id, $data) {
        // Update admin
    }
    public function delete($id) {
        // Delete admin
    }
    public function findByUsername($username) {
        // Get admin by username
    }
} 