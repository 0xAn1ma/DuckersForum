<?php
    if (INIT != "1314") { exit(1); }                                                              
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
                <strong>Installer</strong>
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
    <div id="form-container">
        <form id="install-form">
            <div class="install-form-wp">
                <fieldset class="db-settings">
                    <h2>DB Settings</h2>
                    <label><input type="text" name="dbhost" placeholder="DB Host"></label>
                    <label><input type="text" name="dbname" placeholder="DB Name"></label>
                    <label><input type="text" name="dbuser" placeholder="DB Username"></label>
                    <label><input class="last-inpt" type="password" name="dbpass" placeholder="DB Password"></label>
                </fieldset>
                <fieldset class="forum-settings">
                    <h2>Forum Settigs</h2>
                    <br><br>
                    <label><input type="text" name="username" placeholder="Admin Username"></label>
                    <label><input type="text" name="email" placeholder="Admin Email"></label>
                    <label><input type="text" name="password" placeholder="Admin Password"></label>
                </fieldset> 
            </div>
            <input type='submit' class="login-submit" value="Install">
        </form>
        <script>
            
            window.onload = () => {
                document.getElementById('install-form').addEventListener('submit', async function(event) {
                    event.preventDefault()
                    event.stopPropagation();

                    const dbhost = document.querySelector('input[name=dbhost]').value
                    const dbname = document.querySelector('input[name=dbname]').value
                    const dbuser = document.querySelector('input[name=dbuser]').value
                    const dbpass = document.querySelector('input[name=dbpass]').value
                    const username = document.querySelector('input[name=username]').value
                    const email = document.querySelector('input[name=email]').value
                    const password = document.querySelector('input[name=password]').value

                    const response = await fetch("index.php?action=install", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            dbhost: dbhost,
                            dbname: dbname,
                            dbuser: dbuser,
                            dbpass: dbpass,
                            username: username,
                            email: email,
                            password: password

                        })
                    })
                    jsonData = await response.json()
                    if (jsonData.status == 0) {
                        window.location.href = jsonData.redirectUrl
                    }
                })
            }
            

        </script>
    </div>
</section>
