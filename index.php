<?php
$servername = "172.22.144.1";
$username = "store_2026_04_07_user";
$password = "password";
$dbname = "store_2026_04_07";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>Database Data</h1>";

/* ===== CUSTOMERS TABLE ===== */
echo "<h2>Customers</h2>";

$sql_customers = "SELECT * FROM customers";
$result_customers = $conn->query($sql_customers);

if ($result_customers->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Birth Date</th>
                <th>Points</th>
            </tr>";

    while($row = $result_customers->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['first_name']}</td>
                <td>{$row['last_name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['birth_date']}</td>
                <td>{$row['points']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "No customers found.";
}


/* ===== ORDERS TABLE ===== */
echo "<h2>Orders</h2>";

$sql_orders = "SELECT * FROM orders";
$result_orders = $conn->query($sql_orders);

if ($result_orders->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>
            <tr>
                <th>ID</th>
                <th>Customer ID</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Comment</th>
                <th>Delivery Date</th>
            </tr>";

    while($row = $result_orders->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['customer_id']}</td>
                <td>{$row['order_date']}</td>
                <td>{$row['status']}</td>
                <td>{$row['comment']}</td>
                <td>{$row['delivery_date']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "No orders found.";
}

$conn->close();
?>