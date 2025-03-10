<?php
include 'dbcon.php'; // Database connection

// Fetch payor details from the payor table
$payorQuery = "SELECT * FROM payor";
$payorResult = $conn->query($payorQuery);
$payorOptions = [];
while ($payorRow = $payorResult->fetch_assoc()) {
    $payorOptions[] = $payorRow;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel File</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
</head>
<body>
    <h2>Upload Excel File</h2>
    <form action="import_alphalist.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit" name="import">Upload</button>
    </form>

    <h2>Imported Data</h2>
    <table border="1" style="border-collapse: collapse;">
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
            <th>ACTIONS</th>
        </tr>
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
                    <td><button class='btn btn-primary' data-toggle='modal' data-target='#exportModal' data-row='".json_encode($row)."'>Export</button></td>
                </tr>";
        }
        ?>
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
                    <div class="form-group">
                        <label for="period_from">Period From</label>
                        <input type="date" class="form-control" id="period_from" name="period_from" required>
                    </div>
                    <div class="form-group">
                        <label for="period_to">Period To</label>
                        <input type="date" class="form-control" id="period_to" name="period_to" required>
                    </div>
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
                        <label for="name_of_payees">ADDRESS</label>
                        <input type="text" class="form-control" id="payees_address" name="payees_address">
                    </div>
                    <div class="form-group">
                        <label for="name_of_payees">ZIPCODE</label>
                        <input type="text" class="form-control" id="payees_zipcode" name="payees_zipcode">
                    </div>
                    <div class="form-group">
                        <label for="atc_code">ATC CODE</label>
                        <input type="text" class="form-control" id="atc_code" name="atc_code">
                    </div>
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
                    <button type="submit" class="btn btn-primary">Confirm Export</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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

            const formatDate = (date) => {
                const [year, month, day] = date.split('-');
                return `${month}${day}${year}`;
            };

            const periodFrom = formatDate($('#period_from').val());
            const periodTo = formatDate($('#period_to').val());
            form.getTextField('period_from').setText(periodFrom);
            form.getTextField('period_to').setText(periodTo);

            if (isCorporation) {
                form.getTextField('payees_name').setText(registeredName);
                form.getTextField('conforme_name').setText(registeredName);
                form.getTextField('payer_zipcode').setText(payeesZipcode);
                form.getTextField('payer_reg_address').setText(payeesAddress);
            } else {
                form.getTextField('payees_name').setText(nameOfPayees);
                form.getTextField('conforme_name').setText(nameOfPayees + " / " + taxpayerId);
                form.getTextField('payer_zipcode').setText(payeesZipcode);
                form.getTextField('payer_reg_address').setText(payeesAddress);
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