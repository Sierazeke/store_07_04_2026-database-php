<?php
$servername = "172.22.144.1";
$username = "store_2026_04_07_user";
$password = "password";
$dbname = "store_2026_04_07";

/**
 * Connect to MySQL database
 */
function connectDatabase($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

/**
 * Optional: insert a customer without orders
 */
function insertCustomer($conn, $customer) {
    $stmt = $conn->prepare("
        INSERT IGNORE INTO customers
        (customer_id, name, surname, email, birth_date, points)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "issssi",
        $customer['customer_id'],
        $customer['name'],
        $customer['surname'],
        $customer['email'],
        $customer['birth_date'],
        $customer['points']
    );
    $stmt->execute();
    $stmt->close();
}

/**
 * Fetch all customers with their orders
 */
function fetchCustomersWithOrders($conn) {
    $sql = "
        SELECT c.customer_id, c.name, c.surname, c.email, c.birth_date, c.points,
               o.order_id, o.date AS order_date, o.status, o.comment, o.delivery_date
        FROM customers c
        LEFT JOIN orders o ON c.customer_id = o.customer_id
        ORDER BY c.customer_id, o.date
    ";
    $result = $conn->query($sql);

    $rows = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * Transform flat SQL result into hierarchical structure
 */
function transformToHierarchical($rows) {
    $customers = [];
    foreach ($rows as $row) {
        $cid = $row['customer_id'];
        if (!isset($customers[$cid])) {
            $customers[$cid] = [
                'customer_id' => $cid,
                'name' => $row['name'],
                'surname' => $row['surname'],
                'email' => $row['email'],
                'birth_date' => $row['birth_date'],
                'points' => $row['points'],
                'orders' => []
            ];
        }
        if ($row['order_id'] !== null) {
            $customers[$cid]['orders'][] = [
                'order_id' => $row['order_id'],
                'date' => $row['order_date'],
                'status' => $row['status'],
                'comment' => $row['comment'],
                'delivery_date' => $row['delivery_date']
            ];
        }
    }
    return $customers;
}

/**
 * Render customers and orders as HTML
 */
function renderHTML($customers) {
    echo "<h1>Customers and Orders (Hierarchical)</h1>";
    foreach ($customers as $customer) {
        echo "<h2>Customer: {$customer['name']} {$customer['surname']} (ID: {$customer['customer_id']})</h2>";
        echo "<p>Email: {$customer['email']} | Birth Date: {$customer['birth_date']} | Points: {$customer['points']}</p>";

        if (!empty($customer['orders'])) {
            echo "<table border='1' cellpadding='5'>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Comment</th>
                        <th>Delivery Date</th>
                    </tr>";
            foreach ($customer['orders'] as $order) {
                echo "<tr>
                        <td>{$order['order_id']}</td>
                        <td>{$order['date']}</td>
                        <td>{$order['status']}</td>
                        <td>{$order['comment']}</td>
                        <td>{$order['delivery_date']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p><em>No orders</em></p>";
        }
    }
}

// Main execution
$conn = connectDatabase($servername, $username, $password, $dbname);

insertCustomer($conn, [
    'customer_id' => 4,
    'name' => 'Lucy',
    'surname' => 'Green',
    'email' => 'lucy.green@example.com',
    'birth_date' => '1995-09-10',
    'points' => 50
]);

$rows = fetchCustomersWithOrders($conn);
$customers = transformToHierarchical($rows);
renderHTML($customers);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database HTML</title>
</head>
<style>
    body {
        background-color: #dfdfdf;
        text-align: center;
        font-family: Arial, sans-serif;
    }
    h1 {
        border-radius: 6px;
        background-color: #cfcfcf;
        padding: 10px;
    }
    table {
        margin: 20px auto;
        border-collapse: collapse;
        text-align: left;
        font-family: monospace;
    }
    th, td {
        padding: 5px 10px;
        border: 1px solid black;
    }
</style>
</html>