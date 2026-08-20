package com.sunrisedental.filter;

import com.sunrisedental.util.JsonResponse;
import jakarta.servlet.Filter;
import jakarta.servlet.FilterChain;
import jakarta.servlet.ServletException;
import jakarta.servlet.ServletRequest;
import jakarta.servlet.ServletResponse;
import jakarta.servlet.annotation.WebFilter;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

import java.io.IOException;
import java.util.Set;

@WebFilter("/*")
public class AuthFilter implements Filter {
    private static final Set<String> PUBLIC_PATHS = Set.of(
            "/",
            "/index.html",
            "/css/style.css",
            "/js/app.js",
            "/js/auth.js",
            "/api/login",
            "/api/session"
    );

    @Override
    public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain)
            throws IOException, ServletException {
        HttpServletRequest req = (HttpServletRequest) request;
        HttpServletResponse resp = (HttpServletResponse) response;
        req.setCharacterEncoding("UTF-8");

        String path = req.getRequestURI().substring(req.getContextPath().length());
        if (path.isEmpty()) {
            path = "/";
        }

        if (isPublic(path) || path.startsWith("/css/") || path.startsWith("/js/") || path.startsWith("/images/")) {
            chain.doFilter(request, response);
            return;
        }

        HttpSession session = req.getSession(false);
        boolean loggedIn = session != null && session.getAttribute("user") != null;
        if (loggedIn) {
            chain.doFilter(request, response);
            return;
        }

        if (path.startsWith("/api/")) {
            JsonResponse.error(resp, HttpServletResponse.SC_UNAUTHORIZED, "Please log in to continue.");
            return;
        }

        resp.sendRedirect(req.getContextPath() + "/index.html");
    }

    private boolean isPublic(String path) {
        return PUBLIC_PATHS.contains(path);
    }
}
