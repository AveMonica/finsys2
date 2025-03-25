<?php
include 'dbcon.php'; // Database connection

// Fetch payor and ATC options once
$payorQuery = "SELECT * FROM payor";
$payorResult = $conn->query($payorQuery);
$payorOptions = [];
while ($payorRow = $payorResult->fetch_assoc()) {
    $payorOptions[] = $payorRow;
}

$atcQuery = "SELECT atc_code, atc_description FROM atc";
$atcResult = $conn->query($atcQuery);
$atcOptions = [];
while ($atcRow = $atcResult->fetch_assoc()) {
    $atcOptions[] = $atcRow;
}

// Fetch alphalist data
$sql = "SELECT a.seq_no, a.taxpayer_id, a.registered_name, a.name_of_payees, a.payees_address, 
               a.payees_zipcode, a.atc_code, a.amount_of_income_payment, a.rate_of_tax, 
               a.amount_of_tax_withheld, a.vat_nonvat, v.vat_nonvat_description
        FROM alphalist_of_payees a
        JOIN vat_nonvat_table v ON a.vat_nonvat = v.vat_nonvat";
$result = $conn->query($sql);
$alphalistData = [];
while ($row = $result->fetch_assoc()) {
    $alphalistData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel File</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="assets/css/dashboard-styles.css">
    <link rel="stylesheet" href="assets/css/export_bir-styles.css">
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

    <!-- Top Bar -->
    <div class="top-bar d-flex justify-content-end" style="border-radius: 0;">
        <div class="top-bar-container d-flex align-items-center">
            <form action="create_new_payee.php" method="get" style="margin: 0;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Create New Payee</button>
            </form>
            <button class="btn btn-secondary ml-2" data-toggle="modal" data-target="#uploadExcelModal">
                <i class="fas fa-file-import"></i> Import Excel File
            </button>
        </div>
    </div>

    <div class="dashboard-content">

    <h2>Alphalist of Payees</h2>
    <table id="dataTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>SEQ NO</th>
                <th>TAXPAYER ID</th>
                <th>REGISTERED NAME</th>
                <th>NAME OF PAYEES</th>
                <th>ADDRESS</th>
                <th>ZIPCODE</th>
                <th>ATC CODE</th>
                <th>INCOME PAYMENT</th>
                <th>TAX RATE</th>
                <th>TAX WITHHELD</th>
                <th>VAT TYPE</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($alphalistData as $row) {
                $amount_of_income_payment = number_format($row['amount_of_income_payment'], 2);
                $amount_of_tax_withheld = number_format($row['amount_of_tax_withheld'], 2);
                echo "<tr>
                    <td data-label='SEQ NO'>{$row['seq_no']}</td>
                    <td data-label='TAXPAYER ID'>{$row['taxpayer_id']}</td>
                    <td data-label='REGISTERED NAME'>{$row['registered_name']}</td>
                    <td data-label='NAME OF PAYEES'>{$row['name_of_payees']}</td>
                    <td data-label='ADDRESS'>{$row['payees_address']}</td>
                    <td data-label='ZIPCODE'>{$row['payees_zipcode']}</td>
                    <td data-label='ATC CODE'>{$row['atc_code']}</td>
                    <td data-label='INCOME PAYMENT'>{$amount_of_income_payment}</td>
                    <td data-label='TAX RATE'>{$row['rate_of_tax']}</td>
                    <td data-label='TAX WITHHELD'>{$amount_of_tax_withheld}</td>
                    <td data-label='VAT TYPE'>{$row['vat_nonvat']}</td>
                    <td data-label='ACTIONS'>
                        <button class='btn btn-primary' data-toggle='modal' data-target='#exportModal' data-row='" . json_encode($row) . "'>
                            <i class='bi bi-file-earmark-arrow-up-fill'></i> Export
                        </button>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Modal for Upload Excel File -->
<div class="modal fade" id="uploadExcelModal" tabindex="-1" role="dialog" aria-labelledby="uploadExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadExcelModalLabel">Upload Excel File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="import_alphalist.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Choose Excel File</label>
                        <input type="file" class="form-control-file" id="file" name="file" required>
                    </div>
                    <button type="submit" class="btn btn-primary" name="import">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exportModalLabel">Export Data</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportForm" action="update_alphalist.php" method="post">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="period_from">Period From</label>
                                    <select class="form-control" id="period_from" name="period_from" required>
                                        <option value="" disabled selected>Select a quarter</option>
                                        <option value="Q1">First Quarter</option>
                                        <option value="Q2">Second Quarter</option>
                                        <option value="Q3">Third Quarter</option>
                                        <option value="Q4">Fourth Quarter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="month_from">Month From</label>
                                    <select class="form-control" id="month_from" name="month_from" required>
                                        <option value="">Select a month</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <h5 class="section-title">PAYER INFORMATION</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="seq_no">SEQ NO</label>
                                    <input type="text" class="form-control" id="seq_no" name="seq_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                        
                        <div class="form-group">
                            <label>Is the Supplier/Payee/Creditor a Corporation?</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="is_corporation_yes" name="is_corporation" value="yes">
                                <label class="form-check-label" for="is_corporation_yes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="is_corporation_no" name="is_corporation" value="no" checked>
                                <label class="form-check-label" for="is_corporation_no">No</label>
                            </div>
                        </div>
                        
                        <h5 class="section-title">PAYOR INFORMATION</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payor_tin">Payor TIN</label>
                                    <select class="form-control" id="payor_tin" name="payor_tin" required>
                                        <option value="" disabled selected>Select a payor tin</option>
                                        <?php foreach ($payorOptions as $payor): ?>
                                            <option value="<?php echo $payor['payor_tin']; ?>"><?php echo $payor['payor_tin']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payor_name">Payor Name</label>
                                    <input type="text" class="form-control" id="payor_name" name="payor_name" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="payor_address">Payor Address</label>
                                    <input type="text" class="form-control" id="payor_address" name="payor_address" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payor_zipcode">Payor Zipcode</label>
                                    <input type="text" class="form-control" id="payor_zipcode" name="payor_zipcode" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="vat_nonvat">VAT OR NON VAT</label>
                            <input type="text" class="form-control" id="vat_nonvat" name="vat_nonvat" readonly>
                        </div>
                        <div class="form-group" style="display: none;">
                            <label for="vat_nonvat_description">VAT OR NON VAT desc</label>
                            <input type="text" class="form-control" id="vat_nonvat_description" name="vat_nonvat_description" readonly>
                        </div>
                        
                        <h5 class="section-title">COMPUTATION</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="base_amount">Gross Amount or Base Amount</label>
                                    <input type="number" class="form-control" id="base_amount" name="base_amount">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="atc_code">ATC Code</label>
                                    <select class="form-control" id="atc_code" name="atc_code">
                                        <?php foreach ($atcOptions as $atc): ?>
                                            <option value="<?php echo $atc['atc_code']; ?>"><?php echo $atc['atc_code']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" id="period_from_hidden" name="period_from_hidden">
                        <input type="hidden" id="period_to_hidden" name="period_to_hidden">
                        <div class="text-right mt-4">
                        <button type="button" class="btn btn-secondary btn-sm" id="saveButton">Update Data</button>
                        <button type="submit" class="btn btn-primary btn-lg">Confirm Export</button>
                        
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    <script>
        // Pass PHP data to JavaScript
const payorOptions = <?php echo json_encode($payorOptions); ?>;
const atcOptions = <?php echo json_encode($atcOptions); ?>;
let selectedMonth;

$(document).ready(function() {
    $('#dataTable').DataTable({
        responsive: true,
        dom: '<"top"lf>rt<"bottom"ip><"clear">',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        initComplete: function() {
            $('.dataTables_filter input').addClass('form-control');
            $('.dataTables_length select').addClass('form-control');
        }
    });

    // Save button click handler
    $('#saveButton').on('click', function() {
        // Perform save/update/edit actions here
        // For example, you can submit the form via AJAX
        const formData = $('#exportForm').serialize();
        $.ajax({
            type: 'POST',
            url: 'update_alphalist.php', // Change this to your save/update URL
            data: formData,
            success: function(response) {
                alert('Data saved successfully!');
                $('#exportModal').modal('hide');
                window.location.href = 'export_bir_pdf.php'
            },
            error: function(error) {
                alert('Failed to save data. Please try again.');
            }
        });
    });

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

    // Handle modal opening
    $('#exportModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const row = button.data('row');
        const modal = $(this);

        // Fill in modal fields with row data
        modal.find('.modal-body #seq_no').val(row.seq_no);
        modal.find('.modal-body #taxpayer_id').val(row.taxpayer_id);
        modal.find('.modal-body #registered_name').val(row.registered_name);
        modal.find('.modal-body #name_of_payees').val(row.name_of_payees);
        modal.find('.modal-body #payees_address').val(row.payees_address);
        modal.find('.modal-body #payees_zipcode').val(row.payees_zipcode);
        modal.find('.modal-body #atc_code').val(row.atc_code);
        modal.find('.modal-body #amount_of_income_payment').val(row.amount_of_income_payment);
        modal.find('.modal-body #rate_of_tax').val(row.rate_of_tax);
        modal.find('.modal-body #amount_of_tax_withheld').val(row.amount_of_tax_withheld);
        modal.find('.modal-body #vat_nonvat').val(row.vat_nonvat);
        modal.find('.modal-body #vat_nonvat_description').val(row.vat_nonvat_description);

        // Check ATC code and set radio buttons
        const isWi = row.atc_code.startsWith('WI');
        const isWc = row.atc_code.startsWith('WC');

        if (isWi || isWc) {
            $('#is_corporation_' + (isWc ? 'yes' : 'no')).prop('checked', true);
            $('#is_corporation_yes, #is_corporation_no').prop('disabled', true);
        } else {
            $('#is_corporation_yes, #is_corporation_no').prop('disabled', false);
        }
    });

    // Period selection
    $('#period_from').on('change', function() {
        const selectedQuarter = $(this).val();
        const quarterMonths = {
            "Q1": ["January", "February", "March"],
            "Q2": ["April", "May", "June"],
            "Q3": ["July", "August", "September"],
            "Q4": ["October", "November", "December"]
        };

        // Update month options
        $('#month_from').html('<option value="" disabled>Select a month</option>');
        if (quarterMonths[selectedQuarter]) {
            quarterMonths[selectedQuarter].forEach((month, index) => {
                $('#month_from').append(`<option value="${month}" ${index === 0 ? 'selected' : ''}>${month}</option>`);
            });
            $('#month_from_group').show(); // Show the month dropdown
            selectedMonth = quarterMonths[selectedQuarter][0]; // Set the first month as selected by default
        } else {
            $('#month_from_group').hide(); // Hide the month dropdown
        }

        // Set hidden period fields
        const currentYear = new Date().getFullYear();
        let periodFrom, periodTo;

        switch (selectedQuarter) {
            case 'Q1':
                periodFrom = `0101${currentYear}`;
                periodTo = `0331${currentYear}`;
                break;
            case 'Q2':
                periodFrom = `0401${currentYear}`;
                periodTo = `0630${currentYear}`;
                break;
            case 'Q3':
                periodFrom = `0701${currentYear}`;
                periodTo = `0930${currentYear}`;
                break;
            case 'Q4':
                periodFrom = `1001${currentYear}`;
                periodTo = `1231${currentYear}`;
                break;
        }

        $('#period_from_hidden').val(periodFrom);
        $('#period_to_hidden').val(periodTo);
    });

    // Update selected month
    $('#month_from').on('change', function() {
        selectedMonth = $(this).val();
    });

    // Form submission
    $('#exportForm').on('submit', async function(event) {
        event.preventDefault();
        await generatePDF();
    });
});

// PDF generation function
async function generatePDF() {
    const {
        PDFDocument
    } = PDFLib;

    try {
        const ATC_CODE = $('#atc_code').val();
        const VAT_NONVAT = $('#vat_nonvat').val();

        // Select appropriate PDF template
        const url = ((ATC_CODE === 'WI120' || ATC_CODE === 'WC120') && VAT_NONVAT === 'WV030') ?
            'assets/pdf/LONG.pdf' :
            'assets/pdf/A4.pdf';

        const existingPdfBytes = await fetch(url).then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.arrayBuffer();
        });

        const pdfDoc = await PDFDocument.load(existingPdfBytes);
        const form = pdfDoc.getForm();

        // Collect form data
        const taxpayerId = $('#taxpayer_id').val();
        const registeredName = $('#registered_name').val();
        const nameOfPayees = $('#name_of_payees').val();
        const payeesAddress = $('#payees_address').val();
        const payeesZipcode = $('#payees_zipcode').val();
        const atcCode = $('#atc_code').val();
        const taxpayerIdParts = taxpayerId.split('-');
        const isCorporation = $('input[name="is_corporation"]:checked').val() === 'yes';

        const payorID = $('#payor_tin').val();
        const payorName = $('#payor_name').val();
        const payorAddress = $('#payor_address').val();
        const payorZipcode = $('#payor_zipcode').val();
        const payorIDParts = payorID.split('-');

        const baseAmount = parseFloat($('#base_amount').val());
        const vatNonVat = $('#vat_nonvat').val();
        const vatNonVatPrefix = vatNonVat.substring(0, 2);
        const vatNonVatDesc = $('#vat_nonvat_description').val();

        const periodFrom = $('#period_from_hidden').val();
        const periodTo = $('#period_to_hidden').val();

        // Fill payer fields
        form.getTextField('payer_tin1').setText(taxpayerIdParts[0] || '');
        form.getTextField('payer_tin2').setText(taxpayerIdParts[1] || '');
        form.getTextField('payer_tin3').setText(taxpayerIdParts[2] || '');
        form.getTextField('payer_tin4').setText(taxpayerIdParts[3] || '');

        // Fill payor fields
        form.getTextField('payor_tin1').setText(payorIDParts[0] || '');
        form.getTextField('payor_tin2').setText(payorIDParts[1] || '');
        form.getTextField('payor_tin3').setText(payorIDParts[2] || '');
        form.getTextField('payor_tin4').setText(payorIDParts[3] || '');
        form.getTextField('payor_name').setText(payorName);
        form.getTextField('payor_zipcode').setText(payorZipcode);
        form.getTextField('payor_reg_address').setText(payorAddress);
        form.getTextField('ATC1').setText(atcCode);

        // Fill period fields
        form.getTextField('period_from').setText(periodFrom);
        form.getTextField('period_to').setText(periodTo);

        // Fill payee information
        if (isCorporation) {
            form.getTextField('payees_name').setText(registeredName);
            form.getTextField('conforme_name').setText(registeredName + " / " + taxpayerId);
        } else {
            form.getTextField('payees_name').setText(registeredName);
            form.getTextField('conforme_name').setText(nameOfPayees + " / " + taxpayerId);
        }

        form.getTextField('payer_zipcode').setText(payeesZipcode);
        form.getTextField('payer_reg_address').setText(payeesAddress);

        // Find ATC description
        const selectedAtc = atcOptions.find(atc => atc.atc_code === atcCode);
        if (selectedAtc) {
            form.getTextField('IPS1').setText(selectedAtc.atc_description);
        }

        // Fill VAT fields
        form.getTextField('MPS1').setText(vatNonVatDesc);
        form.getTextField('MPS_ATC1').setText(vatNonVat);

        // Determine which month fields to fill
        let monthField, mpsMonthField, totalMonthField, totalTaxField;

        if (selectedMonth === "January" || selectedMonth === "April" || selectedMonth === "July" || selectedMonth === "October") {
            monthField = 'first_month_1';
            mpsMonthField = 'MPS_first_month_1';
            totalMonthField = 'TOTAL_first_month';
            totalMpsMonthField = 'MPS_first_month';
        } else if (selectedMonth === "February" || selectedMonth === "May" || selectedMonth === "August" || selectedMonth === "November") {
            monthField = 'second_month_1';
            mpsMonthField = 'MPS_second_month_1';
            totalMonthField = 'TOTAL_second_month';
            totalMpsMonthField = 'TOTAL_MPS_second_month';
        } else if (selectedMonth === "March" || selectedMonth === "June" || selectedMonth === "September" || selectedMonth === "December") {
            monthField = 'third_month_1';
            mpsMonthField = 'MPS_third_month_1';
            totalMonthField = 'TOTAL_third_month';
            totalMpsMonthField = 'MPS_third_month';
        }

        // Calculate upper section values
        let netPayUpper = 0;
        let taxUpper = 0;

        // Upper section calculation based on VAT type and ATC code
        if (vatNonVatPrefix === 'WV') {
            netPayUpper = baseAmount / 1.12;

            if (['WC120', 'WI120', 'WC157', 'WI157'].includes(atcCode)) {
                taxUpper = netPayUpper * 0.02;
            } else if (['WC640', 'WI640'].includes(atcCode)) {
                taxUpper = netPayUpper * 0.01;
            } else if (atcCode === 'WI010') {
                taxUpper = netPayUpper * 0.05;
            } else if (atcCode === 'WIO11') {
                taxUpper = netPayUpper * 0.10;
            }

            form.getTextField(monthField).setText(netPayUpper.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

        } else if (vatNonVatPrefix === 'WB') {
            netPayUpper = baseAmount;

            if (['WC120', 'WI120', 'WC157', 'WI157'].includes(atcCode)) {
                taxUpper = netPayUpper * 0.02;
            } else if (['WC640', 'WI640'].includes(atcCode)) {
                taxUpper = netPayUpper * 0.01;
            } else if (atcCode === 'WI010') {
                taxUpper = netPayUpper * 0.05;
            } else if (atcCode === 'WIO11') {
                taxUpper = netPayUpper * 0.10;
            }

            form.getTextField(monthField).setText(netPayUpper.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

        }

        // Set upper section totals

        form.getTextField('total1').setText(netPayUpper.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        form.getTextField('tax1').setText(taxUpper.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        form.getTextField(totalMonthField).setText(netPayUpper.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        form.getTextField('TOTAL_total').setText(netPayUpper.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        form.getTextField('TOTAL_tax').setText(taxUpper.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        // Lower section calculation
        let netPayLower = 0;
        let taxLower = 0;

        if (vatNonVatPrefix === 'WV') {
            netPayLower = baseAmount / 1.12;
            taxLower = netPayLower * 0.05;
        } else if (vatNonVatPrefix === 'WB') {
            netPayLower = baseAmount;
            taxLower = netPayLower * 0.03;
        }

        // Set lower section values
        form.getTextField(monthField).setText(netPayUpper.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        form.getTextField(mpsMonthField).setText(netPayLower.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        form.getTextField('MPS_total1').setText(netPayLower.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        form.getTextField('MPS_tax1').setText(taxLower.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        form.getTextField(totalMpsMonthField).setText(netPayLower.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        form.getTextField('TOTAL_MPS_total').setText(netPayLower.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));
        form.getTextField('MPS_tax').setText(taxLower.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        // Save and download PDF
        const pdfBytes = await pdfDoc.save();
        const blob = new Blob([pdfBytes], {
            type: 'application/pdf'
        });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        const tin = $('#taxpayer_id').val(); // Get taxpayer_id
        link.download = `${taxpayerId} - BIR FORM 2307 EXPORTED.pdf`;
        link.click();

        $('#exportModal').modal('hide');
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Failed to generate PDF. Please check the file path and try again.');
    }
}
    </script>
</body>

</html>