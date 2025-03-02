<?php
include("dbcon.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $particulars = $_POST['particulars'];
    $registered_name = $_POST['registered_name'];

    $updateQuery = "UPDATE checks SET registered_name = ? WHERE particulars = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ss", $registered_name, $particulars);

    if (mysqli_stmt_execute($stmt)) {
        echo "Registered Name updated successfully!";
    } else {
        echo "Error updating: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
