package com.sunrisedental.servlet;

import com.sunrisedental.dao.TreatmentDAO;
import com.sunrisedental.util.JsonResponse;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.sql.SQLException;

@WebServlet("/api/treatments")
public class TreatmentServlet extends HttpServlet {
    private final TreatmentDAO treatmentDAO = new TreatmentDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        try {
            JsonResponse.ok(resp, "Treatments loaded.", treatmentDAO.findAll());
        } catch (SQLException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Could not load treatments.");
        }
    }
}
