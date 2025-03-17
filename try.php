<?php
include 'dbcon.php'; // Database connection

// Fetch payor details from the payor table
$payorQuery = "SELECT * FROM payor";
$payorResult = $conn->query($payorQuery);
$payorOptions = [];
while ($payorRow = $payorResult->fetch_assoc()) {
    $payorOptions[] = $payorRow;
}

// Fetch ATC codes from the atc table
// $atcQuery = "SELECT atc_code FROM atc";
// $atcResult = $conn->query($atcQuery);
// $atcOptions = [];
// while ($atcRow = $atcResult->fetch_assoc()) {
//     $atcOptions[] = $atcRow['atc_code'];
// }

$atcQuery = "SELECT atc_code, atc_description FROM atc";
$atcResult = $conn->query($atcQuery);
$atcOptions = [];
while ($atcRow = $atcResult->fetch_assoc()) {
    $atcOptions[] = $atcRow;
}
?>

<script>
    const atcOptions = <?php echo json_encode($atcOptions); ?>;
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel File</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <h2>Upload Excel File</h2>
    <form action="import_alphalist.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit" name="import">Upload</button>
    </form>

    <h2>Imported Data</h2>
    <table id="dataTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>SEQ NO</th>
                <th>TAXPAYER IDENTIFICATION NUMBER</th>
                <th>REGISTERED NAME</th>
                <th>NAME OF PAYEES<br>(Last Name, First Name, Middle Name)</th>
                <th>ADDRESS</th>
                <th>ZIPCODE</th>
                <th>ATC CODE</th>
                <th>AMOUNT OF INCOME PAYMENT</th>
                <th>RATE OF TAX</th>
                <th>AMOUNT OF TAX WITHHELD</th>
                <th>VAT OR NON VAT</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM alphalist_of_payees";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $amount_of_income_payment = number_format($row['amount_of_income_payment'], 2);
            $amount_of_tax_withheld = number_format($row['amount_of_tax_withheld'], 2);
            echo "<tr>
                    <td>{$row['seq_no']}</td>
                    <td>{$row['taxpayer_id']}</td>
                    <td>{$row['registered_name']}</td>
                    <td>{$row['name_of_payees']}</td>
                    <td>{$row['payees_address']}</td>
                    <td>{$row['payees_zipcode']}</td>
                    <td>{$row['atc_code']}</td>
                    <td>{$amount_of_income_payment}</td>
                    <td>{$row['rate_of_tax']}</td>
                    <td>{$amount_of_tax_withheld}</td>
                    <td>{$row['vat_nonvat']}</td>
                    <td><button class='btn btn-primary' data-toggle='modal' data-target='#exportModal' data-row='".json_encode($row)."'>Export</button></td>
                </tr>";
        }
        ?>
        </tbody>
    </table>

<!-- Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="exportForm" action="update_alphalist.php" method="post">
                    <div class="form-group">
                        <label for="period_from">Period From</label>
                        <select class="form-control" id="period_from" name="period_from" required>
                            <option value="Q1">First Quarter</option>
                            <option value="Q2">Second Quarter</option>
                            <option value="Q3">Third Quarter</option>
                            <option value="Q4">Fourth Quarter</option>
                        </select>
                    </div>
                    <div class="form-group">
                      <h1>PAYER</h1>
                    </div>
                    <div class="form-group">
                        <label for="seq_no">SEQ NO</label>
                        <input type="text" class="form-control" id="seq_no" name="seq_no" readonly>
                    </div>
                    <div class="form-group">
                        <label for="taxpayer_id">TAXPAYER IDENTIFICATION NUMBER</label>
                        <input type="text" class="form-control" id="taxpayer_id" name="taxpayer_id">
                    </div>
                    <div class="form-group">
                        <label for="registered_name">REGISTERED NAME</label>
                        <input type="text" class="form-control" id="registered_name" name="registered_name">
                    </div>
                    <div class="form-group">
                        <label for="name_of_payees">NAME OF PAYEES</label>
                        <input type="text" class="form-control" id="name_of_payees" name="name_of_payees">
                    </div>
                    <div class="form-group">
                        <label for="payees_address">ADDRESS</label>
                        <input type="text" class="form-control" id="payees_address" name="payees_address">
                    </div>
                    <div class="form-group">
                        <label for="payees_zipcode">ZIPCODE</label>
                        <input type="text" class="form-control" id="payees_zipcode" name="payees_zipcode">
                    </div>
                    <!-- <div class="form-group">
                        <label for="atc_code">ATC CODE</label>
                        <input type="text" class="form-control" id="atc_code" name="atc_code">
                    </div> -->
                    <div class="form-group">
                        <label for="amount_of_income_payment">AMOUNT OF INCOME PAYMENT</label>
                        <input type="text" class="form-control" id="amount_of_income_payment" name="amount_of_income_payment">
                    </div>
                    <div class="form-group">
                        <label for="rate_of_tax">RATE OF TAX</label>
                        <input type="text" class="form-control" id="rate_of_tax" name="rate_of_tax">
                    </div>
                    <div class="form-group">
                        <label for="amount_of_tax_withheld">AMOUNT OF TAX WITHHELD</label>
                        <input type="text" class="form-control" id="amount_of_tax_withheld" name="amount_of_tax_withheld">
                    </div>
    
                    <div class="form-group">
                        <label>Is the Supplier/Payee/Creditor a Corporation?</label><br>
                        <input type="radio" id="is_corporation_yes" name="is_corporation" value="yes"> Yes
                        <input type="radio" id="is_corporation_no" name="is_corporation" value="no" checked> No
                    </div>
                    <div class="form-group">
                    <h1>PAYOR</h1>
                    </div>
                    <div class="form-group">
                        <label for="payor_tin">Payor TIN</label>
                        <select class="form-control" id="payor_tin" name="payor_tin">
                            <?php foreach ($payorOptions as $payor): ?>
                                <option value="<?php echo $payor['payor_tin']; ?>"><?php echo $payor['payor_tin']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payor_name">Payor Name</label>
                        <input type="text" class="form-control" id="payor_name" name="payor_name" readonly>
                    </div>
                    <div class="form-group">
                        <label for="payor_address">Payor Address</label>
                        <input type="text" class="form-control" id="payor_address" name="payor_address" readonly>
                    </div>
                    <div class="form-group">
                        <label for="payor_zipcode">Payor Address Zipcode</label>
                        <input type="text" class="form-control" id="payor_zipcode" name="payor_zipcode" readonly>
                    </div>
                    <div class="form-group">
                        <label for="payor_zipcode">VAT OR NON VAT</label>
                        <input type="text" class="form-control" id="vat_nonvat" name="vat_nonvat" readonly>
                    </div>
                    <div class="form-group">
                    <h1>*COMPUTATION*</h1>
                    </div>
                    <div class="form-group">
                        <label for="payor_address">Gross Amount or Base Amount</label>
                        <input type="number" class="form-control" id="base_amount" name="base_amount">
                    </div>
                    <!-- atc -->
                    <!-- atc -->
