<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

include("dbcon.php");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fund = trim(mysqli_real_escape_string($conn, $_POST["fund"]));
    $sub_fund = trim(mysqli_real_escape_string($conn, $_POST["sub_fund"]));

    // Construct fund_name format
    if (empty($sub_fund)) {
        $fund_name = $fund; // No square brackets if there's no sub-fund
    } else {
        $fund_name = "[$fund] $sub_fund"; // Use square brackets if sub-fund exists
    }

    // Insert into the funds table
    $query = "INSERT INTO funds (fund_name) VALUES ('$fund_name')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Fund added successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error adding fund.');</script>";
    }
}

// Fetch all funds
$result = mysqli_query($conn, "SELECT * FROM funds ORDER BY fund_id DESC");

?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <style>
        .fund-button {
            display: block;
            width: 200px;
            padding: 10px;
            margin: 5px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }

        .fund-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h2>Funds List</h2>

    <!-- Display existing funds as buttons -->
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <a href="checks_table.php?fund_id=<?php echo $row['fund_id']; ?>" class="fund-button">
            <?php echo $row['fund_name']; ?>
        </a>
    <?php endwhile; ?>

    <h2>Create New Fund</h2>
    <form method="POST">
        <label for="fund">Fund Name:</label>
        <input type="text" id="fund" name="fund" required>

        <label for="sub_fund">Sub-Fund Name (optional):</label>
        <input type="text" id="sub_fund" name="sub_fund">

        <button type="submit">Create Fund</button>
    </form>

    <br>
    <a href="logout.php">Logout</a>
</body>

</html>