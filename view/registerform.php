<?php
    if (INIT != "1314") { exit(1); }

    //  ____  _____ ____ ___ ____ _____ _____ ____   __     _____ _______        __
    // |  _ \| ____/ ___|_ _/ ___|_   _| ____|  _ \  \ \   / /_ _| ____\ \      / /
    // | |_) |  _|| |  _ | |\___ \ | | |  _| | |_) |  \ \ / / | ||  _|  \ \ /\ / / 
    // |  _ <| |__| |_| || | ___) || | | |___|  _ <    \ V /  | || |___  \ V  V /  
    // |_| \_\_____\____|___|____/ |_| |_____|_| \_\    \_/  |___|_____|  \_/\_/   
                                                                                
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
                <strong>Sign up</strong>
            </span>
        </span>
    </ul>
</nav> 
<section id="rgf-container">
    <article id="rgbrand">
        <a href="index.php"><img class="logo" src="images/duckers2.png" width="120"></a>
        <p class="slogan">We are Duckers</p>
    </article>
    <article id="rgform">
        <form action="index.php?action=register" method="post">
            <label><h2 id="rgform-title">Sign up</h2></label>
            <?php
                // ALERTS
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
            <input class="register-submit" type="submit" value="Create account">
            <p id="login-question">do you have an account? <a href="index.php?view=login">Login</a></p>
        </form>
    </article>
</section>
