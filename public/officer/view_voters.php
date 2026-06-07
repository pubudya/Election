<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registered Voters - Officer - Sri Lankan Election System</title>
    <link rel="icon" href="../assets/images/sri-lanka-flag.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/sri-lanka-theme.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <div class="flag-bar">
        <div class="maroon"></div>
        <div class="gold"></div>
        <div class="green"></div>
        <div class="orange"></div>
    </div>
    <div class="container mt-5">
        <div class="secure-container">
            <div class="secure-header text-center mb-4">
                <img src="../assets/images/sri-lanka-flag.png" alt="Sri Lanka Flag" class="logo mb-2">
                <h2>Registered Voters</h2>
            </div>
            <table id="votersTable" class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>NIC</th>
                        <th>Name</th>
                        <th>Division</th>
                        <th>District</th>
                        <th>Province</th>
                        <th>Registered</th>
                        <th>Has Voted</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Voter rows will be loaded here by PHP/JS -->
                    <tr><td colspan="8" class="text-center">No voters found.</td></tr>
                </tbody>
            </table>
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
</body>
</html> 