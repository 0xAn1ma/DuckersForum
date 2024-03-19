<nav>
    <ul>
        <a href="index.php?view=home"><span><i class="fa-solid fa-house"></i></span></a>
        <span>
            <span>
                <a href="index.php?view=home">DuckersForum</a>
            </span>
            <span>
                <i class="fa-solid fa-angle-right"></i>
            </span>
            <span>
            <a href="index.php?view=profile">My profile</a>
            </span>
            <span>
                <i class="fa-solid fa-angle-right"></i>
            </span>
            <span>
                <strong>My replies</strong>
            </span>
        </span>
    </ul>
</nav>
<div id="posts-wp">
<?php
    // POR CADA POST
    foreach($posts as $post) {
    ?>
    <section class="complete-post">
    <?php 
            // SI EL USUARIO ES EL CREADOR DEL POST
        if($userController->get_is_connected() && $post['user_id'] == $userController->get_user_id()) {
            ?>
            <div class="post-title-wp">
                <a href="index.php?view=posts&section=<?=$post['section_id']?>&thread=<?=$post['thread_id']?>"><p>Read the full thread</p></a>
            </div>
            <div class="info-post-wp">
                <!-- POST USER PROFILE -->
                <article class="user-profile">
                    <p class="username"><?=$forumController->get_username_by_user_id($post['user_id'])?></p>
                    <img src="images/default-user" alt="avatar" width="130" height="130">
                    <hr>
                    <div class="int-info">
                        <p>Post: <?=$userController->count_posts($post['user_id']);?></p>
                        <p>Threads: <?=$userController->count_threads($post['user_id']);?></p>
                        <p>Joined: <?=$forumController->get_joined_date_by_user_id($post['user_id'])?><p>
                    </div>
                </article>
                <article class="post-msg post_content_<?=$post['id']?>">
                    <p><?=$post['creation_date']?></p>
                    <p class="post_msg_<?=$post['id']?>"><?=$post['msg']?></p>
                </article>
            </div>
        <?php
        }
        ?>
    </section>
    <?php
    }
    ?>
</div>