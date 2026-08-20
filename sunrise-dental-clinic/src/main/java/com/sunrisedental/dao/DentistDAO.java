package com.sunrisedental.dao;

import com.sunrisedental.model.Dentist;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class DentistDAO {

    public List<Dentist> findAllActive() throws SQLException {
        String sql = """
                SELECT dentist_id, dentist_name, specialization, active
                FROM dentists
                WHERE active = 1
                ORDER BY dentist_name
                """;
        List<Dentist> dentists = new ArrayList<>();
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                dentists.add(map(rs));
            }
        }
        return dentists;
    }

    public Dentist findById(int dentistId) throws SQLException {
        String sql = "SELECT dentist_id, dentist_name, specialization, active FROM dentists WHERE dentist_id = ?";
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setInt(1, dentistId);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return map(rs);
                }
            }
        }
        return null;
    }

    private Dentist map(ResultSet rs) throws SQLException {
        Dentist dentist = new Dentist();
        dentist.setDentistId(rs.getInt("dentist_id"));
        dentist.setDentistName(rs.getString("dentist_name"));
        dentist.setSpecialization(rs.getString("specialization"));
        dentist.setActive(rs.getBoolean("active"));
        return dentist;
    }
}
