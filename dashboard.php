<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

include("dbcon.php");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fund = trim(mysqli_real_escape_string($conn, $_POST["fund"]));
    $sub_fund = trim(mysqli_real_escape_string($conn, $_POST["sub_fund"]));

    // Construct fund_name format
    if (empty($sub_fund)) {
        $fund_name = $fund; // No square brackets if there's no sub-fund
    } else {
        $fund_name = "[$fund] $sub_fund"; // Use square brackets if sub-fund exists
    }

    // Insert into the funds table
    $query = "INSERT INTO funds (fund_name) VALUES ('$fund_name')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Fund added successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding fund.');</script>";
    }
}

// Fetch all funds
$result = mysqli_query($conn, "SELECT * FROM funds ORDER BY fund_id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINSYS | Dashboard</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/dashboard-styles.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="assets/img/2020-nia-logo.svg" width="45px" alt="Nia Logo 2020">
                <span>FinanceSystem</span>
            </div>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php"><i class="bi bi-wallet"></i> Funds</a></li>
                <li><a href="export_bir_pdf.php"><i class="bi bi-file-earmark-spreadsheet"></i> BIR FORM 2307</a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <!-- <div class="search-bar">
                    <input type="text" placeholder="Search...">
                    <i class="bi bi-search"></i>
                </div> -->
                <div class="user-profile">
                    <!-- Dark/Light Mode Toggle -->
                    <!-- <button id="theme-toggle" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-moon"></i> 
                    </button> -->
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <h1>Funds List</h1>
                <div class="cards-section">
                    <!-- Create New Fund Tile -->
                    <div class="card create-fund">
                        <div class="card-icon">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <div class="card-content">
                            <h3>Create New Fund</h3>
                            <p>Start a new fund allocation.</p>
                            <!-- Button to Trigger Modal -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFundModal">
                                Create Fund
                            </button>
                        </div>
                    </div>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="card">
                            <div class="card-icon">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="card-content">
                                <h3><?php echo $row['fund_name']; ?></h3>
                                <!-- <p>Total Records: (display here the total records of each funds)</p> -->
                                <a href="checks_table.php?fund_id=<?php echo $row['fund_id']; ?>" class="btn btn-outline-primary">View Table</a>
                            </div>
                        </div>
                    <?php endwhile; ?>


                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Create New Fund -->
    <div style="margin-top: 110px;" class="modal fade" id="createFundModal" tabindex="-1" aria-labelledby="createFundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createFundModalLabel">Create New Fund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form for Creating New Fund -->
                    <form method="POST" action="dashboard.php">
                        <div class="mb-3">
                            <label for="fund" class="form-label">Fund Name:</label>
                            <input type="text" class="form-control" id="fund" name="fund" required>
                        </div>
                        <div class="mb-3">
                            <label for="sub_fund" class="form-label">Sub-Fund Name (optional):</label>
                            <input type="text" class="form-control" id="sub_fund" name="sub_fund">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn" style="background-color: #43a047; color: #fff;">Create Fund</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.js"></script>
    <!-- Custom JS for Dark/Light Mode Toggle -->
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;

        // Check saved theme from localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.classList.add(savedTheme);
            updateToggleIcon();
        }

        // Toggle Dark/Light Mode
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark-mode' : '');
            updateToggleIcon();
        });

        // Update Toggle Icon
        function updateToggleIcon() {
            const isDarkMode = body.classList.contains('dark-mode');
            themeToggle.innerHTML = isDarkMode ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>';
        }
    </script>
</body>

</html>