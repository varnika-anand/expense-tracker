<?php
include 'db.php';
session_start();

// Agar user login nahi hai toh use login page par bhej do
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Expense Add karne ka logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_expense'])) {
    $title = trim($_POST['title']);
    $amount = trim($_POST['amount']);
    $category = trim($_POST['category']);
    $date = $_POST['expense_date'];

    if (!empty($title) && !empty($amount) && !empty($category) && !empty($date)) {
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, title, amount, category, transaction_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdss", $user_id, $title, $amount, $category, $date);
        
        if ($stmt->execute()) {
            $message = "✅ Expense added successfully!";
        } else {
            $message = "❌ Error adding expense.";
        }
        $stmt->close();
    } else {
        $message = "⚠️ Please fill all fields!";
    }
}

// User ke saare expenses database se nikalna
$result = $conn->query("SELECT * FROM transactions WHERE user_id = $user_id ORDER BY transaction_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Expense Tracker</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2, h3 { color: #333; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        form { display: grid; gap: 10px; margin-top: 20px; background: #f9f9f9; padding: 15px; border-radius: 6px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .logout-btn { background: #dc3545; padding: 8px 15px; text-decoration: none; color: white; border-radius: 4px; font-size: 14px; }
        .logout-btn:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; }
        p { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! 👋</h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <?php if (!empty($message)) echo "<p>$message</p>"; ?>

        <h3>Add New Expense</h3>
        <form method="POST" action="">
            <input type="text" name="title" placeholder="Expense Title (e.g., Grocery, Petrol)" required>
            <input type="number" step="0.01" name="amount" placeholder="Amount (₹)" required>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="Food">Food</option>
                <option value="Travel">Travel</option>
                <option value="Bills">Bills & Utilities</option>
                <option value="Shopping">Shopping</option>
                <option value="Other">Other</option>
            </select>
            <input type="date" name="expense_date" required>
            <button type="submit" name="add_expense">Add Expense</button>
        </form>

        <h3>Your Expenses</h3>
        <table>
            <tr>
                <th>Title</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Date</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td>₹<?php echo htmlspecialchars($row['amount']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>