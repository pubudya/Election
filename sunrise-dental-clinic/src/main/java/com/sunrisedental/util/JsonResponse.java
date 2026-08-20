package com.sunrisedental.util;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.google.gson.JsonPrimitive;
import com.google.gson.JsonSerializer;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.LocalTime;
import java.time.format.DateTimeFormatter;
import java.util.LinkedHashMap;
import java.util.Map;

public final class JsonResponse {
    private static final Gson GSON = new GsonBuilder()
            .registerTypeAdapter(LocalDate.class,
                    (JsonSerializer<LocalDate>) (src, type, ctx) ->
                            new JsonPrimitive(src.format(DateTimeFormatter.ISO_LOCAL_DATE)))
            .registerTypeAdapter(LocalTime.class,
                    (JsonSerializer<LocalTime>) (src, type, ctx) ->
                            new JsonPrimitive(src.format(DateTimeFormatter.ofPattern("HH:mm"))))
            .registerTypeAdapter(LocalDateTime.class,
                    (JsonSerializer<LocalDateTime>) (src, type, ctx) ->
                            new JsonPrimitive(src.format(DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm"))))
            .create();

    private JsonResponse() {
    }

    public static void ok(HttpServletResponse response, String message, Object data) throws IOException {
        write(response, HttpServletResponse.SC_OK, true, message, data);
    }

    public static void error(HttpServletResponse response, int status, String message) throws IOException {
        write(response, status, false, message, null);
    }

    private static void write(HttpServletResponse response, int status, boolean success, String message, Object data)
            throws IOException {
        response.setStatus(status);
        response.setContentType("application/json");
        response.setCharacterEncoding("UTF-8");
        Map<String, Object> body = new LinkedHashMap<>();
        body.put("success", success);
        body.put("message", message);
        if (data != null) {
            body.put("data", data);
        }
        response.getWriter().write(GSON.toJson(body));
    }

    public static Gson gson() {
        return GSON;
    }
}
