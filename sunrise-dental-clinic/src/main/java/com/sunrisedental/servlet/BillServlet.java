package com.sunrisedental.servlet;

import com.google.gson.JsonObject;
import com.sunrisedental.dao.AppointmentDAO;
import com.sunrisedental.dao.BillDAO;
import com.sunrisedental.model.Appointment;
import com.sunrisedental.model.Bill;
import com.sunrisedental.model.User;
import com.sunrisedental.util.AuditLog;
import com.sunrisedental.util.JsonResponse;
import com.sunrisedental.util.ValidationUtil;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.math.BigDecimal;
import java.sql.SQLException;
import java.util.LinkedHashMap;
import java.util.Map;

@WebServlet("/api/bills")
public class BillServlet extends HttpServlet {
    private final AppointmentDAO appointmentDAO = new AppointmentDAO();
    private final BillDAO billDAO = new BillDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        String number = req.getParameter("number");
        String error = ValidationUtil.validateAppointmentNumber(number);
        if (error != null) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, error);
            return;
        }
        try {
            Appointment appointment = appointmentDAO.findByNumber(number.trim().toUpperCase());
            if (appointment == null) {
                JsonResponse.error(resp, HttpServletResponse.SC_NOT_FOUND, "No appointment found for that number.");
                return;
            }
            if ("CANCELLED".equals(appointment.getStatus())) {
                JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST,
                        "A cancelled appointment cannot be billed.");
                return;
            }

            Bill existing = billDAO.findByAppointmentNumber(appointment.getAppointmentNumber());
            Map<String, Object> payload = preview(appointment, existing);
            JsonResponse.ok(resp, existing == null ? "Bill preview ready." : "Existing bill loaded.", payload);
        } catch (SQLException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Could not calculate the bill.");
        }
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        JsonObject body = JsonResponse.gson().fromJson(req.getReader(), JsonObject.class);
        String number = body != null && body.has("appointmentNumber")
                ? body.get("appointmentNumber").getAsString() : "";
        String error = ValidationUtil.validateAppointmentNumber(number);
        if (error != null) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, error);
            return;
        }
        try {
            Appointment appointment = appointmentDAO.findByNumber(number.trim().toUpperCase());
            if (appointment == null) {
                JsonResponse.error(resp, HttpServletResponse.SC_NOT_FOUND, "No appointment found for that number.");
                return;
            }
            if ("CANCELLED".equals(appointment.getStatus())) {
                JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST,
                        "A cancelled appointment cannot be billed.");
                return;
            }
            User staff = (User) req.getSession().getAttribute("user");
            Bill bill = billDAO.create(appointment, staff == null ? null : staff.getUserId());
            AuditLog.write(staff == null ? "unknown" : staff.getUsername(), "PRINT_BILL", bill.getBillNumber());
            JsonResponse.ok(resp, "Bill generated successfully.", preview(appointment, bill));
        } catch (SQLException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Could not save the bill.");
        }
    }

    private Map<String, Object> preview(Appointment appointment, Bill bill) {
        BigDecimal consultation = "Consultation".equalsIgnoreCase(appointment.getTreatmentType())
                ? BigDecimal.ZERO
                : Bill.CONSULTATION_FEE;
        BigDecimal treatmentCost = appointment.getTreatmentCost();
        BigDecimal total = consultation.add(treatmentCost);

        Map<String, Object> payload = new LinkedHashMap<>();
        payload.put("appointment", appointment);
        payload.put("consultationFee", bill == null ? consultation : bill.getConsultationFee());
        payload.put("treatmentCost", bill == null ? treatmentCost : bill.getTreatmentCost());
        payload.put("totalAmount", bill == null ? total : bill.getTotalAmount());
        payload.put("bill", bill);
        return payload;
    }
}
