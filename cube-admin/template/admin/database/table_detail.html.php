<form class="form-horizontal" action="table-detail" method="post" name="add_form" onsubmit="return onSubmitForm();">
    <div class="col-md-4">
        <input class='form-control' name="kind" type="text" value='<?php $kind ?>' placeholder='table kind'>
    </div>
    <div class="col-md-4">
        <button type="submit" class="btn btn-primary">query table information</button>
    </div>
</form>
<hr/>
<div class='col-md-10'>
    <p><?php echo $kind; ?>: <?php echo count($list); ?> table(s).</p>

    <?php foreach ($list as $item): ?>
    <p>
    <?php $item->o('tableName'); ?>
    In  <?php $item->o('host'); ?>,  sid(server id) is <?php $item->o('sid'); ?></p>
    <p>Mysql Connection String(Maste):  <?php $item->o('connectionStr'); ?> -A</p>
    <?php endforeach; ?>
    <pre>
<?php echo $sql; ?>
    </pre>

</div>
<!-- 表单验证 -->
<script>

$('form[name="add_form"]').submit(function(ev) {

    if($.trim(this['kind'].value) === '') {
        alert('table kind can not be empty!');
        ev.preventDefault();
    }
});

</script>
