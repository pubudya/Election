package com.sunrisedental.dao;

import com.sunrisedental.model.Treatment;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class TreatmentDAO {

    public List<Treatment> findAll() throws SQLException {
        String sql = """
                SELECT treatment_id, treatment_type, treatment_cost, description
                FROM treatments
                ORDER BY treatment_type
                """;
        List<Treatment> treatments = new ArrayList<>();
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                treatments.add(map(rs));
            }
        }
        return treatments;
    }

    public Treatment findById(int treatmentId) throws SQLException {
        String sql = """
                SELECT treatment_id, treatment_type, treatment_cost, description
                FROM treatments
                WHERE treatment_id = ?
                """;
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {
            ps.setInt(1, treatmentId);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return map(rs);
                }
            }
        }
        return null;
    }

    private Treatment map(ResultSet rs) throws SQLException {
        Treatment treatment = new Treatment();
        treatment.setTreatmentId(rs.getInt("treatment_id"));
        treatment.setTreatmentType(rs.getString("treatment_type"));
        treatment.setTreatmentCost(rs.getBigDecimal("treatment_cost"));
        treatment.setDescription(rs.getString("description"));
        return treatment;
    }
}
