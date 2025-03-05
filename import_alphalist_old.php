<?php
require 'vendor/autoload.php'; // PhpSpreadsheet library
include 'dbcon.php'; // Database connection

use PhpOffice\PhpSpreadsheet\IOFactory;



if (isset($_POST['submit'])) {
    $file = $_FILES['excel_file']['tmp_name'];

    if (!empty($file)) {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        // Skip the first row if it contains headers
        array_shift($data);

        foreach ($data as $row) {
            $seq_no = $conn->real_escape_string($row[0]);
            $taxpayer_id = $conn->real_escape_string($row[1]);
            $registered_name = $conn->real_escape_string($row[2]);
            $name_of_payees = $conn->real_escape_string($row[3]);
            $atc_code = $conn->real_escape_string($row[4]);
            $amount_of_income_payment = $conn->real_escape_string($row[5]);
            $rate_of_tax = $conn->real_escape_string($row[6]);
            $amount_of_tax_withheld = $conn->real_escape_string($row[7]);

            $sql = "INSERT INTO alphalist_of_payees (seq_no, taxpayer_id, registered_name, name_of_payees, atc_code, amount_of_income_payment, rate_of_tax, amount_of_tax_withheld) 
                VALUES ('$seq_no', '$taxpayer_id', '$registered_name', '$name_of_payees', '$atc_code', '$amount_of_income_payment', '$rate_of_tax', '$amount_of_tax_withheld')";

            $conn->query($sql);
        }
        echo "Data imported successfully!";
    } else {
        echo "No file selected!";
    }
}
?>
