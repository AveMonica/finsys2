<?php
session_start();
include("dbcon.php");

if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['fund_id'])) {
    echo "<script>alert('No fund selected!'); window.location.href='dashboard.php';</script>";
    exit();
}

$fund_id = intval($_GET['fund_id']);
$fundQuery = mysqli_query($conn, "SELECT fund_name FROM funds WHERE fund_id = '$fund_id'");
$fund = mysqli_fetch_assoc($fundQuery);
$fund_name = $fund['fund_name'] ?? 'Unknown Fund';

$result = mysqli_query($conn, "SELECT * FROM checks WHERE fund_id = '$fund_id'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $fund_name; ?> Table</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/dashboard-styles.css">
    <link rel="stylesheet" href="assets/css/dashboard-styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            /* margin: 20px; */
        }

        .import-form {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .filter-section {
            margin-bottom: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            padding: 20px;
            width: 50%;
            margin: 10% auto;
            border-radius: 5px;
        }

        .close {
            float: right;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">


        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="assets/img/2020-nia-logo.svg" width="45px" alt="Nia Logo 2020">
                <span>Dashboard</span>
            </div>
            <ul class="sidebar-nav">
                <li><a href="dashboard.php"><i class="bi bi-wallet"></i> Funds</a></li>
                <li><a href="export_bir_pdf.php"><i class="bi bi-file-earmark-spreadsheet"></i> BIR FORM 2307</a></li>
                <li><a href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <div class="dashboard-content">
                <h2>Checks Table for <?php echo $fund_name; ?></h2>
                <a href="dashboard.php">Back to Dashboard</a>

                <!-- Import Excel Form -->
                <div class="import-form">
                    <h3>Import Excel File</h3>
                    <form action="import.php?fund_id=<?php echo $fund_id; ?>" method="POST" enctype="multipart/form-data">
                        <input type="file" name="file" required>
                        <button type="submit" name="import">Import Excel</button>
                    </form>
                </div>

                <!-- Date Range Filter -->
                <!-- <div class="filter-section">
                    <label>Start Date:</label>
                    <input type="date" id="startDate">
                    <label>End Date:</label>
                    <input type="date" id="endDate">
                    <button onclick="filterByDate()">Filter</button>
                </div> -->

                <table id="checksTable" class="display">
                    <thead>
                        <tr>
                            <th>Check Date</th>
                            <th>Serial No.</th>
                            <th>Payroll No.</th>
                            <th>Payee</th>
                            <th>Particulars</th>
                            <th>Account Name</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>ASA # Obligation</th>
                            <th>Running Balance</th>
                            <th>Registered Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo date("d-M-y", strtotime($row['check_date'])); ?></td>
                                <td><?php echo $row['check_serial_number']; ?></td>
                                <td><?php echo $row['dv_payroll_number']; ?></td>
                                <td><?php echo $row['payee']; ?></td>
                                <td><?php echo $row['particulars']; ?></td>
                                <td><?php echo $row['account_name']; ?></td>
                                <td><?php echo number_format($row['debit'], 2); ?></td>
                                <td><?php echo number_format($row['credit'], 2); ?></td>
                                <td><?php echo $row['asa_obligation_number']; ?></td>
                                <td><?php echo number_format($row['running_balance'], 2); ?></td>
                                <td><?php echo $row['registered_name']; ?></td>
                                <td>
                                    <!-- <button onclick='viewDetails(<?php echo json_encode($row); ?>)'>View</button> -->
                                    <button onclick='openEditModal(<?php echo json_encode($row); ?>)'>Edit</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>

                <!-- View Modal (Your existing one, kept as-is) -->
                <div id="viewModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeViewModal()">&times;</span>
                        <h3>View Details</h3>
                        <p id="viewDetailsText"></p>
                    </div>
                </div>

                <!-- Edit Modal (New one for editing registered_name) -->
                <div id="editModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeEditModal()">&times;</span>
                        <h3>Edit Registered Name</h3>
                        <input type="hidden" id="editParticulars">
                        <label>Registered Name:</label>
                        <input type="text" id="editRegisteredName">
                        <button onclick="saveEdit()">Save</button>
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                $('#checksTable').DataTable();
            });
            // NOT WORKING DATE RANGE UPDATE
            function filterByDate() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();
                var table = $('#checksTable').DataTable();

                table.column(0).search(startDate + '|' + endDate, true, false).draw();
            }
            // display every records or data here
            function viewDetails(row) {
                document.getElementById('viewDetailsText').innerHTML = `
        <strong>Payee:</strong> ${row.payee} <br>
        <strong>Particulars:</strong> ${row.particulars} <br>
        <strong>Registered Name:</strong> ${row.registered_name}
    `;
                document.getElementById('viewModal').style.display = 'block';
            }

            function closeViewModal() {
                document.getElementById('viewModal').style.display = 'none';
            }

            function openEditModal(row) {
                $('#editParticulars').val(row.particulars);
                $('#editRegisteredName').val(row.registered_name);
                $('#editModal').show();
            }

            function closeEditModal() {
                $('#editModal').hide();
            }

            function saveEdit() {
                var particulars = $('#editParticulars').val();
                var registeredName = $('#editRegisteredName').val();

                $.ajax({
                    url: 'update.php',
                    type: 'POST',
                    data: {
                        particulars: particulars,
                        registered_name: registeredName
                    },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    }
                });
            }
        </script>

</body>

</html>