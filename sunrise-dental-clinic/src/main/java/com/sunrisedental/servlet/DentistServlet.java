package com.sunrisedental.servlet;

import com.sunrisedental.dao.DentistDAO;
import com.sunrisedental.util.JsonResponse;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.sql.SQLException;

@WebServlet("/api/dentists")
public class DentistServlet extends HttpServlet {
    private final DentistDAO dentistDAO = new DentistDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        try {
            JsonResponse.ok(resp, "Dentists loaded.", dentistDAO.findAllActive());
        } catch (SQLException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Could not load dentists.");
        }
    }
}
