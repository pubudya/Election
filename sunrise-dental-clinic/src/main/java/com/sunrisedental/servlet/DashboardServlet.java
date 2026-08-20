package com.sunrisedental.servlet;

import com.sunrisedental.dao.AppointmentDAO;
import com.sunrisedental.dao.BillDAO;
import com.sunrisedental.util.JsonResponse;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.sql.SQLException;
import java.time.LocalDate;
import java.util.LinkedHashMap;
import java.util.Map;

@WebServlet("/api/dashboard")
public class DashboardServlet extends HttpServlet {
    private final AppointmentDAO appointmentDAO = new AppointmentDAO();
    private final BillDAO billDAO = new BillDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        try {
            Map<String, Object> data = new LinkedHashMap<>();
            data.put("todayCount", appointmentDAO.countToday());
            data.put("scheduledCount", appointmentDAO.countByStatus("SCHEDULED"));
            data.put("completedCount", appointmentDAO.countByStatus("COMPLETED"));
            data.put("cancelledCount", appointmentDAO.countByStatus("CANCELLED"));
            data.put("todayRevenue", billDAO.sumToday());
            data.put("todayAppointments", appointmentDAO.findByDate(LocalDate.now()));
            JsonResponse.ok(resp, "Dashboard loaded.", data);
        } catch (SQLException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Could not load the dashboard.");
        }
    }
}
