<?php
include 'dbcon.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seq_no = $_POST['seq_no'];
    $taxpayer_id = $_POST['taxpayer_id'];
    $registered_name = $_POST['registered_name'];
    $name_of_payees = $_POST['name_of_payees'];
    $atc_code = $_POST['atc_code'];
    $amount_of_income_payment = $_POST['amount_of_income_payment'];
    $rate_of_tax = $_POST['rate_of_tax'];
    $amount_of_tax_withheld = $_POST['amount_of_tax_withheld'];

    $sql = "UPDATE alphalist_of_payees SET 
            taxpayer_id = ?, 
            registered_name = ?, 
            name_of_payees = ?, 
            atc_code = ?, 
            amount_of_income_payment = ?, 
            rate_of_tax = ?, 
            amount_of_tax_withheld = ? 
            WHERE seq_no = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssddds", $taxpayer_id, $registered_name, $name_of_payees, $atc_code, $amount_of_income_payment, $rate_of_tax, $amount_of_tax_withheld, $seq_no);

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to the main page
    header("Location: sample.php");
    exit();
}
?>