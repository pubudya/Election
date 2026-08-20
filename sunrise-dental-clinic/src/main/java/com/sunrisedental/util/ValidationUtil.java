package com.sunrisedental.util;

import java.time.LocalDate;
import java.time.LocalTime;
import java.util.regex.Pattern;

public final class ValidationUtil {
    private static final Pattern NAME = Pattern.compile("^[A-Za-z .'-]{2,100}$");
    private static final Pattern PHONE = Pattern.compile("^(?:0\\d{9}|\\+94\\d{9})$");
    private static final Pattern APPT_NUMBER = Pattern.compile("^SDC-\\d{8}-\\d{3}$");

    private ValidationUtil() {
    }

    public static String requireText(String value, String field, int min, int max) {
        if (value == null || value.trim().isEmpty()) {
            return field + " is required.";
        }
        String trimmed = value.trim();
        if (trimmed.length() < min || trimmed.length() > max) {
            return field + " must be between " + min + " and " + max + " characters.";
        }
        return null;
    }

    public static String validatePatientName(String name) {
        String error = requireText(name, "Patient name", 2, 100);
        if (error != null) {
            return error;
        }
        if (!NAME.matcher(name.trim()).matches()) {
            return "Patient name may contain only letters, spaces, apostrophes, and hyphens.";
        }
        return null;
    }

    public static String validatePhone(String phone) {
        if (phone == null || phone.trim().isEmpty()) {
            return "Contact number is required.";
        }
        String cleaned = phone.trim().replace(" ", "");
        if (!PHONE.matcher(cleaned).matches()) {
            return "Enter a valid Sri Lankan number, for example 0771234567.";
        }
        return null;
    }

    public static String validateDateTime(LocalDate date, LocalTime time) {
        if (date == null) {
            return "Appointment date is required.";
        }
        if (time == null) {
            return "Appointment time is required.";
        }
        if (date.isBefore(LocalDate.now())) {
            return "Appointment date cannot be in the past.";
        }
        if (time.isBefore(LocalTime.of(8, 0)) || time.isAfter(LocalTime.of(18, 0))) {
            return "Clinic hours are 8:00 AM to 6:00 PM.";
        }
        return null;
    }

    public static String validateAppointmentNumber(String number) {
        if (number == null || number.trim().isEmpty()) {
            return "Appointment number is required.";
        }
        if (!APPT_NUMBER.matcher(number.trim().toUpperCase()).matches()) {
            return "Appointment number format is SDC-YYYYMMDD-001.";
        }
        return null;
    }

    public static String clean(String value) {
        return value == null ? "" : value.trim();
    }
}
