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
                <strong>My threads</strong>
            </span>
        </span>
    </ul>
</nav>
<div id="threads-wp">
<?php
    // POR CADA THREAD
    foreach($threads as $thread) {
    ?>
        <?php
        // SI EL USUARIO ES EL CREADOR DEL THREAD
            if($userController->get_is_connected() && $thread['user_id'] == $userController->get_user_id()) {
        ?>
            <div class="thread-wp thread_<?=$thread['id']?>">
                <article class="thread-content-wp">
                    <div>
                        <a href="index.php?view=posts&section=<?=$section['id']?>&thread=<?=$thread['id']?>"><h3><?=$thread['title']?></h3></a>
                        <div class="thread-info">
                            <p><?=$thread['creation_date']?> | by <?=$forumController->get_username_by_user_id($thread['user_id'])?></p>
                        </div>
                    </div>
                    <div class="posts-info">
                        <p><?=$forumController->count_thread_posts($thread['id'])?> Replies</p>
                    </div> 
                </article>
                <div class="dropdown ellipsis-wp">
                    <i class="fa-solid fa-ellipsis-vertical"></i> 
                    <div class="dropdown-content">
                        <label onclick ="delete_thread(<?=$thread['id']?>)">
                            <i class="fa-solid fa-trash"></i>
                            <p>Delete</p>
                        </label>
                    </div>
                </div>
            </div> 
        <?php
        }
    ?>  
    <?php
    }
    ?>
</div>