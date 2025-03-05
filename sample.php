<?php
include 'dbcon.php'; // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Excel File</title>
</head>
<body>
    <h2>Upload Excel File</h2>
    <form action="import_alphalist.php" method="post" enctype="multipart/form-data">
        <input type="file" name="excel_file" required>
        <button type="submit" name="submit">Upload</button>
    </form>

    <h2>Imported Data</h2>
    <table border="1">
        <tr>
            <th>seq no</th>
            <th>taxpayer_id</th>
            <th>registered_nam</th>
            <th>name_of_payees</th>
            <th>atc_code</th>
            <th>amount_of_income_payment</th>
            <th>rate_of_tax</th>
            <th>amount_of_tax_withheld</th>
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
                  </tr>";
        }
        ?>
    </table>
</body>
</html>
