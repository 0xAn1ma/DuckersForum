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
                <a><img class="logo" src="images/logo-2.png" width="100"></a>
            </article>
            <article id="logout-container">
                <a><div id="user-container">
                    <i class="fa-regular fa-user"></i>
                    <p><?=$_SESSION['username']?></p>
                </div></a>
                <label id="logout"><a href="index.php?action=logout">Logout</a></label>
            </article>
        </header>
        <div class="pattern"></div>
        <section id="content-wp">
            <div id="sections-wp">
                <?php
                foreach($sections as $section) {
                ?>
                <a>
                    <article>
                        <h2><?=$section['title']?></h2>
                        <p><?=$section['description']?></p>
                    </article>
                </a>
                <?php
                }
                ?>
            </div>
            <?php
                if(isset($_GET['error']) && strtolower($_GET['error']) == "section_name_taken") {
                    echo '<div class="error-container"><i class="xmark fa-solid fa-xmark"></i><h2>The section name already exists</h2></div>';
                }
            ?>
            <?php if($userController->is_admin === true) {

            ?>
                <div id="create-section-wp">
                    <form action="index.php?action=create_section" method="post">
                        <label><input type="text" name="title" placeholder="Title"></label>
                        <label><textarea name="description" rows="10" cols="20" placeholder="Describe what this section is about"></textarea></label>
                        <input class="submit" type="submit" value="Create section">
                        <?php
                            if(isset($_GET['msg']) && strtolower($_GET['msg']) == "section_created_success") {
                                echo '<div class="msg-container"><i class="checkmark  fa-solid fa-check"></i><h3>The section has been created successfully</h3></div>';
                            }
                        ?>
                    </form>
                </div>
            <?php } ?>
        </section>
    </body>
</html>