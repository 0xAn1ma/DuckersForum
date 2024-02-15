<?php
    if (INIT != "1314") { exit(1); }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Register Form</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/styles.css" type="text/css">
        <script src="https://kit.fontawesome.com/cdb3baf29a.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <section id="rgf-container">
            <article id="rgbrand">
                <a href="index.php"><img class="logo" src="images/logo-2.png" width="120"></a>
                <img class="slogan" src="images/weare.png">
            </article>
            <article id="rgform">
                <form action="index.php?action=register" method="post">
                    <?php
                        if(isset($_GET['error']) && strtolower($_GET['error']) == "not_valid_broh") {
                            echo '<div class="error-container"><i class="xmark fa-solid fa-xmark"></i><h2>Something went wrong</h2></div>';
                        }
                        if(isset($_GET['error']) && strtolower($_GET['error']) == "invalid_email") {
                            echo '<div class="error-container"><i class="xmark fa-solid fa-xmark"></i><h2>Invalid email</h2></div>';
                        }
                        if(isset($_GET['error']) && strtolower($_GET['error']) == "username_taken") {
                            echo '<div class="error-container"><i class="xmark fa-solid fa-xmark"></i><h2>User name already taken</h2></div>'; 
                        }
                    ?>
                    <label><input type="text" name="username" placeholder="Username"></label>
                    <label><input type="email" name="email" placeholder="Email"></label>
                    <label><input type="password" name="password" placeholder="Password"></label>
                    <input class="submit" type="submit" value="Create account">
                    <p id="login-question">do you have an account? <a href="index.php">Login</a></p>
                </form>
            </article>
        </section>
    </body>
</html>