<div class='col-sm-offset-2 col-sm-8 mt-10'>
    <?php if (!$page_data['error_msg'] && !$page_data['warning_msg']): ?>
    <div class="alert alert-success"><?php $page_data->o('ok_msg', false); ?></div>
    <?php else:?>
    <?php if ($page_data['error_msg']): ?>
    <div class="alert alert-danger"><?php $page_data->o('error_msg', false); ?></div>
    <?php endif;?>
    <?php if ($page_data['warning_msg']): ?>
    <div class="alert alert-warning"><?php $page_data->o('warning_msg', false); ?></div>
    <?php endif;?>
    <?php endif;?>
</div>
<?php if (!$page_data['has_init']): ?>
<div class='row'>
    <div class="form-horizontal col-md-6" role="form">
        <h4>Database for System Config</h2>
        <hr class='mr-50'/>
        <div class="form-group">
            <label for="db_key" class="col-sm-4 control-label">Group Key</label>
            <div class="col-sm-6">
                <input type="email" class="form-control" id="db_key" value="sys-config">
            </div>
        </div>
        <div class="form-group">
            <label for="db_host" class="col-sm-4 control-label">Host</label>
            <div class="col-sm-6">
                <input class="form-control" id="db_host" value="127.0.0.1">
            </div>
        </div>
        <div class="form-group">
            <label for="db_port" class="col-sm-4 control-label">Port</label>
            <div class="col-sm-6">
                <input class="form-control" id="db_port" value="3306">
            </div>
        </div>
        <div class="form-group">
            <label for="db_user" class="col-sm-4 control-label">User</label>
            <div class="col-sm-6">
                <input class="form-control" id="db_user" value="root">
            </div>
        </div>
        <div class="form-group">
            <label for="db_pwd" class="col-sm-4 control-label">Password</label>
            <div class="col-sm-6">
                <input class="form-control" id="db_pwd">
            </div>
        </div>

        <div class="form-group">
            <label for="db_name" class="col-sm-4 control-label">DB name</label>
            <div class="col-sm-6">
                <input class="form-control" id="db_name" value='cube_demo'>
            </div>
        </div>

        <div class="form-group">
            <label for="db_charset" class="col-sm-4 control-label">Charset</label>
            <div class="col-sm-6">
                <input class="form-control" id="db_charset" value='utf8'>
            </div>
        </div>

    </div>
    <div class="form-horizontal col-md-6" role="form">
        <h4 >System-Admin</h2>
        <hr class='mr-50'/>
        <div class="form-group">
            <label for="user_db" class="col-sm-4 control-label">Admin DB</label>
            <div class="col-sm-6">
                <input class="form-control" id="user_db" value='cube_demo'>
            </div>
        </div>
        <div class="form-group">
            <label for="user_table" class="col-sm-4 control-label">Table Name</label>
            <div class="col-sm-6">
                <input class="form-control" id="user_table" value='s_cube_admin_user'>
            </div>
        </div>
        <div class="form-group">
            <label for="user_account" class="col-sm-4 control-label">Account</label>
            <div class="col-sm-6">
                <input class="form-control" id="user_account" value='srain'>
            </div>
        </div>

        <div class="form-group">
            <label for="user_pwd" class="col-sm-4 control-label">Password</label>
            <div class="col-sm-6">
                <input class="form-control" id="user_pwd" value=''>
            </div>
        </div>
    </div>
</div>
<div class='row'>
    <div class='col-sm-offset-1 col-sm-6 mt-10'>
        <button type="button" class="btn btn-danger mr-10" id='j_btn_reset'>Clear All Config</button>
        <button type="button" class="btn btn-outline mr-10" id='j_btn_deploy'>Do Deploy</button>
        <button type="button" class="btn btn-outline mr-10" id='j_btn_get_config_info'>Get Config Information</button>
        <button type="button" class="btn btn-outline mr-10" id='j_btn_check_deploy'>Check Deploy</button>
    </div>
</div>
<div class='row'>
    <h3>Copy config</h3>
    <hr>
    <div class='col-md-5'>
        <p>System config data:</p>
        <p><code><?php $page_data->o('sys_config_path');?></code></p>
        <textarea id='sys_config_str' rows='10' class='form-control col-md-5'/></textarea>
    </div>
    <div class='col-md-offset-1 col-md-5'>
        <p>Database deploy data:</p>
        <p><code><?php $page_data->o('deploy_data_path');?></code></p>
        <textarea id='deploy_data_str' rows='10' class='form-control col-md-5'/></textarea>
    </div>
</div>
<hr class='mb-50'>
<?php endif;?>
