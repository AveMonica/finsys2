<?php
session_start();
include 'dbcon.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// Check if fund_id is provided
if (!isset($_GET['fund_id']) || !is_numeric($_GET['fund_id'])) {
    $_SESSION['message'] = "<p style='color:red;'>Error: No fund selected.</p>";
    header("Location: dashboard.php");
    exit();
}

$fund_id = intval($_GET['fund_id']);

if (isset($_POST['import'])) {
    $file = $_FILES['file']['tmp_name'];

    if ($file) {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);

        // Check if "Data" worksheet exists
        $sheetNames = $spreadsheet->getSheetNames();
        if (!in_array("Data", $sheetNames)) {
            $_SESSION['message'] = "<p style='color:red;'>Error: Worksheet 'Data' not found in the uploaded file.</p>";
            header("Location: checks_table.php?fund_id=$fund_id");
            exit();
        }

        $worksheet = $spreadsheet->getSheetByName("Data");
        $rows = $worksheet->toArray(null, true, true, true);

        if (count($rows) <= 9) {
            $_SESSION['message'] = "<p style='color:red;'>Error: The 'Data' worksheet does not contain enough records.</p>";
            header("Location: checks_table.php?fund_id=$fund_id");
            exit();
        }

        $rows = array_slice($rows, 8); // Skip first 8 rows

        // Disable foreign key checks temporarily
        $conn->query("SET FOREIGN_KEY_CHECKS=0;");

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO checks (
            fund_id, check_date, check_serial_number, dv_payroll_number, ors_burs_no, 
            responsibility_center_code, sub_code, gross_amount_bir, series, 
            payee, particulars, account_name, ppsas_code, ngas_code, debit, 
            credit, asa_obligation_number, running_balance, date_created
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        foreach ($rows as $row) {
            if (empty($row['B']) || empty($row['C']) || empty($row['J'])) {
                continue; // Skip empty rows
            }

            // Convert date formats
            $check_date = $row['B'];
            if (is_numeric($check_date)) {
                $check_date = Date::excelToDateTimeObject($check_date)->format('Y-m-d');
            } else {
                $check_date = date('Y-m-d', strtotime($check_date));
            }

            // Sanitize numeric values
            $debit = is_numeric(str_replace(',', '', $row['O'])) ? floatval(str_replace(',', '', $row['O'])) : 0.00;
            $credit = is_numeric(str_replace(',', '', $row['P'])) ? floatval(str_replace(',', '', $row['P'])) : 0.00;
            $running_balance = is_numeric(str_replace(',', '', $row['T'])) ? floatval(str_replace(',', '', $row['T'])) : 0.00;

            $stmt->execute([
                $fund_id,
                $check_date,
                trim($row['C']),
                trim($row['D']),
                trim($row['E']),
                trim($row['F']),
                trim($row['G']),
                trim($row['H']),
                trim($row['I']),
                trim($row['J']),
                trim($row['K']),
                trim($row['L']),
                trim($row['M']),
                trim($row['N']),
                $debit,
                $credit,
                trim($row['Q']),
                $running_balance
            ]);
        }

        // Enable foreign key checks again
        $conn->query("SET FOREIGN_KEY_CHECKS=1;");

        $_SESSION['message'] = "<p style='color:green;'>File imported successfully!</p>";
        header("Location: checks_table.php?fund_id=$fund_id");
        exit();
    }
}

$_SESSION['message'] = "<p style='color:red;'>Error uploading file.</p>";
header("Location: checks_table.php?fund_id=$fund_id");
exit();
