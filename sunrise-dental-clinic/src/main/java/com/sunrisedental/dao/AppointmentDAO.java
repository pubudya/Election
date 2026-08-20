package com.sunrisedental.dao;

import com.sunrisedental.model.Appointment;

import java.sql.Connection;
import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.Time;
import java.time.LocalDate;
import java.time.LocalTime;
import java.util.ArrayList;
import java.util.List;

public class AppointmentDAO {
    private static final String SELECT_JOIN = """
            SELECT a.appointment_id, a.appointment_number, a.patient_name, a.address, a.contact_number,
                   a.dentist_id, d.dentist_name, d.specialization,
                   a.treatment_id, t.treatment_type, t.treatment_cost,
                   a.appointment_date, a.appointment_time, a.status, a.notes, a.created_by, a.created_at
            FROM appointments a
            JOIN dentists d ON d.dentist_id = a.dentist_id
            JOIN treatments t ON t.treatment_id = a.treatment_id
            """;

    public Appointment create(Appointment appointment) throws SQLException {
        String sql = """
                INSERT INTO appointments
                    (appointment_number, patient_name, address, contact_number, dentist_id, treatment_id,
                     appointment_date, appointment_time, status, notes, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'SCHEDULED', ?, ?)
                """;
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            ps.setString(1, appointment.getAppointmentNumber());
            ps.setString(2, appointment.getPatientName());
            ps.setString(3, appointment.getAddress());
            ps.setString(4, appointment.getContactNumber());
            ps.setInt(5, appointment.getDentistId());
            ps.setInt(6, appointment.getTreatmentId());
            ps.setDate(7, Date.valueOf(appointment.getAppointmentDate()));
            ps.setTime(8, Time.valueOf(appointment.getAppointmentTime()));
            ps.setString(9, appointment.getNotes());
            if (appointment.getCreatedBy() == null) {
                ps.setNull(10, java.sql.Types.INTEGER);
            } else {
                ps.setInt(10, appointment.getCreatedBy());
            }
            ps.executeUpdate();
            try (ResultSet keys = ps.getGeneratedKeys()) {
                if (keys.next()) {
                    appointment.setAppointmentId(keys.getInt(1));
                }
            }
        }
        return findByNumber(appointment.getAppointmentNumber());
    }

    public Appointment findByNumber(String appointmentNumber) throws SQLException {
        String sql = SELECT_JOIN + " WHERE a.appointment_number = ?";
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

    public List<Appointment> findAll() throws SQLException {
        String sql = SELECT_JOIN + " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        return queryList(sql);
    }

    public List<Appointment> findByDate(LocalDate date) throws SQLException {
        String sql = SELECT_JOIN + " WHERE a.appointment_date = ? AND a.status <> 'CANCELLED' "
                + "ORDER BY a.appointment_time";
        List<Appointment> list = new ArrayList<>();
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setDate(1, Date.valueOf(date));
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    list.add(map(rs));
                }
            }
        }
        return list;
    }

    public boolean isSlotTaken(int dentistId, LocalDate date, LocalTime time, String excludeNumber)
            throws SQLException {
        String sql = """
                SELECT COUNT(*) FROM appointments
                WHERE dentist_id = ? AND appointment_date = ? AND appointment_time = ?
                  AND status <> 'CANCELLED'
                  AND (? IS NULL OR appointment_number <> ?)
                """;
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setInt(1, dentistId);
            ps.setDate(2, Date.valueOf(date));
            ps.setTime(3, Time.valueOf(time));
            ps.setString(4, excludeNumber);
            ps.setString(5, excludeNumber);
            try (ResultSet rs = ps.executeQuery()) {
                rs.next();
                return rs.getInt(1) > 0;
            }
        }
    }

    public int countByStatus(String status) throws SQLException {
        String sql = "SELECT COUNT(*) FROM appointments WHERE status = ?";
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, status);
            try (ResultSet rs = ps.executeQuery()) {
                rs.next();
                return rs.getInt(1);
            }
        }
    }

    public int countToday() throws SQLException {
        String sql = "SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE() AND status <> 'CANCELLED'";
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            rs.next();
            return rs.getInt(1);
        }
    }

    public String nextAppointmentNumber(LocalDate date) throws SQLException {
        String prefix = "SDC-" + date.toString().replace("-", "") + "-";
        String sql = """
                SELECT appointment_number FROM appointments
                WHERE appointment_number LIKE ?
                ORDER BY appointment_number DESC
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

    public boolean updateStatus(String appointmentNumber, String status) throws SQLException {
        String sql = "UPDATE appointments SET status = ? WHERE appointment_number = ?";
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setString(1, status);
            ps.setString(2, appointmentNumber);
            return ps.executeUpdate() == 1;
        }
    }

    private List<Appointment> queryList(String sql) throws SQLException {
        List<Appointment> list = new ArrayList<>();
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                list.add(map(rs));
            }
        }
        return list;
    }

    private Appointment map(ResultSet rs) throws SQLException {
        Appointment appointment = new Appointment();
        appointment.setAppointmentId(rs.getInt("appointment_id"));
        appointment.setAppointmentNumber(rs.getString("appointment_number"));
        appointment.setPatientName(rs.getString("patient_name"));
        appointment.setAddress(rs.getString("address"));
        appointment.setContactNumber(rs.getString("contact_number"));
        appointment.setDentistId(rs.getInt("dentist_id"));
        appointment.setDentistName(rs.getString("dentist_name"));
        appointment.setSpecialization(rs.getString("specialization"));
        appointment.setTreatmentId(rs.getInt("treatment_id"));
        appointment.setTreatmentType(rs.getString("treatment_type"));
        appointment.setTreatmentCost(rs.getBigDecimal("treatment_cost"));
        Date date = rs.getDate("appointment_date");
        Time time = rs.getTime("appointment_time");
        appointment.setAppointmentDate(date == null ? null : date.toLocalDate());
        appointment.setAppointmentTime(time == null ? null : time.toLocalTime());
        appointment.setStatus(rs.getString("status"));
        appointment.setNotes(rs.getString("notes"));
        int createdBy = rs.getInt("created_by");
        appointment.setCreatedBy(rs.wasNull() ? null : createdBy);
        java.sql.Timestamp createdAt = rs.getTimestamp("created_at");
        appointment.setCreatedAt(createdAt == null ? null : createdAt.toLocalDateTime());
        return appointment;
    }
}
