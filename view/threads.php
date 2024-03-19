<?php
    if (INIT != "1314") { exit(1); }
    
    //  _____ _   _ ____  _____    _    ____  ____   __     _____ _______        __
    // |_   _| | | |  _ \| ____|  / \  |  _ \/ ___|  \ \   / /_ _| ____\ \      / /
    //   | | | |_| | |_) |  _|   / _ \ | | | \___ \   \ \ / / | ||  _|  \ \ /\ / / 
    //   | | |  _  |  _ <| |___ / ___ \| |_| |___) |   \ V /  | || |___  \ V  V /  
    //   |_| |_| |_|_| \_\_____/_/   \_\____/|____/     \_/  |___|_____|  \_/\_/   
                                                                                
?>


<script type="text/javascript">

//   _____                 _   _                 
//  |  ___|   _ _ __   ___| |_(_) ___  _ __  ___ 
//  | |_ | | | | '_ \ / __| __| |/ _ \| '_ \/ __|
//  |  _|| |_| | | | | (__| |_| | (_) | | | \__ \
//  |_|   \__,_|_| |_|\___|\__|_|\___/|_| |_|___/

async function create_thread() {
    const titleValue = document.querySelector(`#title-editor`).value
    const msgValue = tinymce.activeEditor.getContent('#editor')
    const urlParams = new URLSearchParams(window.location.search);
    if (!urlParams.has('section')) {
        console.log('No section')
        return false
    }
    sectionId = urlParams.get('section')
    // Llamar al servidor para crear el post
    try {
        const response = await fetch(`index.php?action=create_thread&section=${sectionId}`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                title: titleValue,
                msg: msgValue
            })
        })
        //console.log(await response.text())
        const jsonData = await response.json()
        console.log(jsonData)
        if (jsonData.status === 0) {
            window.location.href = window.location.href
        }
    }
    catch (e) {
        console.log(e)
    } 
}


async function delete_thread(threadId) {
    // Confirmar antes de eliminar
    if (!confirm('Are you sure you want to delete this thread?')) {
        return;
    }

    const response = await fetch(`index.php?action=delete_thread&id=${threadId}`) 
    const jsonData = await response.json()

    if(jsonData.status === 0) {
        window.location.href = jsonData['redirectUrl']
    }
}

tinymce.init({
    selector: '#editor',
    width: "90%",
    height: 300,
    menubar: false,
    plugins: 'emoticons wordcount',
    toolbar: 'undo redo | formatselect | ' +
    'bold italic backcolor | alignleft aligncenter ' +
    'alignright alignjustify | bullist numlist outdent indent | ' +
    'removeformat | emoticons',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
    max_chars: 2000,
    setup: function(editor) {

        // Evento para manejar cambios de teclado y pegado
        editor.on('keydown keyup', function(e) {
            // Si es keydown, verifica si se debe permitir la entrada basada en el conteo de caracteres
            if (e.type === 'keydown') {
                const content = editor.getContent({format: 'text'});
                if (content.length >= 2000 && e.keyCode !== 8 && e.keyCode !== 46) { // 8 es backspace, 46 es delete
                    e.preventDefault();
                }
            }
        });

        // Manejar el evento de pegado para limitar el contenido
        editor.on('paste', function(e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text').substring(0, 2000);
            const content = editor.getContent({format: 'text'});
            if (content.length + text.length > 2000) {
                // Calcula cuántos caracteres más se pueden pegar
                const allowedLength = 2000 - content.length;
                const trimmedText = text.substring(0, allowedLength);
                editor.insertContent(trimmedText);
            }
            else {
                editor.insertContent(text);
            }
        });

    }
});

</script>
<!-- 
     ____  _                   _                  
    / ___|| |_ _ __ _   _  ___| |_ _   _ _ __ ___ 
    \___ \| __| '__| | | |/ __| __| | | | '__/ _ \
     ___) | |_| |  | |_| | (__| |_| |_| | | |  __/
    |____/ \__|_|   \__,_|\___|\__|\__,_|_|  \___| 
--> 
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
                <strong><?=$section['title']?></strong>
            </span>
        </span>
    </ul>
</nav>
<div id="threads-wp">
    <?php
    // POR CADA THREAD
    foreach($threads as $thread) {
    ?>  
    <div class="thread-wp thread_<?=$thread['id']?>">
        <article class="thread-content-wp">
            <div>
                <a href="index.php?view=posts&section=<?=$_GET['section']?>&thread=<?=$thread['id']?>"><h3><?=$thread['title']?></h3></a>
                <div class="thread-info">
                    <p><?=$thread['creation_date']?> | by <?=$forumController->get_username_by_user_id($thread['user_id'])?></p>
                </div>
            </div>
            <div class="posts-info">
                <p><?=$forumController->count_thread_posts($thread['id'])?> Replies</p>
            </div> 
        </article>
        <?php
        // SI EL USUARIO ES EL CREADOR DEL THREAD
        if($userController->get_is_connected() && $thread['user_id'] == $userController->get_user_id()) {
        ?>
        <div class="dropdown ellipsis-wp">
            <i class="fa-solid fa-ellipsis-vertical"></i> 
            <div class="dropdown-content">
                <label onclick ="delete_thread(<?=$thread['id']?>)">
                    <i class="fa-solid fa-trash"></i>
                    <p>Delete</p>
                </label>
            </div>
        </div>
        <?php
        }
        ?>
    </div>
    <?php
    }
    ?>
</div>
<!-- SI EL USUARIO ESTÁ CONECTADO -->
<?php if($userController->get_is_connected()) {
?>
    <div id="create-thread-wp">
        <article class="create-thread">
            <div class="title-form">
                <h3>Create new thread</h3>
            </div>
            <label><input id="title-editor" type="text" name="title" maxlength="100" placeholder="Title" required></label>
            <label><textarea id="editor" name="msg" rows="10" cols="20" placeholder="I think that ..."></textarea></label>
            <button class="submit" onclick="create_thread()">Create Thread</button>
            <?php
                if(isset($_GET['msg']) && strtolower($_GET['msg']) == "thread_created_success") {
                    echo '<div class="msg-container"><i class="checkmark  fa-solid fa-check"></i><h3>The section has been created successfully</h3></div>';
                }
            ?>
        </article>
    </div>
    <script>
       function characterCount() {
            const wordCount = tinymce.activeEditor.plugins.wordcount;
            alert(wordcount.body.getCharacterCountWithoutSpaces());
        }
    </script>
<?php } ?>


