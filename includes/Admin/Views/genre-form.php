<?php
$genre = $data['genre'];
?>
<div class="wrap">
    <h1><?php echo $genre ? 'Edit' : 'Add'; ?> Genre</h1>

    <form method="post" action="admin-post.php">
        <table class="form-table">
            <input type="hidden" name="action" value="add_genre">
            <?php if ($genre) : ?>
                <input type="hidden" name="id" value="<?= esc_attr($genre['id']) ?>">
            <?php endif; ?>
            <tr>
                <th scope="row"><label for="genre_name">Genre Name</label></th>
                <td><input name="name" type="text" id="genre_name" value="<?php echo esc_attr($genre['name'] ?? '') ?>" required class="regular-text"></td>
            </tr>
        </table>

        <?php submit_button('Save Genre'); ?>
    </form>
</div>