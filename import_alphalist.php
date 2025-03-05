<?php
session_start();
include 'dbcon.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

if (isset($_POST['import'])) {
    $file = $_FILES['file']['tmp_name'];

    if ($file) {
        error_log("File uploaded successfully: " . $file);

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        $worksheet = $spreadsheet->getSheet(0);
        $rows = $worksheet->toArray(null, true, true, true);

        error_log("Number of rows read: " . count($rows));

        $rows = array_slice($rows, 14); 

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO alphalist_of_payees (seq_no, taxpayer_id, registered_name, name_of_payees, atc_code, amount_of_income_payment, rate_of_tax, amount_of_tax_withheld) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($rows as $row) {
            if (empty($row['A']) || empty($row['B']) || empty($row['C'])) {
                error_log("Skipping empty row");
                continue; // Skip empty rows
            }

            $stmt->execute([
                trim($row['A']),
                trim($row['B']),
                trim($row['C']),
                trim($row['D']),
                trim($row['E']),
                trim($row['F']),
                trim($row['G']),
                trim($row['H'])
            ]);
        }

        $_SESSION['message'] = "<p style='color:green;'>File imported successfully!</p>";
        header("Location: sample.php");
        exit();
    } else {
        error_log("File upload failed.");
    }
}

$_SESSION['message'] = "<p style='color:red;'>Error uploading file.</p>";
header("Location: sample.php");
exit();
?>