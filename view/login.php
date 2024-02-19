<?php
    if (INIT != "1314") { exit(1); }
?>

<section id="container">
    <article id="brand">
        <div id="logo-container">
            <img class="logo" src="images/logo-2.png" width="120">
        </div>
        <div id="slogan-container">
            <img class="slogan" src="images/weare.png">
            <p id="presentation">Find your voice, embrace your authenticity, and shout to the world who you are.</p>
        </div>
        
    </article>
    <article id="form-container">
    <form action="index.php?action=login" method="post">
        <?php
            if(isset($_GET['error']) && strtolower($_GET['error']) == "incorrectpass") {
                echo '<div class="error-container"><i class="xmark fa-solid fa-xmark"></i><h2>Incorrect password or username</h2></div>';
            }
        ?>
        <label id="firstlbl"><input type="text" name="username" placeholder="Username"></label>
        <label><input type="password" name="password" placeholder="Password"></label>
        <input class="submit" type="submit" value="Login">
        <hr>
        <label id="register"><a href="index.php?view=register">Register</a></label>
        <?php
            if(isset($_GET['msg']) && strtolower($_GET['msg']) == "register_success") {
                echo '<div class="msg-container"><i class="checkmark  fa-solid fa-check"></i><h3>Successfully registered!</h3></div>';
            }
        ?>
    </form>
    </article>
</section>
