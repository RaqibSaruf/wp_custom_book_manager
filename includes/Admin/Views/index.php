<?php
$table = $data['table'];
$searchId = $data['search_id'] ?? 'table';
$actionUrl = $data['action_url'] ?? '';
$actionLabel = $data['action_label'] ?? 'Add';
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Books</h1>
    <?php if($actionUrl): ?>
    <a href="<?php echo $actionUrl ?>" class="page-title-action"><?php echo $actionLabel ?></a>
    <?php endif; ?>
    <hr class="wp-header-end">
    <?php $table->views(); ?>
    <form method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>">
        <?php if (isset($_REQUEST['status'])) : ?>
            <input type="hidden" name="status" value="<?php echo $_REQUEST['status'] ?>">
        <?php endif; ?>
        <?php $table->search_box('Search', $searchId); ?>
    </form>
    <form method="get">
        <?php $table->display(); ?>
    </form>
</div>