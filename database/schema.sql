-- Automated Sri Lankan Presidential Election System DB Schema
CREATE DATABASE IF NOT EXISTS election_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE election_system;

-- Grama Niladhari Officers
CREATE TABLE grama_niladhari (
    officer_id INT AUTO_INCREMENT PRIMARY KEY,
    nic VARCHAR(12) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    division VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    province VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    password_hash VARCHAR(64) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME
) ENGINE=InnoDB;

-- Admin accounts
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(64) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15),
    is_super_admin BOOLEAN DEFAULT FALSE,
    last_login DATETIME,
    login_attempts INT DEFAULT 0,
    account_locked BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Voters table
CREATE TABLE voters (
    voter_id INT AUTO_INCREMENT PRIMARY KEY,
    nic VARCHAR(12) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    address TEXT NOT NULL,
    division VARCHAR(50) NOT NULL,
    district VARCHAR(50) NOT NULL,
    province VARCHAR(50) NOT NULL,
    polling_division VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(15),
    pin_hash VARCHAR(64) NOT NULL,
    is_registered BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT FALSE,
    has_voted BOOLEAN DEFAULT FALSE,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    registered_by INT,
    FOREIGN KEY (registered_by) REFERENCES grama_niladhari(officer_id)
) ENGINE=InnoDB;

-- Add indexes for better performance
CREATE INDEX idx_voters_polling_division ON voters(polling_division);
CREATE INDEX idx_voters_is_approved ON voters(is_approved);

-- Candidates
CREATE TABLE candidates (
    candidate_id INT AUTO_INCREMENT PRIMARY KEY,
    nic VARCHAR(12) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    party VARCHAR(100) NOT NULL,
    party_symbol VARCHAR(50),
    candidate_number INT,
    province VARCHAR(50),
    photo_path VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    registered_by INT,
    registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (registered_by) REFERENCES admins(admin_id)
) ENGINE=InnoDB;

-- Votes
CREATE TABLE votes (
    vote_id INT AUTO_INCREMENT PRIMARY KEY,
    voter_id INT NOT NULL,
    first_preference INT NOT NULL,
    second_preference INT,
    third_preference INT,
    vote_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    device_info TEXT,
    session_id VARCHAR(64),
    election_id INT NOT NULL,
    is_valid BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (voter_id) REFERENCES voters(voter_id),
    FOREIGN KEY (first_preference) REFERENCES candidates(candidate_id),
    FOREIGN KEY (second_preference) REFERENCES candidates(candidate_id),
    FOREIGN KEY (third_preference) REFERENCES candidates(candidate_id),
    FOREIGN KEY (election_id) REFERENCES election_config(config_id)
) ENGINE=InnoDB;

-- OTP Logs
CREATE TABLE otp_logs (
    otp_id INT AUTO_INCREMENT PRIMARY KEY,
    nic VARCHAR(12) NOT NULL,
    otp_code VARCHAR(6) NOT NULL,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    is_used BOOLEAN DEFAULT FALSE,
    used_at DATETIME,
    purpose ENUM('voter_login', 'officer_login', 'admin_login') NOT NULL,
    ip_address VARCHAR(45)
) ENGINE=InnoDB;

-- System logs
CREATE TABLE system_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('voter', 'officer', 'admin') NOT NULL,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    action_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('success', 'failure', 'warning') NOT NULL,
    details TEXT
) ENGINE=InnoDB;

-- Failed login attempts
CREATE TABLE failed_logins (
    attempt_id INT AUTO_INCREMENT PRIMARY KEY,
    nic VARCHAR(12),
    username VARCHAR(50),
    ip_address VARCHAR(45) NOT NULL,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_agent TEXT,
    is_locked BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB;

-- Election configuration
CREATE TABLE election_config (
    config_id INT AUTO_INCREMENT PRIMARY KEY,
    election_name VARCHAR(255) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    vote_time_limit INT DEFAULT 5,
    extend_time_limit INT DEFAULT 2,
    is_active BOOLEAN DEFAULT FALSE,
    results_published BOOLEAN DEFAULT FALSE,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(admin_id)
) ENGINE=InnoDB;

-- Reviews & Queries
CREATE TABLE IF NOT EXISTS reviews_queries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    reply TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    replied_at DATETIME
) ENGINE=InnoDB; 