<div class="form-group">
    <label for="atc_code">ATC Code</label>
    <select class="form-control" id="atc_code" name="atc_code">
        <?php foreach ($atcOptions as $atc): ?>
            <option value="<?php echo $atc['atc_code']; ?>"><?php echo $atc['atc_code']; ?></option>
        <?php endforeach; ?>
    </select>
</div>
                    <input type="hidden" id="period_from_hidden" name="period_from_hidden">
                    <input type="hidden" id="period_to_hidden" name="period_to_hidden">
                    <button type="submit" class="btn btn-primary">Confirm Export</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    const payorOptions = <?php echo json_encode($payorOptions); ?>;

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

    $('#exportModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var row = button.data('row');
        var modal = $(this);
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
        // Check ATC code and set radio buttons accordingly
        if (row.atc_code.startsWith('WI')) {
            $('#is_corporation_no').prop('checked', true);
            $('#is_corporation_yes').prop('disabled', true);
            $('#is_corporation_no').prop('disabled', true);
        } else if (row.atc_code.startsWith('WC')) {
            $('#is_corporation_yes').prop('checked', true);
            $('#is_corporation_yes').prop('disabled', true);
            $('#is_corporation_no').prop('disabled', true);
        } else {
            $('#is_corporation_yes').prop('disabled', false);
            $('#is_corporation_no').prop('disabled', false);
        }
    });

    $('#period_from').on('change', function() {
        const selectedQuarter = $(this).val();
        let periodFrom, periodTo;
        const currentYear = new Date().getFullYear();

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

        // Set the period fields in the hidden inputs
        $('#period_from_hidden').val(periodFrom);
        $('#period_to_hidden').val(periodTo);

        console.log("Period From: " + periodFrom + " | Period To: " + periodTo);
    });

