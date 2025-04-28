<?php
$book = $data['book'];
$genres = $data['genres'];
$authors = $data['authors'];
?>
<div class="wrap">
    <h1><?php echo $book ? 'Edit' : 'Add'; ?> Book</h1>

    <form method="post" action="admin-post.php">
        <table class="form-table">
            <input type="hidden" name="action" value="add_book">
            <?php if ($book) : ?>
                <input type="hidden" name="id" value="<?= esc_attr($book['id']) ?>">
            <?php endif; ?>
            <tr>
                <th scope="row"><label for="book_name">Book Name</label></th>
                <td><input name="name" type="text" id="book_name" value="<?php echo esc_attr($book['name'] ?? '') ?>" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="genre_id">Genre</label></th>
                <td>
                    <select name="genre_id" id="genre_id" required class="regular-text">
                        <option value="">Select Genre</option>
                        <?php foreach ($genres as $genre) : ?>
                            <option value="<?= $genre['id'] ?>" <?php selected($book['genre_id'] ?? '', $genre['id']); ?>><?= $genre['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="author_id">Author</label></th>
                <td>
                    <select name="author_id" id="author_id" required class="regular-text">
                        <option value="">Select Author</option>
                        <?php foreach ($authors as $author) : ?>
                            <option value="<?= $author['id'] ?>" <?php selected($book['author_id'] ?? '', $author['id']); ?>><?= $author['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="publish_date">Publish Date</label></th>
                <td><input name="publish_date" type="date" id="publish_date" value="<?php echo esc_attr($book['publish_date'] ?? '') ?>" required class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="rating">Rating</label></th>
                <td><input name="rating" type="number" step="0.1" id="rating" value="<?php echo esc_attr($book['rating'] ?? '') ?>" class="small-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="thumbnail">Thumbnail</label></th>
                <td>
                    <div id="thumbnail-preview-wrapper">
                        <img id="thumbnail-preview" src="<?php echo esc_url($book['thumbnail_image'] ?? ''); ?>" style="max-width: 150px; height: auto;" />
                    </div>
                    <input type="hidden" id="thumbnail" name="thumbnail" value="<?php echo esc_attr($book['thumbnail_image'] ?? ''); ?>">
                    <button type="button" class="button" id="upload-thumbnail-button">Select Image</button>
                </td>
            </tr>
        </table>

        <?php submit_button('Save Book'); ?>
    </form>
</div>

<script>
    jQuery(document).ready(function($) {
        let mediaUploader;

        $('#upload-thumbnail-button').click(function(e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: 'Select Thumbnail',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#thumbnail').val(attachment.url);
                $('#thumbnail-preview').attr('src', attachment.url);
            });

            mediaUploader.open();
        });
    });
</script>