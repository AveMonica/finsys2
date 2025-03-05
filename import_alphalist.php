<?php
require 'vendor/autoload.php'; // PhpSpreadsheet library
include 'dbcon.php'; // Database connection

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['submit'])) {
    $file = $_FILES['excel_file']['tmp_name'];

    if (!empty($file)) {
        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow(); // Get last row number

        for ($row = 15; $row <= $highestRow; $row++) { // Start at row 15
           

            $seq_no = trim($sheet->getCell("A$row")->getValue());
            $taxpayer_id = trim($sheet->getCell("B$row")->getValue());
            $registered_name = trim($sheet->getCell("C$row")->getValue());
            $name_of_payees = trim($sheet->getCell("D$row")->getValue());
            $atc_code = trim($sheet->getCell("E$row")->getValue());
            $amount_of_income_payment = trim($sheet->getCell("F$row")->getValue());
            $rate_of_tax = trim($sheet->getCell("G$row")->getValue());
            $amount_of_tax_withheld = trim($sheet->getCell("H$row")->getValue());

            // Stop if the row is empty
            // if (empty($seq_no) && empty($taxpayer_id) && empty($registered_name) && empty($name_of_payees) && empty($atc_code) && empty($amount_of_income_payment) && empty($rate_of_tax) && empty($amount_of_tax_withheld)) {
            //     break;
            // }
            // if (empty($seq_no) || empty($taxpayer_id) || empty($registered_name) || empty($name_of_payees) || empty($atc_code) || empty($amount_of_income_payment) || empty($rate_of_tax) || empty($amount_of_tax_withheld)) {
            //     continue; // Skip empty rows
            // }
            if (empty($seq_no) && empty($taxpayer_id) && empty($registered_name) && empty($name_of_payees) && empty($atc_code) && empty($amount_of_income_payment) && empty($rate_of_tax) && empty($amount_of_tax_withheld)) {
                continue; // Stop if all cells are empty
            }
            

            // Sanitize inputs to prevent SQL injection
            $seq_no = $conn->real_escape_string($seq_no);
            $taxpayer_id = $conn->real_escape_string($taxpayer_id);
            $registered_name = $conn->real_escape_string($registered_name);
            $name_of_payees = $conn->real_escape_string($name_of_payees);
            $atc_code = $conn->real_escape_string($atc_code);
            $amount_of_income_payment = $conn->real_escape_string($amount_of_income_payment);
            $rate_of_tax = $conn->real_escape_string($rate_of_tax);
            $amount_of_tax_withheld = $conn->real_escape_string($amount_of_tax_withheld);

            // Insert into database
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
