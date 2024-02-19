<?php
    if (INIT != "1314") { exit(1); }
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Home</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/styles.css" type="text/css">
        <script src="https://kit.fontawesome.com/cdb3baf29a.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <header>
            
            <article>
                <a href="index.php"><img class="logo" src="images/logo-2.png" width="100"></a>
            </article>
            <article id="logout-container">
                <?php
                if(!isset($_SESSION['username'])){
                ?> 
                    <label id="logout"><a href="index.php?view=login">Login</a></label>
            </article>
                <?php
                }
                else {
                ?>  <a><div id="user-container">
                        <i class="fa-regular fa-user"></i>
                        <p><?=$_SESSION['username']?></p>
                    </div></a>
                    <label id="logout"><a href="index.php?action=logout">Logout</a></label>
            </article>
            <?php
                }
            ?>
                
                
        </header>
        <div class="pattern"></div>
        <section id="content-wp">
            <?php
            include $view;
            ?>
        </section>
    </body>
</html>