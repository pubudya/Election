package com.sunrisedental.servlet;

import com.google.gson.JsonObject;
import com.sunrisedental.dao.UserDAO;
import com.sunrisedental.model.User;
import com.sunrisedental.util.AuditLog;
import com.sunrisedental.util.JsonResponse;
import com.sunrisedental.util.ValidationUtil;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

import java.io.IOException;
import java.sql.SQLException;

@WebServlet("/api/login")
public class LoginServlet extends HttpServlet {
    private final UserDAO userDAO = new UserDAO();

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        JsonObject body;
        try {
            body = JsonResponse.gson().fromJson(req.getReader(), JsonObject.class);
        } catch (RuntimeException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, "Please send a valid login request.");
            return;
        }
        if (body == null) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, "Username and password are required.");
            return;
        }

        String username = body.has("username") ? ValidationUtil.clean(body.get("username").getAsString()) : "";
        String password = body.has("password") ? body.get("password").getAsString() : "";

        if (username.isEmpty() || password == null || password.isEmpty()) {
            JsonResponse.error(resp, HttpServletResponse.SC_BAD_REQUEST, "Please enter both username and password.");
            return;
        }

        try {
            User user = userDAO.authenticate(username, password);
            if (user == null) {
                AuditLog.write(username, "LOGIN_FAILED", "Invalid credentials");
                JsonResponse.error(resp, HttpServletResponse.SC_UNAUTHORIZED, "Invalid username or password.");
                return;
            }

            HttpSession session = req.getSession(true);
            session.setAttribute("user", user);
            session.setMaxInactiveInterval(30 * 60);
            AuditLog.write(user.getUsername(), "LOGIN", "Successful login");
            JsonResponse.ok(resp, "Welcome back, " + user.getFullName() + ".", user);
        } catch (SQLException e) {
            JsonResponse.error(resp, HttpServletResponse.SC_INTERNAL_SERVER_ERROR,
                    "Unable to connect to the database. Start MySQL in XAMPP and try again.");
        }
    }
}
