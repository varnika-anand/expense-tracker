<?php 
include 'db.php';
$message = "";
if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        //password ko secure hash karna- imp for viva
        $hashed_password = password_hash($password , PASSWORD_DEFAULT);
        //To check if email is registered prior
        $checkQuery = "SELECT id FROM users WHERE email=?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s" , $email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0)
            {
                $message = "Email already registered! Please login";
            } 
            else
                {
                    //to insert new user
                    $insertQuery = "INSERT INTO users (full_name, email, password) VALUES(?,?,?)";
                    $stmt = $conn->prepare($insertQuery);
                    $stmt->bind_param("sss", $full_name, $email, $hashed_password);
                    if($stmt->execute())
                        {
                            $message = "Registeration successful! You can now login ";
                        }
                        else{
                            $message = "Error" . $conn->error;
                        }
                }
                $stmt->close();
    }
    $conn->close();
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset = "UTF-8">
            <meta name = "viewport" content = "width=device-width , initial-scale=1.0">
            <title>Register - Expense Tracker</title>
            <style>
                <body>{font-family :Arial, sans-serif; background-color:#f4f7f6; display:flex; justify-content:center; align-items:center; height: 100vh; margin:0;}
                .card{background :white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0,1); width: 350px;}
                h2{text-align:center; color:#333;}
                input{width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:4px; box-sizing:border-box;}
                <button>{width:100%; padding:10px; background:#28a745; color:white; border:none; border-radius:4px; cursor: pointer; font-size:16px;}
                button:hover{background:#218838;}
                p{text-align:center; font-size:14px;}
                </style>
                </head>
<body>
<div class="card">
    <h2>Register</h2>
    <?php if (!empty($message)) echo"<p><b>$message</b></p>";?>
    <form method="POST" action="">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign Up</button>
</form>
<p>Already have an account? Login here</p>
</div>
</body>
</html>
