<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - CliniSphere Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1>CliniSphere - Admin</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="doctors.php" class="active">Doctors</a></li>
                <li><a href="timeslots.php">Time Slots</a></li>
                <li><a href="#" onclick="logout()">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2>Manage Doctors</h2>
            <button class="btn btn-primary" onclick="openAddDoctorForm()">Add Doctor</button>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Specialization</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="doctors-table-body">
                <tr><td colspan="5">Loading...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Doctor Modal -->
    <div id="doctorModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 8px; width: 90%; max-width: 500px;">
            <h3 id="modal-title">Add Doctor</h3>
            <div class="form-group">
                <label for="doc-first-name">First Name:</label>
                <input type="text" id="doc-first-name">
            </div>
            <div class="form-group">
                <label for="doc-last-name">Last Name:</label>
                <input type="text" id="doc-last-name">
            </div>
            <div class="form-group">
                <label for="doc-email">Email:</label>
                <input type="email" id="doc-email">
            </div>
            <div class="form-group">
                <label for="doc-specialization">Specialization:</label>
                <input type="text" id="doc-specialization">
            </div>
            <div style="display: flex; gap: 1rem;">
                <button class="btn btn-primary" onclick="saveDoctor()">Save</button>
                <button class="btn" style="background-color: #95a5a6;" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script src="../js/doctors-admin.js"></script>
</body>
</html>
