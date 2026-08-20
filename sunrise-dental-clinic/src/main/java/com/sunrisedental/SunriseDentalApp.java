package com.sunrisedental;

import org.apache.catalina.Context;
import org.apache.catalina.WebResourceRoot;
import org.apache.catalina.startup.Tomcat;
import org.apache.catalina.webresources.DirResourceSet;
import org.apache.catalina.webresources.StandardRoot;

import java.awt.Desktop;
import java.io.File;
import java.net.URI;
import java.nio.file.Files;
import java.util.LinkedHashSet;
import java.util.Set;

/**
 * Click the green Run button on THIS class in IntelliJ.
 * HTML, CSS, servlet, and DAO files are not runnable on their own.
 */
public class SunriseDentalApp {
    public static void main(String[] args) throws Exception {
        File projectRoot = findProjectRoot();
        File webappDir = new File(projectRoot, "src/main/webapp");
        if (!webappDir.isDirectory()) {
            throw new IllegalStateException(
                    "Cannot find src/main/webapp. In IntelliJ Run configuration set Working directory to the sunrise-dental-clinic folder (the folder that contains pom.xml)."
            );
        }

        int port = parsePort(args);
        File baseDir = Files.createTempDirectory("sunrise-tomcat-").toFile();
        baseDir.deleteOnExit();

        Tomcat tomcat = new Tomcat();
        tomcat.setBaseDir(baseDir.getAbsolutePath());
        tomcat.setPort(port);
        tomcat.getConnector();

        Context context = tomcat.addWebapp("", webappDir.getAbsolutePath());
        context.setParentClassLoader(SunriseDentalApp.class.getClassLoader());

        WebResourceRoot resources = new StandardRoot(context);
        for (File classesDir : classDirectories(projectRoot)) {
            if (classesDir.isDirectory()) {
                resources.addPreResources(new DirResourceSet(
                        resources,
                        "/WEB-INF/classes",
                        classesDir.getAbsolutePath(),
                        "/"
                ));
            }
        }
        context.setResources(resources);

        tomcat.start();
        String url = "http://localhost:" + port + "/";
        System.out.println();
        System.out.println("Sunrise Dental Clinic is running.");
        System.out.println("Open: " + url);
        System.out.println("Login: admin / Admin@123   or   staff / Staff@123");
        System.out.println("Press Ctrl+C in this console to stop.");
        System.out.println();
        openBrowser(url);
        tomcat.getServer().await();
    }

    private static int parsePort(String[] args) {
        if (args != null && args.length > 0) {
            return Integer.parseInt(args[0]);
        }
        String env = System.getenv("PORT");
        return env == null || env.isBlank() ? 8080 : Integer.parseInt(env);
    }

    private static File findProjectRoot() {
        File dir = new File(System.getProperty("user.dir")).getAbsoluteFile();
        for (int i = 0; i < 8 && dir != null; i++) {
            File nested = new File(dir, "sunrise-dental-clinic");
            if (isProjectRoot(nested)) {
                return nested;
            }
            if (isProjectRoot(dir)) {
                return dir;
            }
            dir = dir.getParentFile();
        }
        throw new IllegalStateException(
                "Could not find the sunrise-dental-clinic project. Open that folder in IntelliJ (the one with pom.xml), then run SunriseDentalApp."
        );
    }

    private static boolean isProjectRoot(File dir) {
        return dir.isDirectory()
                && new File(dir, "pom.xml").isFile()
                && new File(dir, "src/main/webapp").isDirectory();
    }

    private static File[] classDirectories(File projectRoot) {
        Set<File> dirs = new LinkedHashSet<>();
        dirs.add(new File(projectRoot, "target/classes"));
        dirs.add(new File(projectRoot, "out/production/sunrise-dental-clinic"));
        dirs.add(new File(projectRoot, "out/production/classes"));
        try {
            File fromClasspath = new File(
                    SunriseDentalApp.class.getProtectionDomain().getCodeSource().getLocation().toURI()
            );
            if (fromClasspath.isDirectory()) {
                dirs.add(fromClasspath);
            }
        } catch (Exception ignored) {
            // Fall back to Maven/IntelliJ output folders.
        }
        return dirs.stream().filter(File::isDirectory).toArray(File[]::new);
    }

    private static void openBrowser(String url) {
        try {
            if (Desktop.isDesktopSupported() && Desktop.getDesktop().isSupported(Desktop.Action.BROWSE)) {
                Desktop.getDesktop().browse(URI.create(url));
            }
        } catch (Exception ignored) {
            // Browser open is optional; the console URL is enough.
        }
    }
}
