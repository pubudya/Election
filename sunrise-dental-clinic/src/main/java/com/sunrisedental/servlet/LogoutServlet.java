package com.sunrisedental.servlet;

import com.sunrisedental.model.User;
import com.sunrisedental.util.AuditLog;
import com.sunrisedental.util.JsonResponse;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

import java.io.IOException;

@WebServlet("/api/logout")
public class LogoutServlet extends HttpServlet {
    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        HttpSession session = req.getSession(false);
        if (session != null) {
            User user = (User) session.getAttribute("user");
            if (user != null) {
                AuditLog.write(user.getUsername(), "LOGOUT", "Session closed");
            }
            session.invalidate();
        }
        JsonResponse.ok(resp, "You have been logged out safely.", null);
    }
}
