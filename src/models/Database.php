<?php
require_once __DIR__ . '/../../config/db_config.php';

class DatabaseModel {
    private $db;
    public function __construct() {
        $this->db = new Database();
    }
    public function getConnection() {
        return $this->db->getConnection();
    }
    public function prepare($sql) {
        return $this->db->prepare($sql);
    }
    public function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    public function commit() {
        return $this->db->commit();
    }
    public function rollBack() {
        return $this->db->rollBack();
    }
} 