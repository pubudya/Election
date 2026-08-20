package com.sunrisedental.servlet;

import com.google.gson.JsonObject;
import com.sunrisedental.dao.AppointmentDAO;
import com.sunrisedental.dao.DentistDAO;
import com.sunrisedental.dao.TreatmentDAO;
import com.sunrisedental.model.Appointment;
import com.sunrisedental.model.Dentist;
import com.sunrisedental.model.Treatment;
import com.sunrisedental.model.User;
import com.sunrisedental.util.AuditLog;
import com.sunrisedental.util.JsonResponse;
import com.sunrisedental.util.ValidationUtil;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.sql.SQLException;
import java.time.LocalDate;
import java.time.LocalTime;

@WebServlet("/api/appointments")
public class AppointmentServlet extends HttpServlet {
    private final AppointmentDAO appointmentDAO = new AppointmentDAO();
    private final DentistDAO dentistDAO = new DentistDAO();
    private final TreatmentDAO treatmentDAO = new TreatmentDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        String number = req.getParameter("number");
        try {
            if (number != null && !number.isBlank()) {
                String error = ValidationUtil.validateAppointmentNumber(number);
                if (error != null) {
                    JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, error);
                    return;
                }
                Appointment appointment = appointmentDAO.findByNumber(number.trim().toUpperCase());
                if (appointment == null) {
                    JsonResponse.error(resp, HttpServletResponse.SC_NOT_FOUND,
                            "No appointment found for that number.");
                    return;
                }
                JsonResponse.ok(resp, "Appointment found.", appointment);
                return;
            }
            JsonResponse.ok(resp, "Appointments loaded.", appointmentDAO.findAll());
        } catch (SQLException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Could not load appointments.");
        }
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        JsonObject body = JsonResponse.gson().fromJson(req.getReader(), JsonObject.class);
        if (body == null) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, "Appointment details are required.");
            return;
        }

        String patientName = text(body, "patientName");
        String address = text(body, "address");
        String contactNumber = text(body, "contactNumber").replace(" ", "");
        String notes = text(body, "notes");

        String nameError = ValidationUtil.validatePatientName(patientName);
        if (nameError != null) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, nameError);
            return;
        }
        String addressError = ValidationUtil.requireText(address, "Address", 5, 255);
        if (addressError != null) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, addressError);
            return;
        }
        String phoneError = ValidationUtil.validatePhone(contactNumber);
        if (phoneError != null) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, phoneError);
            return;
        }

        int dentistId;
        int treatmentId;
        LocalDate date;
        LocalTime time;
        try {
            dentistId = body.get("dentistId").getAsInt();
            treatmentId = body.get("treatmentId").getAsInt();
            date = LocalDate.parse(text(body, "appointmentDate"));
            time = LocalTime.parse(text(body, "appointmentTime"));
        } catch (RuntimeException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST,
                    "Please select dentist, treatment, date, and time.");
            return;
        }

        String whenError = ValidationUtil.validateDateTime(date, time);
        if (whenError != null) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, whenError);
            return;
        }

        try {
            Dentist dentist = dentistDAO.findById(dentistId);
            Treatment treatment = treatmentDAO.findById(treatmentId);
            if (dentist == null || !dentist.isActive()) {
                JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, "Please select a valid dentist.");
                return;
            }
            if (treatment == null) {
                JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, "Please select a valid treatment.");
                return;
            }
            if (appointmentDAO.isSlotTaken(dentistId, date, time, null)) {
                JsonResponse.error(resp, HttpServletResponse.SC_CONFLICT,
                        dentist.getDentistName() + " already has a booking at that date and time. Choose another slot.");
                return;
            }

            User staff = (User) req.getSession().getAttribute("user");
            Appointment appointment = new Appointment();
            appointment.setAppointmentNumber(appointmentDAO.nextAppointmentNumber(date));
            appointment.setPatientName(patientName.trim());
            appointment.setAddress(address.trim());
            appointment.setContactNumber(contactNumber);
            appointment.setDentistId(dentistId);
            appointment.setTreatmentId(treatmentId);
            appointment.setAppointmentDate(date);
            appointment.setAppointmentTime(time);
            appointment.setNotes(notes.isEmpty() ? null : notes);
            appointment.setCreatedBy(staff == null ? null : staff.getUserId());

            Appointment saved = appointmentDAO.create(appointment);
            AuditLog.write(staff == null ? "unknown" : staff.getUsername(), "REGISTER_APPOINTMENT",
                    saved.getAppointmentNumber());
            JsonResponse.ok(resp, "Appointment registered. Number: " + saved.getAppointmentNumber(), saved);
        } catch (SQLException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_INTERNAL_SERVER_ERROR,
                    "Could not save the appointment. Check the MySQL connection.");
        }
    }

    @Override
    protected void doPut(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        JsonObject body = JsonResponse.gson().fromJson(req.getReader(), JsonObject.class);
        String number = body != null && body.has("appointmentNumber")
                ? body.get("appointmentNumber").getAsString() : "";
        String status = body != null && body.has("status") ? body.get("status").getAsString() : "";
        if (!"CANCELLED".equals(status)) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, "Only cancellation is supported here.");
            return;
        }
        String error = ValidationUtil.validateAppointmentNumber(number);
        if (error != null) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, error);
            return;
        }
        try {
            Appointment appointment = appointmentDAO.findByNumber(number.trim().toUpperCase());
            if (appointment == null) {
                JsonResponse.error(resp, HttpServletResponse.SC_NOT_FOUND, "Appointment not found.");
                return;
            }
            if ("CANCELLED".equals(appointment.getStatus())) {
                JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, "This appointment is already cancelled.");
                return;
            }
            appointmentDAO.updateStatus(appointment.getAppointmentNumber(), "CANCELLED");
            User staff = (User) req.getSession().getAttribute("user");
            AuditLog.write(staff == null ? "unknown" : staff.getUsername(), "CANCEL_APPOINTMENT",
                    appointment.getAppointmentNumber());
            JsonResponse.ok(resp, "Appointment cancelled. The time slot is now free.", null);
        } catch (SQLException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_INTERNAL_SERVER_ERROR, "Could not cancel the appointment.");
        }
    }

    private String text(JsonObject body, String key) {
        return body.has(key) && !body.get(key).isJsonNull() ? ValidationUtil.clean(body.get(key).getAsString()) : "";
    }
}
