<?php
include 'dbcon.php';

// Fetch ATC codes
$atc_codes = [];
$atc_result = $conn->query("SELECT atc_code FROM atc"); // Replace 'atc_codes_table' with your actual table name
if ($atc_result->num_rows > 0) {
    while($row = $atc_result->fetch_assoc()) {
        $atc_codes[] = $row['atc_code'];
    }
}

// Fetch VAT/Non-VAT options
$vat_nonvat_options = [];
$vat_result = $conn->query("SELECT vat_nonvat FROM vat_nonvat_table"); // Replace 'vat_nonvat_table' with your actual table name
if ($vat_result->num_rows > 0) {
    while($row = $vat_result->fetch_assoc()) {
        $vat_nonvat_options[] = $row['vat_nonvat'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Payee</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="assets/css/dashboard-styles.css">
</head>
<body>
    <div class="dashboard-container">

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
                <div class="card">
                    <div class="card-header">
                        <h1>Create New Payee</h1>
                    </div>
                    <div class="card-body">
                        <form action="insert_payee.php" method="post">
                            <div class="form-group">
                                <label for="seq_no">Sequence Number:</label>
                                <input type="number" class="form-control" id="seq_no" name="seq_no">
                            </div>
                            <div class="form-group">
                                <label for="taxpayer_id">Taxpayer ID:</label>
                                <input type="text" class="form-control" id="taxpayer_id" name="taxpayer_id">
                            </div>
                            <div class="form-group">
                                <label for="registered_name">Registered Name:</label>
                                <input type="text" class="form-control" id="registered_name" name="registered_name">
                            </div>
                            <div class="form-group">
                                <label for="name_of_payees">Name of Payees:</label>
                                <input type="text" class="form-control" id="name_of_payees" name="name_of_payees">
                            </div>
                            <div class="form-group">
                                <label for="amount_of_income_payment">Amount of Income Payment:</label>
                                <input type="number" step="0.01" class="form-control" id="amount_of_income_payment" name="amount_of_income_payment">
                            </div>
                            <div class="form-group">
                                <label for="rate_of_tax">Rate of Tax:</label>
                                <input type="number" step="0.01" class="form-control" id="rate_of_tax" name="rate_of_tax">
                            </div>
                            <div class="form-group">
                                <label for="amount_of_tax_withheld">Amount of Tax Withheld:</label>
                                <input type="number" step="0.01" class="form-control" id="amount_of_tax_withheld" name="amount_of_tax_withheld">
                            </div>
                            <div class="form-group">
                                <label for="payees_address">Payees Address:</label>
                                <input type="text" class="form-control" id="payees_address" name="payees_address">
                            </div>
                            <div class="form-group">
                                <label for="payees_zipcode">Payees Zipcode:</label>
                                <input type="number" class="form-control" id="payees_zipcode" name="payees_zipcode">
                            </div>
                            <div class="form-group">
                                <label for="atc_code">ATC Code:</label>
                                <select class="form-control" id="atc_code" name="atc_code">
                                    <?php foreach($atc_codes as $code): ?>
                                        <option value="<?php echo $code; ?>"><?php echo $code; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="vat_nonvat">VAT/Non-VAT:</label>
                                <select class="form-control" id="vat_nonvat" name="vat_nonvat" required>
                                    <?php foreach($vat_nonvat_options as $option): ?>
                                        <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>