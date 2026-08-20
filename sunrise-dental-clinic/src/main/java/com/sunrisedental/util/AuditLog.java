package com.sunrisedental.util;

import java.io.IOException;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.StandardOpenOption;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

/**
 * Simple append-only text log stored under the user home folder.
 * Complements MySQL storage as allowed by the assignment.
 */
public final class AuditLog {
    private static final Path LOG_FILE = Path.of(System.getProperty("user.home"), "sunrise-dental-audit.log");
    private static final DateTimeFormatter FORMAT = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");

    private AuditLog() {
    }

    public static void write(String username, String action, String detail) {
        String line = FORMAT.format(LocalDateTime.now()) + " | " + username + " | " + action + " | " + detail + System.lineSeparator();
        try {
            Files.writeString(LOG_FILE, line, StandardCharsets.UTF_8,
                    StandardOpenOption.CREATE, StandardOpenOption.APPEND);
        } catch (IOException ignored) {
            // Logging must never break a patient request.
        }
    }
}