$('#exportForm').on('submit', async function (event) {
    event.preventDefault();

    const { PDFDocument, rgb } = PDFLib;

    try {
        const url = 'assets/pdf/BIR FORM 2307.pdf';
        const existingPdfBytes = await fetch(url).then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.arrayBuffer();
        });

        const pdfDoc = await PDFDocument.load(existingPdfBytes);
        const form = pdfDoc.getForm();

        const taxpayerId = $('#taxpayer_id').val();
        const registeredName = $('#registered_name').val();
        const nameOfPayees = $('#name_of_payees').val();
        const payeesAddress = $('#payees_address').val();
        const payeesZipcode = $('#payees_zipcode').val();
        const atc_code = $('#atc_code').val();
        const taxpayerIdParts = taxpayerId.split('-');
        const payerTin1 = taxpayerIdParts[0];
        const payerTin2 = taxpayerIdParts[1];
        const payerTin3 = taxpayerIdParts[2];
        const payerTin4 = taxpayerIdParts[3];
        const isCorporation = $('input[name="is_corporation"]:checked').val() === 'yes';

        form.getTextField('payer_tin1').setText(payerTin1);
        form.getTextField('payer_tin2').setText(payerTin2);
        form.getTextField('payer_tin3').setText(payerTin3);
        form.getTextField('payer_tin4').setText(payerTin4);

        const payorID = $('#payor_tin').val();
        const payorName = $('#payor_name').val();
        const payorAddress = $('#payor_address').val();
        const payorZipcode = $('#payor_zipcode').val();
        const payorIDParts = payorID.split('-');
        const payorTin1 = payorIDParts[0];
        const payorTin2 = payorIDParts[1];
        const payorTin3 = payorIDParts[2];
        const payorTin4 = payorIDParts[3];

        form.getTextField('payor_tin1').setText(payorTin1);
        form.getTextField('payor_tin2').setText(payorTin2);
        form.getTextField('payor_tin3').setText(payorTin3);
        form.getTextField('payor_tin4').setText(payorTin4);
        form.getTextField('payor_name').setText(payorName);
        form.getTextField('payor_zipcode').setText(payorZipcode);
        form.getTextField('payor_reg_address').setText(payorAddress);
        form.getTextField('ATC1').setText(atc_code);

        const periodFrom = $('#period_from_hidden').val();
        const periodTo = $('#period_to_hidden').val();
        form.getTextField('period_from').setText(periodFrom);
        form.getTextField('period_to').setText(periodTo);

        if (isCorporation) {
            form.getTextField('payees_name').setText(registeredName);
            form.getTextField('conforme_name').setText(registeredName + " / " + taxpayerId);
            form.getTextField('payer_zipcode').setText(payeesZipcode);
            form.getTextField('payer_reg_address').setText(payeesAddress);
        } else {
            form.getTextField('payees_name').setText(registeredName);
            form.getTextField('conforme_name').setText(nameOfPayees + " / " + taxpayerId);
            form.getTextField('payer_zipcode').setText(payeesZipcode);
            form.getTextField('payer_reg_address').setText(payeesAddress);
        }

        // Calculate the total based on the ATC code and base amount
        const atcCode = $('#atc_code').val();
        const baseAmount = parseFloat($('#base_amount').val());
        const vatNonvat = $('#vat_nonvat').val().substring(0, 2);
        console.log(baseAmount);
        console.log(atcCode);
        console.log(vatNonvat);

        // Find and log the ATC description
        const selectedAtc = atcOptions.find(atc => atc.atc_code === atcCode);
        if (selectedAtc) {
            form.getTextField('IPS1').setText(selectedAtc.atc_description);
            console.log('ATC Description:', selectedAtc.atc_description);
        } else {
            console.log('ATC Description not found');
        }
        

        let total = 0;
        let net_of_pay = 0;

        if (vatNonvat === 'WV') {
            if (['WC120', 'WI120', 'WC157', 'WI157'].includes(atcCode)) {
                net_of_pay = (baseAmount / 1.12);
                console.log(net_of_pay);
                total = net_of_pay * 0.02;
                console.log(total);

                form.getTextField('total1').setText(net_of_pay.toFixed(2));
                form.getTextField('tax1').setText(total.toFixed(2));
            } else if (['WC640', 'WI640'].includes(atcCode)) {
                net_of_pay = (baseAmount / 1.12);
                total = net_of_pay * 0.01;

                form.getTextField('total1').setText(net_of_pay.toFixed(2));
                form.getTextField('tax1').setText(total.toFixed(2));
            } else if (atcCode === 'WI010') {
                net_of_pay = (baseAmount / 1.12);
                total = net_of_pay * 0.05;

                form.getTextField('total1').setText(net_of_pay.toFixed(2));
                form.getTextField('tax1').setText(total.toFixed(2));
            } else if (atcCode === 'WIO11') {
                net_of_pay = (baseAmount / 1.12);
                total = net_of_pay * .10;

                form.getTextField('total1').setText(net_of_pay.toFixed(2));
                form.getTextField('tax1').setText(total.toFixed(2));
            }
        } else if (vatNonvat === 'WB') {
            if (['WC120', 'WI120', 'WC157', 'WI157'].includes(atcCode)) {
                total = baseAmount * 0.02;
            } else if (['WC640', 'WI640'].includes(atcCode)) {
                total = baseAmount * 0.01;
            } else if (atcCode === 'WI010') {
                total = baseAmount * 0.05;
            } else if (atcCode === 'WIO11') {
                total = baseAmount * 0.10;
            }
        }

        if (vatNonvat === 'WV') {
            net_of_pay = (baseAmount / 1.12);
            total = net_of_pay * .05;

            form.getTextField('MPS_total1').setText(net_of_pay.toFixed(2));
            form.getTextField('MPS_tax1').setText(total.toFixed(2));
        } else if (vatNonvat === 'WB') {
            total = baseAmount * 0.03;
            form.getTextField('MPS_total1').setText(baseAmount.toFixed(2));
            form.getTextField('MPS_tax1').setText(total.toFixed(2));
        }

        const pdfBytes = await pdfDoc.save();
        const blob = new Blob([pdfBytes], { type: 'application/pdf' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'exported_data.pdf';
        link.click();

        $('#exportModal').modal('hide');

    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Failed to generate PDF. Please check the file path and try again.');
    }
});
</script>
</body>
</html>