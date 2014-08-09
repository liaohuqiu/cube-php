<form class="form-horizontal" action='' method='post'>
    <input type='hidden' name='edit_action_name' value='submit'>

    <div class="form-group">
        <label class="col-md-4 control-label" for="uid">uid</label>
        <div class="col-md-4">
            <input class='form-control' id="uid" type="text" name="uid" value='<?php $page_data->o('uid'); ?>' disabled>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label" for="user_name">Account</label>
        <div class="col-md-4">
            <input class='form-control' id="user_name" type="text" name="email" value='<?php $page_data->o('email'); ?>'
            <?php if ($page_data['uid']): ?>disabled<?php endif; ?>/>
        </div>
    </div>

    <?php if (!$page_data['uid']): ?>
    <div class="form-group">
        <label class="col-md-4 control-label" for="pwd">Password</label>
        <div class="col-md-4">
            <input class='form-control' id="pwd" type="text" name="pwd" value=''>
        </div>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label class="col-md-4 control-label" for="is_sysadmin">Is System Admin</label>
        <div class="col-md-4">
            <div class='checkbox'>
                <label>
                    <input type='checkbox' id='is_sysadmin' name='is_sysadmin' value='1' <?php $page_data->o('is_sysadmin_checked'); ?> />
                    Check to set to System Admin
                </label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label" for="">Authorization Keywords</label>
        <div class="col-md-4">
            <?php foreach ($page_data['auth_infos'] as $key => $item): ?>
            <label class='checkbox-inline'>
                <input type="checkbox" <?php $item->o('checked'); ?> value="1" name='<?php $item->o('name'); ?>' /><?php echo $key; ?>
            </label>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-4 col-sm-4">
            <button type="submit" class="btn btn-outline">Submit</button>
        </div>
    </div>
</form>
