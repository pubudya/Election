# Election Management System - User Manual 🇱🇰

## Table of Contents
1. [System Overview](#system-overview)
2. [Getting Started](#getting-started)
3. [User Roles and Access](#user-roles-and-access)
4. [Voter Guide](#voter-guide)
5. [GN Officer Guide](#gn-officer-guide)
6. [Admin Guide](#admin-guide)
7. [Security Features](#security-features)
8. [Troubleshooting](#troubleshooting)
9. [Contact Information](#contact-information)

---

## System Overview

The **Automated Sri Lankan Presidential Election System** is a secure, full-stack web application designed to manage presidential elections in Sri Lanka. The system provides digital voter registration, secure multi-factor authentication, electronic voting, automated counting, and transparent result reporting.

### Key Features
- **Multi-language Support**: English, Sinhala, and Tamil
- **Multi-factor Authentication**: NIC + PIN + OTP verification
- **Preference-based Voting**: 1st, 2nd, and 3rd preference selection
- **Real-time Results**: Live election results and statistics
- **Comprehensive Security**: CSRF protection, session hardening, audit trails
- **Sri Lankan Theme**: Authentic national colors and branding

---

## Getting Started

### System Requirements
- **Web Browser**: Chrome, Firefox, Safari, or Edge (latest versions)
- **Internet Connection**: Stable internet connection required
- **Device**: Desktop, laptop, tablet, or mobile device

### Accessing the System
1. Open your web browser
2. Navigate to: `http://localhost/Election/public/`
3. The system homepage will display with navigation options

### Language Selection
- Click on language buttons (EN/SI/TA) to switch between English, Sinhala, and Tamil
- Language preference is saved for your session

---

## User Roles and Access

The system has three main user roles, each with different permissions and access levels:

### 1. Voter 👤
- **Purpose**: Cast votes in presidential elections
- **Access**: Public registration and voting interface
- **Permissions**: View candidates, cast votes, view results

### 2. GN Officer 🏛️
- **Purpose**: Manage voter registration and verification
- **Access**: Officer dashboard with administrative tools
- **Permissions**: Register voters, approve registrations, manage voter data

### 3. Admin 👑
- **Purpose**: System administration and election management
- **Access**: Full administrative dashboard
- **Permissions**: Manage officers, candidates, elections, and system settings

---

## Voter Guide

### Registration Process

#### Option 1: Self-Registration
1. **Navigate to Registration**
   - Go to `public/register.php`
   - Click "Register as Voter"

2. **Fill Registration Form**
   - **Full Name**: Enter your complete legal name
   - **NIC Number**: Enter your National Identity Card number
   - **Date of Birth**: Select your birth date
   - **Phone Number**: Enter your 10-digit mobile number
   - **Email**: Provide a valid email address
   - **Address**: Enter your complete residential address
   - **Division**: Select your Grama Niladhari division
   - **District**: Select your district
   - **Province**: Select your province

3. **Create PIN**
   - Choose a 6-digit PIN (remember this for voting)
   - Confirm your PIN

4. **Submit Registration**
   - Click "Register"
   - Wait for GN Officer approval

#### Option 2: GN Officer Registration
- Visit your local Grama Niladhari office
- Officer will register you directly in the system
- You'll receive your PIN and login credentials

### Login and Voting

#### Step 1: Login
1. Go to `public/login.php`
2. Select "Voter" role
3. Enter your NIC number
4. Enter your 6-digit PIN
5. Click "Login"

#### Step 2: Access Voting Dashboard
1. After successful login, you'll be redirected to the voter dashboard
2. Verify your information is correct
3. Check if an election is currently active

#### Step 3: Cast Your Vote
1. Click "Vote Now" button
2. **Voting Interface**:
   - **Time Limit**: You have 5 minutes to complete your vote
   - **Preference Selection**: Choose your 1st, 2nd, and 3rd preferences
   - **Candidate Information**: View photos, party details, and symbols
   - **Selection Process**:
     - Click "1st" for your top choice
     - Click "2nd" for your second choice
     - Click "3rd" for your third choice (optional)

3. **Review and Submit**:
   - Review your selections
   - Click "Submit Vote"
   - Confirm your choices in the popup
   - Click "Submit Now"

#### Step 4: Confirmation
- You'll see a success message
- Your vote is recorded and cannot be changed
- You'll be redirected to the success page

### Viewing Results
- Go to `public/results.php` to view election results
- Results are displayed in real-time during and after elections
- View candidate statistics and vote counts

---

## GN Officer Guide

### Accessing Officer Dashboard
1. Go to `public/login.php`
2. Select "GN Officer" role
3. Enter your Officer NIC
4. Enter your password
5. Click "Login"

### Dashboard Features

#### Tab 1: Register Voter
- **Manual Registration**: Enter voter details directly
- **Required Fields**:
  - Full Name, NIC, Date of Birth
  - Phone, Email, Address
  - Division, District, Province
- **PIN Generation**: System automatically generates a 6-digit PIN
- **Email Notification**: Voter receives registration confirmation

#### Tab 2: Approve/Deny Registrations
- **Pending Registrations**: View all pending voter registrations
- **Verification Process**:
  - Review voter information
  - Verify NIC and address details
  - Click "Approve" or "Deny"
- **Bulk Actions**: Approve/deny multiple registrations at once

#### Tab 3: Manage Voters
- **Search Voters**: Find voters by NIC, name, or division
- **Edit Information**: Update voter details if needed
- **View Status**: Check registration and voting status
- **Deactivate**: Temporarily disable voter accounts if necessary

### Best Practices
- **Verify Identity**: Always verify NIC numbers and addresses
- **Documentation**: Keep records of all registrations and approvals
- **Communication**: Inform voters of their PIN and login process
- **Security**: Never share officer credentials

---

## Admin Guide

### Accessing Admin Dashboard
1. Go to `public/login.php`
2. Select "Admin" role
3. Enter admin username
4. Enter admin password
5. Click "Login"

### Dashboard Features

#### Tab 1: Manage GN Officers
- **Add Officer**: Create new officer accounts
  - Full Name, NIC, Division, District, Province
  - Email, Phone, Password
- **Edit Officer**: Modify existing officer information
- **Delete Officer**: Remove officer accounts (with confirmation)
- **View All Officers**: Complete list with status and contact info

#### Tab 2: Manage Candidates
- **Add Candidate**: Register new presidential candidates
  - Personal Information: Name, Photo, Party
  - Party Details: Logo, Symbol, Color
  - Regional Information: Province, District
- **Edit Candidate**: Update candidate information
- **Delete Candidate**: Remove candidates (with confirmation)
- **Photo Management**: Upload and manage candidate photos

#### Tab 3: Launch Elections
- **Election Configuration**:
  - Election Name and Description
  - Start Date and Time
  - End Date and Time
  - Candidate Selection
- **Election Status**: Active, Pending, or Completed
- **Results Publishing**: Control when results become public

#### Tab 4: Reviews & Queries
- **User Feedback**: View and respond to voter inquiries
- **System Issues**: Track and resolve technical problems
- **Communication**: Send announcements to users

### System Management
- **Database Maintenance**: Monitor system performance
- **Security Logs**: Review access and security events
- **Backup Management**: Ensure data integrity
- **User Support**: Assist with technical issues

---

## Security Features

### Multi-Factor Authentication
- **NIC Verification**: National Identity Card validation
- **PIN Protection**: 6-digit personal identification number
- **OTP Verification**: One-time password for additional security

### Session Security
- **Automatic Timeout**: Sessions expire after inactivity
- **Secure Cookies**: Encrypted session data
- **IP Tracking**: Monitor login locations

### Data Protection
- **Encryption**: All sensitive data is encrypted
- **Audit Trails**: Complete logging of all system activities
- **Access Control**: Role-based permissions
- **CSRF Protection**: Cross-site request forgery prevention

### Best Security Practices
- **Strong PINs**: Use unique, memorable PINs
- **Secure Login**: Never share credentials
- **Logout**: Always log out when finished
- **Device Security**: Use trusted devices only

---

## Troubleshooting

### Common Issues

#### Login Problems
- **Invalid NIC/PIN**: Double-check your credentials
- **Account Locked**: Too many failed attempts - wait 15 minutes
- **Session Expired**: Refresh page and login again

#### Registration Issues
- **NIC Already Exists**: Contact GN Officer for assistance
- **Email Not Received**: Check spam folder or contact support
- **Form Errors**: Ensure all required fields are completed

#### Voting Problems
- **Time Expired**: Request more time using the extension button
- **Selection Issues**: Clear selections and start over
- **Submission Failed**: Check internet connection and try again

#### Technical Issues
- **Page Not Loading**: Clear browser cache and cookies
- **Slow Performance**: Check internet connection
- **Error Messages**: Note the error code and contact support

### Getting Help
1. **Check System Status**: Look for announcements on the homepage
2. **Contact GN Officer**: For registration and approval issues
3. **Admin Support**: For technical and system problems
4. **Documentation**: Refer to this manual for guidance

---

## Contact Information

### Support Channels
- **GN Office**: Visit your local Grama Niladhari office
- **System Admin**: Contact system administrators for technical issues
- **Emergency**: For urgent election-related matters

### Office Hours
- **GN Offices**: Monday-Friday, 8:00 AM - 4:00 PM
- **System Support**: 24/7 during election periods
- **Admin Support**: Business hours (Monday-Friday)

### Important Notes
- **Election Dates**: Check official announcements for voting periods
- **Registration Deadlines**: Complete registration before election starts
- **Voting Hours**: Elections typically run for 12-16 hours
- **Results**: Available after voting closes and counting completes

---

## System Updates and Maintenance

### Regular Maintenance
- **Security Updates**: Applied automatically for system protection
- **Feature Updates**: New capabilities added periodically
- **Performance Improvements**: Ongoing optimization for better user experience

### User Notifications
- **System Announcements**: Posted on homepage and dashboards
- **Email Notifications**: Important updates sent to registered users
- **SMS Alerts**: Critical information sent via text message

---

## Conclusion

This Election Management System provides a secure, efficient, and transparent way to participate in Sri Lankan presidential elections. By following this manual, you can successfully register, vote, and manage the electoral process.

**Remember**: Your vote is your voice in democracy. Use this system responsibly and securely to participate in shaping Sri Lanka's future.

---

*Last Updated: December 2024*  
*Version: 1.0*  
*System: Automated Sri Lankan Presidential Election System*
