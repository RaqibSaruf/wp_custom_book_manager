<?php
$author = $data['author'];
?>
<div class="wrap">
    <h1><?php echo $author ? 'Edit' : 'Add'; ?> Author</h1>

    <form method="post" action="admin-post.php">
        <table class="form-table">
            <input type="hidden" name="action" value="add_author">
            <?php if ($author) : ?>
                <input type="hidden" name="id" value="<?= esc_attr($author['id']) ?>">
            <?php endif; ?>
            <tr>
                <th scope="row"><label for="author_name">Author Name</label></th>
                <td><input name="name" type="text" id="author_name" value="<?php echo esc_attr($author['name'] ?? '') ?>" required class="regular-text"></td>
            </tr>
        </table>

        <?php submit_button('Save Author'); ?>
    </form>
</div>