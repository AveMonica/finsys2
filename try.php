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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
</head>
<body class="container mt-4">
    <h2>Upload Excel File</h2>
    <form action="import_alphalist.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit" name="import" class="btn btn-success">Upload</button>
    </form>

    <h2 class="mt-4">Imported Data</h2>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>SEQ NO</th>
                <th>TAXPAYER ID</th>
                <th>REGISTERED NAME</th>
                <th>NAME OF PAYEES</th>
                <th>ATC CODE</th>
                <th>INCOME PAYMENT</th>
                <th>RATE OF TAX</th>
                <th>TAX WITHHELD</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM alphalist_of_payees";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['seq_no']}</td>
                        <td>{$row['taxpayer_id']}</td>
                        <td>{$row['registered_name']}</td>
                        <td>{$row['name_of_payees']}</td>
                        <td>{$row['atc_code']}</td>
                        <td>" . number_format($row['amount_of_income_payment'], 2) . "</td>
                        <td>{$row['rate_of_tax']}</td>
                        <td>" . number_format($row['amount_of_tax_withheld'], 2) . "</td>
                        <td>
                            <button class='btn btn-primary' data-toggle='modal' data-target='#exportModal' data-row='".json_encode($row)."'>Export</button>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Data</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="exportForm">
                        <div class="form-group">
                            <label>SEQ NO</label>
                            <input type="text" class="form-control" id="seq_no" readonly>
                        </div>
                        <div class="form-group">
                            <label>TAXPAYER ID</label>
                            <input type="text" class="form-control" id="taxpayer_id">
                        </div>
                        <div class="form-group">
                            <label>REGISTERED NAME</label>
                            <input type="text" class="form-control" id="registered_name">
                        </div>
                        <div class="form-group">
                            <label>NAME OF PAYEES</label>
                            <input type="text" class="form-control" id="name_of_payees">
                        </div>
                        <div class="form-group">
                            <label>ATC CODE</label>
                            <input type="text" class="form-control" id="atc_code">
                        </div>
                        <div class="form-group">
                            <label>INCOME PAYMENT</label>
                            <input type="text" class="form-control" id="amount_of_income_payment">
                        </div>
                        <div class="form-group">
                            <label>RATE OF TAX</label>
                            <input type="text" class="form-control" id="rate_of_tax">
                        </div>
                        <div class="form-group">
                            <label>TAX WITHHELD</label>
                            <input type="text" class="form-control" id="amount_of_tax_withheld">
                        </div>
                        <button type="submit" class="btn btn-primary">Generate PDF</button>
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
            modal.find('#seq_no').val(row.seq_no);
            modal.find('#taxpayer_id').val(row.taxpayer_id);
            modal.find('#registered_name').val(row.registered_name);
            modal.find('#name_of_payees').val(row.name_of_payees);
            modal.find('#atc_code').val(row.atc_code);
            modal.find('#amount_of_income_payment').val(row.amount_of_income_payment);
            modal.find('#rate_of_tax').val(row.rate_of_tax);
            modal.find('#amount_of_tax_withheld').val(row.amount_of_tax_withheld);
        });

        $('#exportForm').on('submit', async function (event) {
            event.preventDefault();
            const { PDFDocument } = PDFLib;
            try {
                const url = 'assets/pdf/BIR_FORM_2307.pdf';
                const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer());
                const pdfDoc = await PDFDocument.load(existingPdfBytes);
                const form = pdfDoc.getForm();

                const taxpayerIdParts = $('#taxpayer_id').val().split('-');
                form.getTextField('payer_tin1').setText(taxpayerIdParts[0] || '');
                form.getTextField('payer_tin2').setText(taxpayerIdParts[1] || '');
                form.getTextField('payer_tin3').setText(taxpayerIdParts[2] || '');
                form.getTextField('payer_tin4').setText(taxpayerIdParts[3] || '');

                form.getTextField('payees_name').setText($('#registered_name').val());
                form.getTextField('conforme_name').setText($('#name_of_payees').val());

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


//     $('#exportForm').on('submit', async function (event) {
//     event.preventDefault();

//     const { PDFDocument, rgb } = PDFLib;

//     try {
//         // Load your existing PDF from the server
//         const url = 'assets/pdf/BIR FORM 2307.pdf';
//         const existingPdfBytes = await fetch(url).then(res => {
//             if (!res.ok) {
//                 throw new Error('Network response was not ok');
//             }
//             return res.arrayBuffer();
//         });

//         // Load a PDFDocument from the existing PDF bytes
//         const pdfDoc = await PDFDocument.load(existingPdfBytes);

//         // Load and embed the Cambria Bold font
//         const fontBytes = await fetch('assets/fonts/Cambria.ttf').then(res => res.arrayBuffer());
//         const cambriaBoldFont = await pdfDoc.embedFont(fontBytes);

//         // Get the form so we can fill it
//         const form = pdfDoc.getForm();

//         // Get form values
//         const taxpayerId = $('#taxpayer_id').val();
//         const registeredName = $('#registered_name').val();
//         const nameOfPayees = $('#name_of_payees').val();
//         const conformeName = $('#name_of_payees').val();

//         // Split taxpayer_id into four parts
//         const taxpayerIdParts = taxpayerId.split('-');
//         const payerTin1 = taxpayerIdParts[0];
//         const payerTin2 = taxpayerIdParts[1];
//         const payerTin3 = taxpayerIdParts[2];
//         const payerTin4 = taxpayerIdParts[3];

//         // Determine if it is a corporation
//         const isCorporation = $('input[name="is_corporation"]:checked').val() === 'yes';

//         // Get text fields and set their font
//         const payerTin1Field = form.getTextField('payer_tin1');
//         const payerTin2Field = form.getTextField('payer_tin2');
//         const payerTin3Field = form.getTextField('payer_tin3');
//         const payerTin4Field = form.getTextField('payer_tin4');
//         const payeesNameField = form.getTextField('payees_name');
//         const conformeNameField = form.getTextField('conforme_name');

//         // Set font for each text field
//         payerTin1Field.updateAppearances(cambriaBoldFont);
//         payerTin2Field.updateAppearances(cambriaBoldFont);
//         payerTin3Field.updateAppearances(cambriaBoldFont);
//         payerTin4Field.updateAppearances(cambriaBoldFont);
//         payeesNameField.updateAppearances(cambriaBoldFont);
//         conformeNameField.updateAppearances(cambriaBoldFont);



//         // Fill the form fields
//         payerTin1Field.setText(payerTin1);
//         payerTin2Field.setText(payerTin2);
//         payerTin3Field.setText(payerTin3);
//         payerTin4Field.setText(payerTin4);

//         if (isCorporation) {
//             payeesNameField.setText(registeredName);
//             conformeNameField.setText(registeredName);
//         } else {
//             payeesNameField.setText(nameOfPayees);
//             conformeNameField.setText(nameOfPayees);
//         }

//         // Serialize the PDFDocument to bytes (a Uint8Array)
//         const pdfBytes = await pdfDoc.save();

//         // Trigger the browser to download the PDF document
//         const blob = new Blob([pdfBytes], { type: 'application/pdf' });
//         const link = document.createElement('a');
//         link.href = URL.createObjectURL(blob);
//         link.download = 'exported_data.pdf';
//         link.click();

//         $('#exportModal').modal('hide');
//     } catch (error) {
//         console.error('Error generating PDF:', error);
//         alert('Failed to generate PDF. Please check the file path and try again.');
//     }
// });

</script>
</body>
</html>