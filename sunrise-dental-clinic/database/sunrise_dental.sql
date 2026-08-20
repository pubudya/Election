-- Sunrise Dental Clinic - MySQL schema and sample data
-- Import this file in phpMyAdmin (XAMPP) or: mysql -u root < sunrise_dental.sql

CREATE DATABASE IF NOT EXISTS sunrise_dental
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sunrise_dental;

DROP TABLE IF EXISTS bills;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS treatments;
DROP TABLE IF EXISTS dentists;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id        INT AUTO_INCREMENT PRIMARY KEY,
    username       VARCHAR(50)  NOT NULL UNIQUE,
    password_hash  CHAR(64)     NOT NULL,
    full_name      VARCHAR(100) NOT NULL,
    role           ENUM('ADMIN', 'STAFF') NOT NULL DEFAULT 'STAFF',
    active         TINYINT(1)   NOT NULL DEFAULT 1,
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE dentists (
    dentist_id      INT AUTO_INCREMENT PRIMARY KEY,
    dentist_name    VARCHAR(100) NOT NULL,
    specialization  VARCHAR(100) NOT NULL,
    active          TINYINT(1)   NOT NULL DEFAULT 1
);

CREATE TABLE treatments (
    treatment_id    INT AUTO_INCREMENT PRIMARY KEY,
    treatment_type  VARCHAR(100) NOT NULL UNIQUE,
    treatment_cost  DECIMAL(10,2) NOT NULL,
    description     VARCHAR(255) NOT NULL
);

CREATE TABLE appointments (
    appointment_id      INT AUTO_INCREMENT PRIMARY KEY,
    appointment_number  VARCHAR(20)  NOT NULL UNIQUE,
    patient_name        VARCHAR(100) NOT NULL,
    address             VARCHAR(255) NOT NULL,
    contact_number      VARCHAR(20)  NOT NULL,
    dentist_id          INT          NOT NULL,
    treatment_id        INT          NOT NULL,
    appointment_date    DATE         NOT NULL,
    appointment_time    TIME         NOT NULL,
    status              ENUM('SCHEDULED', 'COMPLETED', 'CANCELLED') NOT NULL DEFAULT 'SCHEDULED',
    notes               VARCHAR(500) NULL,
    created_by          INT          NULL,
    created_at          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_appt_dentist FOREIGN KEY (dentist_id) REFERENCES dentists (dentist_id),
    CONSTRAINT fk_appt_treatment FOREIGN KEY (treatment_id) REFERENCES treatments (treatment_id),
    CONSTRAINT fk_appt_user FOREIGN KEY (created_by) REFERENCES users (user_id)
);

CREATE INDEX idx_appt_number ON appointments (appointment_number);
CREATE INDEX idx_appt_slot ON appointments (dentist_id, appointment_date, appointment_time, status);

CREATE TABLE bills (
    bill_id            INT AUTO_INCREMENT PRIMARY KEY,
    bill_number        VARCHAR(20)   NOT NULL UNIQUE,
    appointment_id     INT           NOT NULL,
    consultation_fee   DECIMAL(10,2) NOT NULL,
    treatment_cost     DECIMAL(10,2) NOT NULL,
    total_amount       DECIMAL(10,2) NOT NULL,
    payment_status     ENUM('PAID', 'UNPAID') NOT NULL DEFAULT 'PAID',
    billed_at          TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    billed_by          INT           NULL,
    CONSTRAINT fk_bill_appt FOREIGN KEY (appointment_id) REFERENCES appointments (appointment_id),
    CONSTRAINT fk_bill_user FOREIGN KEY (billed_by) REFERENCES users (user_id)
);

-- Passwords are SHA-256(salt + password) with salt SunriseDental#
-- admin / Admin@123
-- staff / Staff@123
INSERT INTO users (username, password_hash, full_name, role) VALUES
('admin', '808f94574e5c6a0d570ff489cd897a531f04df8e1742cad1f03810ad01826d45', 'Clinic Administrator', 'ADMIN'),
('staff', '65b0881a3946d944aeba6570a1a51f4c0935a54e634552ace7a800b153a32b25', 'Front Desk Staff', 'STAFF');

INSERT INTO dentists (dentist_name, specialization) VALUES
('Dr. Nimal Perera', 'General Dentistry'),
('Dr. Anjali Fernando', 'Orthodontics'),
('Dr. Ruwan Silva', 'Oral Surgery'),
('Dr. Priyanka Jayawardena', 'Cosmetic Dentistry');

INSERT INTO treatments (treatment_type, treatment_cost, description) VALUES
('Consultation', 2500.00, 'Initial examination and treatment advice'),
('Teeth Cleaning (Scaling)', 4500.00, 'Professional scaling and polishing'),
('Dental Filling', 6000.00, 'Tooth-coloured composite filling'),
('Tooth Extraction', 5000.00, 'Simple or surgical tooth removal'),
('Root Canal Treatment', 18000.00, 'Endodontic treatment for infected tooth'),
('Teeth Whitening', 12000.00, 'In-clinic professional whitening'),
('Braces Consultation', 3500.00, 'Orthodontic assessment and plan'),
('Dental Crown', 25000.00, 'Ceramic or porcelain fused crown'),
('Dentures', 35000.00, 'Partial or complete denture set'),
('Dental X-Ray', 2000.00, 'Digital intraoral or OPG radiograph');

-- Sample appointments so the dashboard is not empty on first login
INSERT INTO appointments
    (appointment_number, patient_name, address, contact_number, dentist_id, treatment_id,
     appointment_date, appointment_time, status, created_by)
VALUES
('SDC-20260820-001', 'Kasun Jayasuriya', '12 Galle Road, Colombo 03', '0771234567', 1, 2,
 CURDATE(), '09:00:00', 'SCHEDULED', 1),
('SDC-20260820-002', 'Nadeesha Perera', '45 Bauddhaloka Mawatha, Colombo 07', '0719876543', 4, 6,
 CURDATE(), '10:30:00', 'SCHEDULED', 2),
('SDC-20260821-001', 'Mohamed Rizwan', '88 Baseline Road, Colombo 09', '0765551122', 3, 4,
 DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', 'SCHEDULED', 2);
