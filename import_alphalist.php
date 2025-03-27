<?php
session_start();
set_time_limit(300);
include 'dbcon.php';
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

if (isset($_POST['import'])) {
    $file = $_FILES['file']['tmp_name'];

    if ($file) {
        error_log("File uploaded successfully: " . $file);

        // Load the spreadsheet
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getSheet(0);
        $rows = $worksheet->toArray(null, true, true, true);

        error_log("Number of rows read: " . count($rows));

        // Slice the array to remove header rows (starting from the 15th row)
        $rows = array_slice($rows, 14);

        // Prepare SQL statement for inserting data into alphalist_of_payees
        $stmt = $conn->prepare("INSERT INTO alphalist_of_payees (seq_no, taxpayer_id, registered_name, name_of_payees, atc_code, amount_of_income_payment, rate_of_tax, amount_of_tax_withheld, payees_address, payees_zipcode, vat_nonvat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if atc_code exists in the atc table
        $atc_stmt = $conn->prepare("SELECT COUNT(*) FROM atc WHERE atc_code = ?");

        // Check if vat_nonvat exists in the vat_nonvat_table
        $vat_stmt = $conn->prepare("SELECT COUNT(*) FROM vat_nonvat_table WHERE vat_nonvat = ?");

        foreach ($rows as $row) {
            if (empty($row['A']) || empty($row['B']) || empty($row['C'])) {
                error_log("Skipping empty row");
                continue; // Skip empty rows
            }

            // Check if atc_code exists in the atc table
            $atc_code = trim($row['E']);
            $atc_stmt->bind_param("s", $atc_code); // Bind atc_code as string
            $atc_stmt->execute();
            $result = $atc_stmt->get_result();
            $atc_exists = $result->fetch_row()[0] > 0; // Use fetch_row() to get the result

            if (!$atc_exists) {
                // If the atc_code doesn't exist in the atc table, you can choose to skip or log an error
                error_log("ATC code {$atc_code} not found in atc table, skipping row.");
                continue; // Skip row if ATC code doesn't exist
            }

            // Check if vat_nonvat exists in the vat_nonvat_table
            $vat_nonvat = trim($row['J']);
            $vat_stmt->bind_param("s", $vat_nonvat); // Bind vat_nonvat as string
            $vat_stmt->execute();
            $result = $vat_stmt->get_result();
            $vat_exists = $result->fetch_row()[0] > 0; // Use fetch_row() to get the result

            if (!$vat_exists) {
                // If the vat_nonvat doesn't exist in the vat_nonvat_table, you can choose to skip or log an error
                error_log("VAT/Non-VAT code {$vat_nonvat} not found in vat_nonvat_table, skipping row.");
                continue; // Skip row if VAT/Non-VAT code doesn't exist
            }

            $address = trim($row['I']);
            $zipcode = substr($address, -4);
            $address = substr($address, 0, -4);

            // Execute the prepared statement to insert the row into the database
            $stmt->execute([
                trim($row['A']),
                trim($row['B']),
                trim($row['C']),
                trim($row['D']),
                $atc_code, // Ensure this is the valid atc_code
                trim($row['F']),
                trim($row['G']),
                trim($row['H']),
                $address,
                $zipcode,
                $vat_nonvat, // Ensure this is the valid vat_nonvat
            ]);
        }

        $_SESSION['message'] = "<p style='color:green;'>File imported successfully!</p>";
        header("Location: export_bir_pdf.php");
        exit();
    } else {
        error_log("File upload failed.");
    }
}

$_SESSION['message'] = "<p style='color:red;'>Error uploading file.</p>";
header("Location: export_bir_pdf.php");
exit();
?>