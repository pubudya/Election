# IntelliJ IDEA setup — Sunrise Dental Clinic

This guide shows how to open the ready Maven project **and** how to recreate the same packages and files by hand in IntelliJ.

If IntelliJ says **“file in the editor is not runnable”**, open and run `SunriseDentalApp.java`. Do not click Run on `index.html` or a servlet class.

---

## A. Install the tools

1. Install **JDK 17+** (Eclipse Temurin or Oracle).
2. Install **IntelliJ IDEA** (Community is enough).
3. Install **XAMPP** and start **Apache** + **MySQL**.
4. Download **Apache Tomcat 10.1** only if you want to deploy a WAR yourself. It is optional. The green Run button uses embedded Tomcat.
5. Optional: install Maven, or use the Maven bundled with IntelliJ.

---

## B. Create the MySQL database (XAMPP)

1. Open `http://localhost/phpmyadmin`.
2. Click **Import**.
3. Choose `sunrise-dental-clinic/database/sunrise_dental.sql`.
4. Click **Go**. You should see database `sunrise_dental` with tables `users`, `dentists`, `treatments`, `appointments`, `bills`.
5. If your MySQL root user has a password, edit `src/main/resources/db.properties`:

```
db.url=jdbc:mysql://localhost:3306/sunrise_dental?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=Asia/Colombo
db.username=root
db.password=YOUR_PASSWORD
db.driver=com.mysql.cj.jdbc.Driver
password.salt=SunriseDental#
```

Do not change `password.salt` unless you also re-hash the passwords in SQL.

---

## C. Open this project in IntelliJ (recommended)

1. **File → Open** and select the folder `sunrise-dental-clinic` (the folder that contains `pom.xml`).
2. Trust the project if asked.
3. Wait until IntelliJ finishes **Maven import** (bottom-right progress bar).
4. **File → Project Structure → Project**
   - SDK: Java 17 or 21
   - Language level: 17
5. **File → Project Structure → Modules → sunrise-dental-clinic**
   - Confirm it is a Maven module with packaging `war`.

If Maven did not import: right-click `pom.xml` → **Add as Maven Project**.

Then run the app:

1. Open `src/main/java/com/sunrisedental/SunriseDentalApp.java`.
2. Click the green triangle next to `public static void main`.
3. Open `http://localhost:8080/`
4. Login `admin` / `Admin@123`

If the green triangle is missing: right-click `SunriseDentalApp.java` → **Run ‘SunriseDentalApp.main()’**.

---

## D. Recreate the project from scratch (if a lecturer asks you to)

These are the exact IntelliJ clicks that match this codebase.

### D1. New Maven web app

1. **File → New → Project**
2. Generator: **Maven**
3. Name: `sunrise-dental-clinic`
4. GroupId: `com.sunrisedental`
5. ArtifactId: `sunrise-dental-clinic`
6. JDK: 17+
7. Create.
8. Replace the generated `pom.xml` with the `pom.xml` from this repository (`packaging` must be `war`).
9. Click the Maven reload button (two arrows) in the Maven tool window.

### D2. Enable the webapp folder

IntelliJ needs a web resource directory:

1. **File → Project Structure → Modules → sunrise-dental-clinic → Web** (if Web is missing, click **+** and add **Web**).
2. Web Resource Directory: `src/main/webapp`
3. Deployment Descriptor: `src/main/webapp/WEB-INF/web.xml`

Create folders if they do not exist:

- `src/main/java`
- `src/main/resources`
- `src/main/webapp`
- `src/main/webapp/WEB-INF`
- `src/main/webapp/css`
- `src/main/webapp/js`

### D3. Create Java packages

In **Project** view, switch to **Packages**.

1. Right-click `src/main/java` → **New → Package**
2. Type `com.sunrisedental` → Enter, then create `SunriseDentalApp` (this is the only class with `main` — click Run on it).
3. Repeat for:
   - `com.sunrisedental.model`
   - `com.sunrisedental.dao`
   - `com.sunrisedental.servlet`
   - `com.sunrisedental.filter`
   - `com.sunrisedental.util`

### D4. Create each Java class

Right-click the package → **New → Java Class** (or **New → Servlet** then replace the body). Copy the matching file from this repo.

**com.sunrisedental**

| Class               | Purpose                                      |
|---------------------|----------------------------------------------|
| `SunriseDentalApp`  | Click Run here. Starts the website on port 8080 |

**model**

| Class        | Purpose                          |
|--------------|----------------------------------|
| `User`       | Logged-in staff                  |
| `Appointment`| Patient visit record             |
| `Dentist`    | Dentist list                     |
| `Treatment`  | Treatment name and price         |
| `Bill`       | Receipt amounts                  |

**dao**

| Class            | Purpose                         |
|------------------|---------------------------------|
| `DBConnection`   | JDBC + `db.properties`          |
| `UserDAO`        | Login query                     |
| `AppointmentDAO` | Create / search / slot check    |
| `DentistDAO`     | Load dentists                   |
| `TreatmentDAO`   | Load treatments                 |
| `BillDAO`        | Create and fetch bills          |

