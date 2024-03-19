<?php
    if (INIT != "1314") { exit(1); }

    //  _     ___   ____ ___ _   _  __     _____ _______        __
    // | |   / _ \ / ___|_ _| \ | | \ \   / /_ _| ____\ \      / /
    // | |  | | | | |  _ | ||  \| |  \ \ / / | ||  _|  \ \ /\ / / 
    // | |__| |_| | |_| || || |\  |   \ V /  | || |___  \ V  V /  
    // |_____\___/ \____|___|_| \_|    \_/  |___|_____|  \_/\_/   
                                                               
?>
<nav>
    <ul>
        <a href="index.php?view=home"><span><i class="fa-solid fa-house"></i></span></a>
        <span>
            <span>
                <a href="index.php?view=home">DuckersForums</a>
            </span>
            <span>
                <i class="fa-solid fa-angle-right"></i>
            </span>
            <span>
                <strong>Login</strong>
            </span>
        </span>
    </ul>
</nav> 
<section id="login-container">
    <article id="brand">
        <div id="logo-container">
            <img class="logo" src="images/duckers2.png" width="120">
        </div>
        <div id="slogan-container">
            <p class="slogan">We are Duckers</p>
            <p class="presentation">Connect and share your nest of ideas</p>
        </div>
        
    </article>
    <article id="form-container">
    <form action="index.php?action=login" method="post">
        <?php
            // ALERT: SI LA CONTRASEÑA ES INCORRECTA
            if(isset($_GET['error']) && strtolower($_GET['error']) == "incorrectpass") {
                echo '<div class="error-container"><i class="xmark fa-solid fa-xmark"></i><h2>Incorrect password or username</h2></div>';
            }
        ?>
        <label id="firstlbl"><input type="text" name="username" placeholder="Username"></label>
        <label><input type="password" name="password" placeholder="Password"></label>
        <input class="login-submit" type="submit" value="Login">
        <hr>
        <label class="register-link"><a href="index.php?view=register">Register</a></label>
        <?php
            // ALERT: SI EL REGISTRO ES SATISFACTORIO
            if(isset($_GET['msg']) && strtolower($_GET['msg']) == "register_success") {
                echo '<div class="msg-container"><i class="checkmark  fa-solid fa-check"></i><h3>Successfully registered!</h3></div>';
            }
        ?>
    </form>
    </article>
</section>
