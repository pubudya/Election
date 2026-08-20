# Sunrise Dental Clinic — Appointment & Patient Management System

Menu-driven Java web application for **Sunrise Dental Clinic, Colombo**. Staff log in, register appointments, search records, calculate bills, and print receipts. Patient data is stored in **MySQL (XAMPP)**. The UI is HTML, CSS, and JavaScript. The backend is Java Servlets + DAO + JDBC, built with **Maven** for **IntelliJ IDEA**.

## What the system does

1. **User Authentication** — username and password; only authorized staff
2. **Register New Appointment** — auto unique number, patient details, dentist, treatment, date/time
3. **Display Appointment Details** — search by appointment number
4. **Calculate and Print Bill** — consultation fee + treatment cost, printable receipt
5. **Help Section** — step-by-step staff instructions
6. **Exit System** — safe logout

Extra features: dashboard statistics, double-booking prevention, appointment cancellation, audit log text file, Sri Lankan phone validation.

## Default staff accounts

| Username | Password   | Role  |
|----------|------------|-------|
| `admin`  | `Admin@123` | ADMIN |
| `staff`  | `Staff@123` | STAFF |

## Software you need

- JDK 17 or newer
- IntelliJ IDEA (Community or Ultimate)
- Apache Maven (bundled with IntelliJ is enough)
- XAMPP (MySQL + phpMyAdmin)

You do **not** need a separate Tomcat install to run the project. `SunriseDentalApp` starts an embedded Tomcat when you click Run.

## Quick start in IntelliJ

## Quick start in IntelliJ

If you see **Could not find or load main class**, IntelliJ ran Java without the Maven classpath. Open the folder that contains `pom.xml`, reload Maven, then use the run configuration **Run Sunrise Dental Clinic** (Maven `compile exec:java`). Or double-click `run.bat`.

1. Import `database/sunrise_dental.sql` in phpMyAdmin (start MySQL in XAMPP).
2. **File → Open** the `sunrise-dental-clinic` folder (the one with `pom.xml`).
3. Maven tool window → Reload. Set Project SDK to **21** (or 17+).
4. **Build → Rebuild Project**.
5. Run configuration: **Run Sunrise Dental Clinic**.
6. Browser: `http://localhost:8080/`  
   Login: `admin` / `Admin@123`

## Project structure

```
sunrise-dental-clinic/
├── pom.xml
├── database/sunrise_dental.sql
├── INTELLIJ_SETUP.md          ← create packages/files in IntelliJ, Tomcat, XAMPP
├── src/main/java/com/sunrisedental/
│   ├── SunriseDentalApp.java  ← click Run on this file
│   ├── model/                 User, Appointment, Dentist, Treatment, Bill
│   ├── dao/                   DBConnection + DAO classes
│   ├── servlet/               REST-style JSON APIs
│   ├── filter/                AuthFilter (blocks guests)
│   └── util/                  Password, JSON, validation, audit log
└── src/main/webapp/
    ├── index.html             Login
    ├── dashboard.html
    ├── register.html
    ├── search.html
    ├── bill.html
    ├── help.html
    ├── css/style.css
    └── js/                    Frontend API calls
```

## Bill formula

- Consultation fee: **LKR 2,500** (waived when the treatment itself is Consultation)
- Treatment cost: taken from the `treatments` table
- **Total = consultation fee + treatment cost**

Appointment numbers look like `SDC-20260820-001`.

## If MySQL login fails

XAMPP default is user `root` with an **empty** password. If you set a password in phpMyAdmin, put it in `db.properties`. Start **Apache** and **MySQL** in the XAMPP control panel before running Tomcat.
