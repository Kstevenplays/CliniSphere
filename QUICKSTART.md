<!-- Installation and Setup Guide for CliniSphere -->
# CliniSphere - Quick Start Guide

## 🚀 Getting Started in 5 Minutes

### Step 1: Verify XAMPP is Running
- Open XAMPP Control Panel
- Click "Start" on both Apache and MySQL

### Step 2: Access the Application
Open your browser and navigate to:
```
http://localhost/CliniSphere/
```

### Step 3: Create Your Account
1. Click "Register" button
2. Fill in your details:
   - First Name
   - Last Name
   - Email
   - Phone (optional)
   - Password
3. Click "Register"

### Step 4: Create Admin Account (for testing)
1. Register as a user first
2. Open phpMyAdmin: http://localhost/phpmyadmin
3. Click on "clinisphere" database
4. Click on "users" table
5. Find your newly registered user
6. Edit the row and change `role` from "patient" to "admin"
7. Click Save

### Step 5: Add a Doctor
1. Login with your admin account
2. You'll be redirected to admin dashboard (or go to `/admin/`)
3. Click "Doctors" in the navigation
4. Click "Add Doctor"
5. Fill in doctor details:
   - First Name: "John"
   - Last Name: "Smith"
   - Email: "doctor@clinic.com"
   - Specialization: "General Practitioner"
6. Click "Save"

### Step 6: Add Time Slots
1. Click "Time Slots" in admin navigation
2. Select the doctor from dropdown
3. Select a date (choose future date)
4. Select a time (e.g., 09:00)
5. Click "Add Slot"
6. Repeat for multiple time slots

### Step 7: Book an Appointment (as patient)
1. Logout from admin account
2. Login with your patient account
3. Click "Book Appointment"
4. Select doctor from dropdown
5. Select appointment date
6. Select available time slot
7. Enter reason for visit (optional)
8. Click "Book Appointment"

### Step 8: Approve Appointment (as admin)
1. Logout and login as admin
2. Go to Admin Dashboard (/admin/)
3. You'll see pending appointments
4. Click "Review" button
5. Optionally add notes
6. Click "Approve" to confirm the appointment
7. Patient will receive approval email (if configured)

## 📋 Default Test Accounts

### Patient Account
- Email: patient@test.com
- Password: password123

### Admin Account
- Email: admin@test.com
- Password: password123

To create these, use the registration system and then update the role to 'admin' for the admin account.

## 🔧 Email Configuration (Optional)

To enable email notifications:

1. Open `config/config.php`
2. Update these values:
```php
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-specific-password');
define('SENDER_EMAIL', 'your-email@gmail.com');
```

3. For Gmail:
   - Enable 2-Factor Authentication on your Gmail account
   - Generate an App Password at: https://myaccount.google.com/apppasswords
   - Use the generated password (without spaces)

## 📱 Key Pages

### For Patients
- Homepage: `/` - Welcome page with doctors preview
- Booking: `/booking.php` - Schedule appointments
- My Appointments: `/my_appointments.php` - View booked appointments
- Login: `/login.php` - Sign in
- Register: `/register.php` - Create new account

### For Admins
- Dashboard: `/admin/` - View pending appointments
- Doctors: `/admin/doctors.php` - Manage doctors
- Time Slots: `/admin/timeslots.php` - Manage appointment times

## 🎯 User Workflows

### Patient Booking Flow
```
1. Register Account
2. Login
3. Click "Book Appointment"
4. Select Doctor → Select Date → Select Time
5. Submit
6. Receive Confirmation Email (if configured)
7. Wait for Admin Approval
8. Receive Approval/Rejection Email
9. See status in "My Appointments"
```

### Admin Approval Flow
```
1. Login as Admin
2. Go to Dashboard
3. Review pending appointments
4. Click "Review"
5. Add notes if needed
6. Click "Approve" or "Reject"
7. Patient receives email notification
```

### Doctor Management Flow
```
1. Login as Admin
2. Go to Doctors page
3. Click "Add Doctor"
4. Fill in details
5. Go to Time Slots page
6. Add available appointment times
7. Doctor is ready to accept appointments
```

## 💡 Tips & Tricks

- **Set Time Slots in Bulk**: Add multiple 30-minute slots for the same doctor and date
- **Appointment Reminders**: Patients should receive a reminder 24 hours before (can be automated)
- **Doctor Availability**: Mark doctors as inactive in the doctors table to prevent new bookings
- **Reschedule**: Rejecting an appointment frees up the time slot for rebooking

## 🐛 Common Issues

### "Connection failed: Connection refused"
- Make sure MySQL is running in XAMPP
- Check that Apache is also running

### "Page not found" / "404 Error"
- Ensure project folder is named exactly "CliniSphere"
- Verify URL is: http://localhost/CliniSphere/

### Email not sending
- Email features are optional
- System works without email configuration
- Configure SMTP if you need email functionality

### Cannot access admin pages
- Make sure your user role is set to "admin" in database
- You cannot create admin accounts through registration
- Must manually update role via phpMyAdmin

## 📚 Database Tables

The system automatically creates these tables:
- `users` - Patient, doctor, and admin accounts
- `doctors` - Doctor profiles and specializations
- `time_slots` - Available appointment slots
- `appointments` - Booking records with status
- `email_logs` - Email sending history
- `audit_log` - Admin action tracking

## 🔐 Security Notes

- Passwords are hashed using bcrypt
- All database queries use prepared statements
- Session timeouts after 30 minutes of inactivity
- Admin approval required for all appointments
- All admin actions are logged

## 🎓 Learning Resources

The code includes well-structured OOP classes:
- `EmailService` - Email sending and logging
- `UserService` - User authentication
- `AppointmentService` - Appointment management
- `DoctorService` - Doctor operations

Each class handles a specific domain of the application.

---

**Need Help?** Check the full README.md for detailed documentation.
