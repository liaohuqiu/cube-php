<div class="form-inline"  action='<?php $_listPageInfo.pageInfoUrl ?>'>
    <?php foreach ($page_data['quick_select'] as $key => $item): ?>
    <?php $item->o('des'); ?>
    <div class="form-group">
        <label class="sr-only"><?php $item->o('des'); ?></label>
        <p class="form-control-static"><?php $item->o('des'); ?></p>
    </div>
    <select class="form-control js-quick-select" name="<?php echo $key; ?>">
        <?php $item->o('options', false); ?>
    </select>
    <?php endforeach; ?>
</div>
