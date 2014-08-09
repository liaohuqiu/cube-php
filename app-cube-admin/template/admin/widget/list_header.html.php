<form class="form-horizontal" id="form1" action='<?php $_listPageInfo.pageInfoUrl ?>' method="POST">
    <?php foreach $_listPageInfo.easySelectInfo.itemList as $item ?>
    <?php $item.des ?>
    <?php $id = "__j_drop_select_<?php $item.field ?>" ?> <?php if $item.type == "select" ?>
    <select class="input-small" id="<?php $id ?>" onchange="window.jQuery('#form1').submit();" name="<?php $item.field ?>">
        <?php $item.options nofilter ?>
    </select>
    <?php else: ?>
    <input type="text" id="<?php $id ?>" name="<?php $item.field ?>" value="<?php $item.value ?>" />
    <?php endif; ?>
    <?php endforeach; ?>

    <script>
        var $ = window.jQuery;
        <?php foreach $_listPageInfo.easySelectInfo.itemList as $item ?>
        <?php if $item.type == 'date' ?>
        $('#__j_drop_select_<?php $item.field ?>').simpleDatepicker({ x: 0, y: $('#__j_drop_select_<?php $item.field ?>').outerHeight() });
        <?php endif; ?>
        <?php endforeach; ?>
    </script>

    <input type="submit"/>
</form>
