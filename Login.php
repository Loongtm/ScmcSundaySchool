<?php
// Connect to the database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'scmc_Sunday_School');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : ''; // 获取用户名

    // Check if the form data is complete
    if (!empty($email) && !empty($password)) {
        // Check if the email exists in the database
        $query = "SELECT * FROM SundaySchool WHERE email = '$email'";
        $result = $conn->query($query);
        
        // Check if the action is set to register
        if (isset($_POST['action']) && $_POST['action'] == 'register') {
            if ($result->num_rows > 0) {
                echo "<script>alert('Email already exists.');</script>";
            } else {
                // 注册新用户 
                $hashed_password = password_hash($password, PASSWORD_DEFAULT); // 哈希密码
                $insert_query = "INSERT INTO SundaySchool (name, email, password) VALUES ('$name', '$email', '$hashed_password')";

                if ($conn->query($insert_query) === TRUE) {
                    echo "<script>alert('Create Account Successfully');</script>";
                } else {
                    echo "<script>alert('Error: " . $conn->error . "');</script>";
                }
            }
        } else {
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                // Verify the password
                if (password_verify($password, $user['password'])) {
                    // Redirect to the welcome page after successful login
                    header("Location: Home.html");
                    exit();
                } else {
                    echo "<script>alert('Invalid username or password.');</script>";
                }
            } else {
                echo "<script>alert('Invalid username or password.');</script>";
            }
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}

// Check if the password reset request is made
if (isset($_POST['action']) && $_POST['action'] == 'reset_password') {
    $query = "SELECT * FROM SundaySchool WHERE email = '$email'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        // Here you can add logic to send the reset link
        // For example, send an email with the reset link
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="Login.css">
    <title>Customer Sign In</title>
    <link rel="icon" type="image/jpg" href="logo.jpg">

    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-24N4JQV2YQ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-24N4JQV2YQ');
</script>
    
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="Login.php" method="post">
                <h1>Create Account</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>or use your email for registration</span>
                <input type="text" placeholder="Name" name="name" required>
                <input type="email" placeholder="Email" name="email" required>
                <div class ="password-container">
                    <input type="password" placeholder="Password" name="password" required id="signup-password">
                    <span class="toggle-password" onclick="togglePassword('signup-password', this)">
                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                    </span>
                </div>
                <input type="hidden" name="action" value="register">
                <button type="submit">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="Login.php" method="post">
                <h2><center>Welcome to <br>SCMC SUNDAY SCHOOL</center></h2>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>or use your email for login</span>
                <input type="email" placeholder="Email" name="email" required>
                <div class="password-container">
                    <input type="password" placeholder="Password" name="password" required id="signin-password">
                    <span class="toggle-password" onclick="togglePassword('signin-password', this)">
                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                    </span>
                </div>
                <button type="submit">Sign In</button>
                <p><a href="#" id="forgot-password-link">Forget Your Password?</a></p>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Hello, Friend!</h1>
                    <p>Register with your personal details to use all of site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>

        <div id="forgot-password-modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Reset Your Password</h2>
                <p>Please enter your email address to receive a password reset link.</p>
                <input type="email" placeholder="Email" id="reset-email" required>
                <button onclick="sendResetLink()">Send Reset Link</button>
            </div>
        </div>

    </div>

    <script>
    function togglePassword(inputId, icon) {
        const passwordField = document.getElementById(inputId);
        const isPasswordVisible = passwordField.type === "text";
        
        passwordField.type = isPasswordVisible ? "password" : "text";
        icon.innerHTML = isPasswordVisible ? 
            '<i class="fa fa-eye-slash" aria-hidden="true"></i>' : 
            '<i class="fa fa-eye" aria-hidden="true"></i>';
    }

    document.getElementById('forgot-password-link').onclick = function(event) {
        event.preventDefault(); // 防止链接的默认行为
        document.getElementById('forgot-password-modal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('forgot-password-modal').style.display = 'none';
    }

    function sendResetLink() {
        const email = document.getElementById('reset-email').value;
        if (email) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "Login.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert(`A password reset link has been sent to ${email}`);
                    closeModal();
                } else {
                    alert('Invalid Email. Please create an account.');
                    if (confirm('Invalid Email. Would you like to create an account?')) {
                        // Redirect to registration form or handle accordingly
                        window.location.href = 'Login.php'; // Redirect to the login page
                    }
                }
            };
            xhr.send("email=" + encodeURIComponent(email) + "&action=reset_password");
        } else {
            alert('Please enter a valid email address.');
        }
    }

    window.onclick = function(event) {
        const modal = document.getElementById('forgot-password-modal');
        if (event.target === modal) {
            closeModal();
        }
    }

    const container = document.getElementById('container');
    const registerBtn = document.getElementById('register');
    const loginBtn = document.getElementById('login');

    registerBtn.addEventListener('click', () => {
        container.classList.add("active");
    });

    loginBtn.addEventListener('click', () => {
        container.classList.remove("active");
    });
    </script>

    <script src="Login.js"></script>

</body>
</html>