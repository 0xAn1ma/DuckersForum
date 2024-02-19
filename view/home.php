<?php
    if (INIT != "1314") { exit(1); }
    
?>
<script>
    async function delete_section(sectionId) {
        // Confirmar antes de eliminar
        if (!confirm('Are you sure you want to delete this section?')) {
            return;
        }

        const url = `index.php?action=delete_section&section_id=${sectionId}`
        r = await fetch(url) 
        const j = await r.json()

        if(j.status == 0) {
            const elem = document.querySelector(`.section_${sectionId}`)
            elem.remove()
        }
    }

    async function edit_section(sectionId) {
       // Confirmar antes de editar
       if (!confirm('Are you sure you want to edit this section?')) {
            return
        }

        const titleValue = document.querySelector('.estitle_input').value
        const descValue = document.querySelector('.esdesc_input').value
        
        // Llamar al servidor para editar la seccion
        try {
            const response = await fetch("http://seas-vm.test/infuria/?action=edit_section", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    title: titleValue,
                    description: descValue,
                    id: sectionId
                })
            })
            const json = await response.json()
            if (json.msg === 'ok') {
                window.location.href = window.location.href
            }
        }
        catch (e) {
            // console.log(e)
        }
        // Volver a mostrar los elementos HTML como antes (h2, p)
    }

    function toggle_edit_section(sectionId) {        
        const title = document.querySelector(`.section_title_${sectionId}`)
        const description = document.querySelector(`.section_desc_${sectionId}`)
        const content = document.querySelector(`.section_content_${sectionId}`)
        // Replace title with input
        const titleInput = document.createElement('input')
        titleInput.value = title.textContent;
        titleInput.classList = "estitle_input"
        title.parentNode.replaceChild(titleInput, title);

        // Replace description with input
        const descriptionInput = document.createElement('input')
        descriptionInput.value = description.textContent;
        // editing_section_description_input
        descriptionInput.classList = "esdesc_input"
        description.parentNode.replaceChild(descriptionInput, description);

        // Create sendButton
        const sendButton = document.createElement('button')
        sendButton.textContent = "Edit"
        sendButton.onclick = function() {
            edit_section(sectionId)
        }
        content.appendChild(sendButton)


    }
</script>
<div id="sections-wp">
    <?php
    if (count($sections) === 0) {
        echo "<h2>No sections. Please add.</h2>";
    }
    foreach($sections as $section) {
    ?>  <div class="all-section-wp section_<?=$section['id']?>">
            <article class="section-wp">
                <div class="section_content_<?=$section['id']?>">
                    <h2 class="section_title_<?=$section['id']?>"><?=$section['title']?></h2>
                    <p class="section_desc_<?=$section['id']?>"><?=$section['description']?></p>
                </div>
                <div>
                    <p>20 Threads</p>
                    <p>63 Post</p>
                </div>
            </article>
            <?php 
            if($userController->is_admin === true) {
            ?>
            <div class="dropdown ellipsis-wp">
                <i class="fa-solid fa-ellipsis-vertical"></i> 
                <div class="dropdown-content">
                    <label onclick ="toggle_edit_section(<?=$section['id']?>)">
                        <i class="fa-regular fa-pen-to-square"></i>
                        <p>Edit</p>
                    </label>
                    <label onclick ="delete_section(<?=$section['id']?>)">
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
<?php
    if(isset($_GET['error']) && strtolower($_GET['error']) == "section_name_taken") {
        echo '<div class="error-container"><i class="xmark fa-solid fa-xmark"></i><h2>The section name already exists</h2></div>';
    }
    if(isset($_GET['action']) && strtolower($_GET['action']) == "delete_section") {
        echo '<div class="msg-container"><i class="checkmark  fa-solid fa-check"></i><h3>The section has been deleted successfully</h3></div>';
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