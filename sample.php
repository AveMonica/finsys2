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

    ...
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

...
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

    $('#exportForm').on('submit', function (event) {
        // Remove event.preventDefault() to allow form submission
        // event.preventDefault();
        // Add your export logic here
        alert('Data exported successfully!');
        $('#exportModal').modal('hide');
    });
</script>
...
</body>
</html>