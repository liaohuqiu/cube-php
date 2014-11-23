<?php
$_listPageInfo = array();
?>
<div class='row'>
    <?php if ($_listPageInfo['title']): ?>
    <h4><?php $_listPageInfo->o('title'); ?></h4>
    <?php endif; ?>
    <div class='row mb-10'>
        <?php if ($page_data['conf']['edit_info']['can_create']): ?>
        <div class='col-md-1'>
            <a class='btn btn-primary' href="<?php $page_data->o('url_create_new'); ?>">Add</a>
        </div>
        <?php endif; ?>
        <?php if ($page_data['quick_select']): ?>
        <div class="col-md-7">
            <?php include $view->getTemplate('admin/widget/list_header.html'); ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="row mb-10">
        <?php if ($searchInfo): ?>
        <div class="span4">
            <form class="form-horizontal" action='<?php $_listPageInfo->o('pageInfoUrl'); ?>' method = 'POST'>
                <?php $searchInfo->o('desc'); ?>：
                <input class="input-mini" name='search_value' type='text' value='<?php $searchInfo->o('searchValue'); ?>' />
                <input class="btn" type='submit' value='search' />
                <?php if ($_listPageInfo['download']): ?>
                <input class="btn" id = "__j_button_download" type='button' value='download' />
                <?php endif; ?>
            </div>
        </form>
        <?php endif; ?>
    </div>
    <?php if ($page_data['row_list'] || $searchInfo || $_listPageInfo['download']): ?>
    <div class="page-info">
    </div>
    <?php endif; ?>
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <?php if ($page_data['conf']['edit_info']['can_delete']): ?>
                <th>
                    delete
                </th>
                <?php endif; ?> <?php foreach ($page_data['thead'] as $headKey => $item): ?>
                <th>
                    <?php if ($item['url_reverse_order']): ?>
                    <a href='<?php $item->o('url_reverse_order'); ?>' title="revers order"><?php $item->o('name'); ?></a>
                    <?php else: ?>
                    <?php $item->o('name'); ?>
                    <?php endif; ?>
                </th>
                <?php endforeach; ?>
                <?php if ($page_data['conf']['edit_info']['can_edit']): ?>
                <th>
                    edit
                </th>
                <?php endif; ?>
                <?php if ($_listPageInfo['subTable']): ?>
                <th>
                    sub list
                </th>
                <?php endif; ?>
            </tr>
        </thead>
        <?php foreach ($page_data['row_list'] as $row): ?>
        <tr>
            <?php if ($page_data['conf']['edit_info']['can_delete']): ?>
            <td>
                <center>
                    <a class="_j_del" href="<?php $row->o('url_delete_info'); ?>">delete</a>
                </center>
            </td>
            <?php endif; ?>
            <?php foreach ($page_data['thead'] as $headKey => $columnHeadInfo): ?>
            <td>
                <<?php $columnHeadInfo->o('align'); ?>>
                <?php if ($columnHeadInfo['url_this_value']): ?>
                <a href='<?php $columnHeadInfo->o('url_this_value'); ?>&
                    <?php echo $headKey; ?>=<?php $row['dataItem']->o($headKey); ?>'><?php $row['dataItem']->o($headKey, false); ?></a>
                <?php else: ?>
                <?php $row['dataItem']->o($headKey, false); ?>
                <?php endif; ?>
                </<?php $columnHeadInfo->o('align'); ?>>
    </td>
    <?php endforeach; ?>
    <?php if ($page_data['conf']['edit_info']['can_edit']): ?>
    <td>
        <center>
            <a href="<?php $row->o('url_edit_info'); ?>">edit</a>
        </center>
    </td>
        <?php endif; ?>
        <?php if ($_listPageInfo['subTable']): ?>
    <td>
        <center>
            <?php if ($row['subTableLink']): ?> <a href="<?php $row->o('subTableLink'); ?>">detail</a> <?php else: ?> none
            <?php endif; ?>
        </center>
    </td>
    <?php endif; ?>
</tr>
<?php endforeach; ?>
    </table>
    <?php if ($page_data['row_list']): ?>
    <div class="page-info">
    <?php include $view->getTemplate('admin/widget/list_pageinfo.html'); ?>
    </div>
    <?php endif; ?>
    <form id="form_num_perpage" action='<?php $_listPageInfo['pageInfoUrl']; ?>' method = 'POST'>
        <input type='hidden' name='pageinfo_num_perpage' value='' id='__j_id_pageinfo_num_perpage'/>
    </form>
    <form id="form_download" action='<?php $_listPageInfo['pageInfoUrl']; ?>' method = 'POST'>
        <input id = "__j_input_action_name" type='hidden' name='action_name' value='' />
    </form>
</div>
<script>
var $ = window.jQuery;
//<!-- 删除警告 -->
$('._j_del').click(function(ev) {
    if (!confirm('Are you sure to delete?')) {
        ev.preventDefault();
    }
});
//翻页提交
$('.__j_pageinfo_num_perpage').change(function(e) {
    $("#__j_id_pageinfo_num_perpage").val(e.target.value);
    $('#form_num_perpage').submit();
});
//下载
$('#__j_button_download').click(function(e) {
    $("#__j_input_action_name").val("download");
    $('#form_download').submit();
    $("#__j_input_action_name").val("");
});
</script>
