<form class='form-horizontal' action="table-edit-submit?action=edit&kind=<?php echo $kind; ?>" method="post" name="add_editform">
    <div class="form-group">
        <div class="col-sm-4">
            <input class='form-control' name="kind" type="text" value="<?php echo $kind; ?>" placeholder='table kind'>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-4">
            <textarea class='form-control' name="sql" class='span5' rows=10></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-4">
            <button type="submit" class='btn btn-primary'/>Alter</button>
        </div>
    </div>
</form>
<pre><?php echo $sql; ?>
</pre>
<!-- 表单验证 -->
<script>
$('form[name="add_edit_form"]').submit(function(ev) {

    if($.trim(this['kind'].value) === '') {
        alert('kind不能为空！');
        ev.preventDefault();
    }

    if($.trim(this['kind'].value) === '') {
        alert('sql语句不能为空！');
        ev.preventDefault();
    }
});
</script>
