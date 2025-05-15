<?php
include 'dbcon.php';

// Fetch ATC codes
$atc_codes = [];
$atc_result = $conn->query("SELECT atc_code FROM atc"); // Replace 'atc_codes_table' with your actual table name
if ($atc_result->num_rows > 0) {
    while ($row = $atc_result->fetch_assoc()) {
        $atc_codes[] = $row['atc_code'];
    }
}

// Fetch VAT/Non-VAT options
$vat_nonvat_options = [];
$vat_result = $conn->query("SELECT vat_nonvat FROM vat_nonvat_table"); // Replace 'vat_nonvat_table' with your actual table name
if ($vat_result->num_rows > 0) {
    while ($row = $vat_result->fetch_assoc()) {
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
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="assets/css/dashboard-styles.css">
    <style>
        :root {
            --primary-green: #2e7d32;
            --light-green: #81c784;
            --lighter-green: #e8f5e9;
            --dark-green: #1b5e20;
            --accent-green: #4caf50;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .card-header {
            background-color: var(--primary-green);
            color: white;
            border-radius: 8px 8px 0 0 !important;
            padding: 15px 20px;
            width: 730px;
        }

        .card-header h1 {
            font-size: 1.8rem;
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            padding: 25px;
        }

        .form-group label {
            font-weight: 500;
            color: var(--dark-green);
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
        }

        .form-control:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
        }

        .btn-primary {
            background-color: var(--accent-green);
            border-color: var(--accent-green);
            border-radius: 4px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: var(--dark-green);
            border-color: var(--dark-green);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            border-radius: 4px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-1px);
        }

        .section-title {
            color: var(--dark-green);
            font-weight: 600;
            margin: 20px 0 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--light-green);
        }

        .form-check-input:checked {
            background-color: var(--accent-green);
            border-color: var(--accent-green);
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
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

        <div class="main-content">
            <div class="dashboard-content">
                <div class="card">
                    <div class="card-header">
                        <h1>Create New Payee</h1>
                    </div>
                    <div class="card-body">
                        <form action="insert_payee.php" method="post">
                            <h5 class="section-title">PAYEE INFORMATION</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="seq_no">SEQ NO</label>
                                        <input type="text" class="form-control" id="seq_no" name="seq_no">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="taxpayer_id">TAXPAYER IDENTIFICATION NUMBER</label>
                                        <input type="text" class="form-control" id="taxpayer_id" name="taxpayer_id">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="registered_name">REGISTERED NAME</label>
                                        <input type="text" class="form-control" id="registered_name" name="registered_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name_of_payees">NAME OF PAYEES</label>
                                        <input type="text" class="form-control" id="name_of_payees" name="name_of_payees">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="payees_address">ADDRESS</label>
                                        <input type="text" class="form-control" id="payees_address" name="payees_address">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="payees_zipcode">ZIPCODE</label>
                                        <input type="text" class="form-control" id="payees_zipcode" name="payees_zipcode">
                                    </div>
                                </div>
                            </div>

                            <h5 class="section-title">PAYMENT INFORMATION</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="amount_of_income_payment">AMOUNT OF INCOME PAYMENT</label>
                                        <input type="text" class="form-control" id="amount_of_income_payment" name="amount_of_income_payment">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="rate_of_tax">RATE OF TAX</label>
                                        <input type="text" class="form-control" id="rate_of_tax" name="rate_of_tax">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="amount_of_tax_withheld">AMOUNT OF TAX WITHHELD</label>
                                        <input type="text" class="form-control" id="amount_of_tax_withheld" name="amount_of_tax_withheld">
                                    </div>
                                </div>
                            </div>


                            <h5 class="section-title">TAX INFORMATION</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="atc_code">ATC Code:</label>
                                        <select class="form-control" id="atc_code" name="atc_code" required>
                                            <option value="" disabled selected>Select an ATC Code</option>
                                            <?php foreach ($atc_codes as $code): ?>
                                                <option value="<?php echo $code; ?>"><?php echo $code; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vat_nonvat">VAT/Non-VAT:</label>
                                        <select class="form-control" id="vat_nonvat" name="vat_nonvat" required>
                                            <option value="" disabled selected>Select VAT/Non-VAT</option>
                                            <?php foreach ($vat_nonvat_options as $option): ?>
                                                <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary">Create Payee</button>
                                <a href="export_bir_pdf.php" class="btn btn-secondary ml-3">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const payorOptions = <?php echo json_encode($payorOptions); ?>;

        $(document).ready(function() {
            // Handle payor selection
            $('#payor_tin').on('change', function() {
                const selectedTin = $(this).val();
                const selectedPayor = payorOptions.find(payor => payor.payor_tin === selectedTin);
                if (selectedPayor) {
                    $('#payor_name').val(selectedPayor.payor_name);
                    $('#payor_address').val(selectedPayor.payor_address);
                    $('#payor_zipcode').val(selectedPayor.payor_zipcode);
                } else {
                    $('#payor_name').val('');
                    $('#payor_address').val('');
                    $('#payor_zipcode').val('');
                }
            });

            // Calculate tax withheld when amount or rate changes
            $('#amount_of_income_payment, #rate_of_tax').on('input', function() {
                const amount = parseFloat($('#amount_of_income_payment').val()) || 0;
                const rate = parseFloat($('#rate_of_tax').val()) || 0;
                const taxWithheld = amount * (rate / 100);
                $('#amount_of_tax_withheld').val(taxWithheld.toFixed(2));
            });
        });
    </script>
</body>

</html>