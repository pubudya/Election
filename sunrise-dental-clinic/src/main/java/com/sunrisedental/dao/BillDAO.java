package com.sunrisedental.dao;

import com.sunrisedental.model.Appointment;
import com.sunrisedental.model.Bill;

import java.math.BigDecimal;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.time.LocalDate;
import java.time.format.DateTimeFormatter;

public class BillDAO {

    public Bill findByAppointmentNumber(String appointmentNumber) throws SQLException {
        String sql = """
                SELECT b.bill_id, b.bill_number, b.appointment_id, a.appointment_number, a.patient_name,
                       d.dentist_name, t.treatment_type, b.consultation_fee, b.treatment_cost,
                       b.total_amount, b.payment_status, b.billed_at
                FROM bills b
                JOIN appointments a ON a.appointment_id = b.appointment_id
                JOIN dentists d ON d.dentist_id = a.dentist_id
                JOIN treatments t ON t.treatment_id = a.treatment_id
                WHERE a.appointment_number = ?
                """;
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, appointmentNumber);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return map(rs);
                }
            }
        }
        return null;
    }

    public Bill create(Appointment appointment, Integer billedBy) throws SQLException {
        Bill existing = findByAppointmentNumber(appointment.getAppointmentNumber());
        if (existing != null) {
            return existing;
        }

        BigDecimal consultation = "Consultation".equalsIgnoreCase(appointment.getTreatmentType())
                ? BigDecimal.ZERO
                : Bill.CONSULTATION_FEE;
        BigDecimal treatmentCost = appointment.getTreatmentCost();
        BigDecimal total = consultation.add(treatmentCost);
        String billNumber = nextBillNumber();

        String sql = """
                INSERT INTO bills (bill_number, appointment_id, consultation_fee, treatment_cost,
                                   total_amount, payment_status, billed_by)
                VALUES (?, ?, ?, ?, ?, 'PAID', ?)
                """;
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            ps.setString(1, billNumber);
            ps.setInt(2, appointment.getAppointmentId());
            ps.setBigDecimal(3, consultation);
            ps.setBigDecimal(4, treatmentCost);
            ps.setBigDecimal(5, total);
            if (billedBy == null) {
                ps.setNull(6, java.sql.Types.INTEGER);
            } else {
                ps.setInt(6, billedBy);
            }
            ps.executeUpdate();
        }

        new AppointmentDAO().updateStatus(appointment.getAppointmentNumber(), "COMPLETED");
        return findByAppointmentNumber(appointment.getAppointmentNumber());
    }

    public BigDecimal sumToday() throws SQLException {
        String sql = "SELECT COALESCE(SUM(total_amount), 0) FROM bills WHERE DATE(billed_at) = CURDATE()";
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            rs.next();
            return rs.getBigDecimal(1);
        }
    }

    private String nextBillNumber() throws SQLException {
        String prefix = "BILL-" + LocalDate.now().format(DateTimeFormatter.BASIC_ISO_DATE) + "-";
        String sql = """
                SELECT bill_number FROM bills
                WHERE bill_number LIKE ?
                ORDER BY bill_number DESC
                LIMIT 1
                """;
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, prefix + "%");
            try (ResultSet rs = ps.executeQuery()) {
                int next = 1;
                if (rs.next()) {
                    String last = rs.getString(1);
                    next = Integer.parseInt(last.substring(last.lastIndexOf('-') + 1)) + 1;
                }
                return prefix + String.format("%03d", next);
            }
        }
    }

    private Bill map(ResultSet rs) throws SQLException {
        Bill bill = new Bill();
        bill.setBillId(rs.getInt("bill_id"));
        bill.setBillNumber(rs.getString("bill_number"));
        bill.setAppointmentId(rs.getInt("appointment_id"));
        bill.setAppointmentNumber(rs.getString("appointment_number"));
        bill.setPatientName(rs.getString("patient_name"));
        bill.setDentistName(rs.getString("dentist_name"));
        bill.setTreatmentType(rs.getString("treatment_type"));
        bill.setConsultationFee(rs.getBigDecimal("consultation_fee"));
        bill.setTreatmentCost(rs.getBigDecimal("treatment_cost"));
        bill.setTotalAmount(rs.getBigDecimal("total_amount"));
        bill.setPaymentStatus(rs.getString("payment_status"));
        java.sql.Timestamp billedAt = rs.getTimestamp("billed_at");
        bill.setBilledAt(billedAt == null ? null : billedAt.toLocalDateTime());
        return bill;
    }
}
