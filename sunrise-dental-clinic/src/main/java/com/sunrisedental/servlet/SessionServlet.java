package com.sunrisedental.servlet;

import com.sunrisedental.model.User;
import com.sunrisedental.util.JsonResponse;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

import java.io.IOException;

@WebServlet("/api/session")
public class SessionServlet extends HttpServlet {
    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        HttpSession session = req.getSession(false);
        User user = session == null ? null : (User) session.getAttribute("user");
        if (user == null) {
            JsonResponse.error(resp, HttpServletResponse.SC_UNAUTHORIZED, "Not logged in.");
            return;
        }
        JsonResponse.ok(resp, "Session active.", user);
    }
}
