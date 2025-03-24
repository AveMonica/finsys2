<?php
include 'dbcon.php';

$seq_no = $_POST['seq_no'];
$taxpayer_id = $_POST['taxpayer_id'];
$registered_name = $_POST['registered_name'];
$name_of_payees = $_POST['name_of_payees'];
$amount_of_income_payment = $_POST['amount_of_income_payment'];
$rate_of_tax = $_POST['rate_of_tax'];
$amount_of_tax_withheld = $_POST['amount_of_tax_withheld'];
$payees_address = $_POST['payees_address'];
$payees_zipcode = $_POST['payees_zipcode'];
$atc_code = $_POST['atc_code'];
$vat_nonvat = $_POST['vat_nonvat'];

$sql = "INSERT INTO alphalist_of_payees (seq_no, taxpayer_id, registered_name, name_of_payees, amount_of_income_payment, rate_of_tax, amount_of_tax_withheld, payees_address, payees_zipcode, atc_code, vat_nonvat)
VALUES ('$seq_no', '$taxpayer_id', '$registered_name', '$name_of_payees', '$amount_of_income_payment', '$rate_of_tax', '$amount_of_tax_withheld', '$payees_address', '$payees_zipcode', '$atc_code', '$vat_nonvat')";

if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>