**util**

| Class             | Purpose                    |
|-------------------|----------------------------|
| `PasswordUtil`    | SHA-256 with salt          |
| `JsonResponse`    | Standard JSON replies      |
| `ValidationUtil`  | Name, phone, date checks   |
| `AuditLog`        | Text file activity log     |

**filter**

| Class        | Purpose                                      |
|--------------|----------------------------------------------|
| `AuthFilter` | Redirects guests to login; 401 on APIs       |

**servlet** (these are the APIs)

| Class                | URL                   | Methods      |
|----------------------|-----------------------|--------------|
| `LoginServlet`       | `/api/login`          | POST         |
| `LogoutServlet`      | `/api/logout`         | POST         |
| `SessionServlet`     | `/api/session`        | GET          |
| `AppointmentServlet` | `/api/appointments`   | GET/POST/PUT |
| `BillServlet`        | `/api/bills`          | GET/POST     |
| `TreatmentServlet`   | `/api/treatments`     | GET          |
| `DentistServlet`     | `/api/dentists`       | GET          |
| `DashboardServlet`   | `/api/dashboard`      | GET          |

When creating a servlet by wizard, set **Name** to the class name and **URL mapping** as in the table. Then paste the repository source so `@WebServlet` matches.

### D5. Create web and config files

Right-click the folder → **New → File**:

| File | Location |
|------|----------|
| `db.properties` | `src/main/resources` |
| `web.xml` | `src/main/webapp/WEB-INF` |
| `index.html` | `src/main/webapp` |
| `dashboard.html` | `src/main/webapp` |
| `register.html` | `src/main/webapp` |
| `search.html` | `src/main/webapp` |
| `bill.html` | `src/main/webapp` |
| `help.html` | `src/main/webapp` |
| `style.css` | `src/main/webapp/css` |
| `app.js` | `src/main/webapp/js` |
| `auth.js` | `src/main/webapp/js` |
| `dashboard.js` | `src/main/webapp/js` |
| `register.js` | `src/main/webapp/js` |
| `appointments.js` | `src/main/webapp/js` |
| `bill.js` | `src/main/webapp/js` |
| `help.js` | `src/main/webapp/js` |
| `sunrise_dental.sql` | project `database` folder |

Paste each file’s contents from this repository.

---

## E. Run on Tomcat from IntelliJ

### Community Edition — Smart Tomcat plugin (usual student setup)

1. **Settings → Plugins → Marketplace** → search **Smart Tomcat** → Install → Restart.
2. **Run → Edit Configurations → + → Smart Tomcat**
3. Tomcat Server: folder of Tomcat 10.1
4. Deployment directory: `src/main/webapp` (or let the plugin use the Maven `target/sunrise-dental-clinic`)
5. Context path: `/sunrise-dental-clinic`
6. JRE: JDK 17+
7. Click **Run** (green triangle).
8. Browser: `http://localhost:8080/sunrise-dental-clinic/`

If the plugin deploys exploded `webapp` without compiled classes, first run **Maven → Lifecycle → package**, then set Deployment to `target/sunrise-dental-clinic`.

### Ultimate Edition — built-in Tomcat

1. **Run → Edit Configurations → + → Tomcat Server → Local**
2. Configure → Tomcat Home = your Tomcat 10.1 folder
3. Deployment tab → **+ → Artifact → sunrise-dental-clinic:war exploded**
4. Application context: `/sunrise-dental-clinic`
5. Run.

---

## F. First login test

1. XAMPP MySQL is green / running.
2. Open the login page.
3. Username `admin`, password `Admin@123`.
4. You should land on the dashboard with today’s sample appointments.
5. Register a new patient, search by the printed appointment number, preview bill, Save bill, Print receipt.
6. Click **Exit** to log out.

---

## G. Common errors

| Problem | Fix |
|---------|-----|
| `ClassNotFoundException: jakarta.servlet.Filter` | You started Tomcat 9. Use Tomcat 10.1. |
| `Communications link failure` | Start MySQL in XAMPP. Check port 3306. |
| `Access denied for user 'root'` | Put the correct password in `db.properties`. |
| Login always fails | Re-import `sunrise_dental.sql`. Do not change `password.salt`. |
| 404 on `/sunrise-dental-clinic/` | Context path must match the URL. |
| Maven dependencies red | Click Maven reload; check internet for Maven Central. |
| Port 8080 busy | Stop the other Tomcat/Skype, or change Tomcat HTTP port to 8081. |

---

## H. Menu map after login

1. **Dashboard** — counts and today’s list  
2. **New Appointment** — register patient  
3. **Find Appointment** — search by number  
4. **Calculate Bill** — total and print  
5. **Help** — staff instructions  
6. **Exit** — logout  

Clinic hours enforced by the server: **8:00 AM to 6:00 PM**. The same dentist cannot have two non-cancelled visits at the same date and time.
