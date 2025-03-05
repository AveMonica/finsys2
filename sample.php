<?php
include 'dbcon.php'; // Database connection
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
                    <button type="submit" class="btn btn-primary">Confirm Export</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $('#exportModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var row = button.data('row');
        var modal = $(this);
        modal.find('.modal-body #seq_no').val(row.seq_no);
        modal.find('.modal-body #taxpayer_id').val(row.taxpayer_id);
        modal.find('.modal-body #registered_name').val(row.registered_name);
        modal.find('.modal-body #name_of_payees').val(row.name_of_payees);
        modal.find('.modal-body #atc_code').val(row.atc_code);
        modal.find('.modal-body #amount_of_income_payment').val(row.amount_of_income_payment);
        modal.find('.modal-body #rate_of_tax').val(row.rate_of_tax);
        modal.find('.modal-body #amount_of_tax_withheld').val(row.amount_of_tax_withheld);
    });

    $('#exportForm').on('submit', async function (event) {
        event.preventDefault();

        const { PDFDocument, rgb } = PDFLib;

        try {
            // Load your existing PDF from the server
            const url = 'assets/pdf/BIR FORM 2307.pdf';
            const existingPdfBytes = await fetch(url).then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.arrayBuffer();
            });

            // Load a PDFDocument from the existing PDF bytes
            const pdfDoc = await PDFDocument.load(existingPdfBytes);

            // Get the form so we can fill it
            const form = pdfDoc.getForm();

            // Get form values
            // const seqNo = $('#seq_no').val();
            const taxpayerId = $('#taxpayer_id').val();
            // const registeredName = $('#registered_name').val();
            const nameOfPayees = $('#name_of_payees').val();
            // const atcCode = $('#atc_code').val();
            // const amountOfIncomePayment = $('#amount_of_income_payment').val();
            // const rateOfTax = $('#rate_of_tax').val();
            // const amountOfTaxWithheld = $('#amount_of_tax_withheld').val();

            // Split taxpayer_id into four parts
            const taxpayerIdParts = taxpayerId.split('-');
            const payerTin1 = taxpayerIdParts[0];
            const payerTin2 = taxpayerIdParts[1];
            const payerTin3 = taxpayerIdParts[2];
            const payerTin4 = taxpayerIdParts[3];

            // Debugging: Log the field properties
            const payerTin4Field = form.getTextField('payer_tin4');
            console.log('payer_tin4 field maxLength:', payerTin4Field.maxLength);
            console.log('payer_tin4 value length:', payerTin4.length);

            // Attempt to set the maximum length explicitly
            payerTin4Field.maxLength = 4;

            // Ensure each part fits within the field constraints
            if (payerTin1.length > 3 || payerTin2.length > 3 || payerTin3.length > 3 || payerTin4.length > 4) {
                throw new Error('Taxpayer ID parts exceed field length constraints.');
            }

            // Fill the form fields
            // form.getTextField('seq_no').setText(seqNo);
            form.getTextField('payer_tin1').setText(payerTin1);
            form.getTextField('payer_tin2').setText(payerTin2);
            form.getTextField('payer_tin3').setText(payerTin3);
            form.getTextField('payer_tin4').setText(payerTin4);
            // form.getTextField('registered_name').setText(registeredName);
            form.getTextField('name_of_payees').setText(nameOfPayees);
            // form.getTextField('atc_code').setText(atcCode);
            // form.getTextField('amount_of_income_payment').setText(amountOfIncomePayment);
            // form.getTextField('rate_of_tax').setText(rateOfTax);
            // form.getTextField('amount_of_tax_withheld').setText(amountOfTaxWithheld);

            // Serialize the PDFDocument to bytes (a Uint8Array)
            const pdfBytes = await pdfDoc.save();

            // Trigger the browser to download the PDF document
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