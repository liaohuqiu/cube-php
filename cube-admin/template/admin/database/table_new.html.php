<form class="form-horizontal" action='table-new-submit' method='post'>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="_j_input_server_group_key">server_group_key</label>
        <div class="col-sm-4">
            <select class='form-control' name='server_group_key' id='_j_input_server_group_key'>
                <?php $page_data->o('serverGroupOptions', false); ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="only_schema">Is System Admin</label>
        <div class="col-md-4">
            <div class='checkbox'>
                <label>
                    <input type='checkbox' id='only_schema' name='only_schema' value='1' />
                    Check to only create schme information
                </label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="_j_input_sql">sql</label>
        <div class="col-sm-6">
            <textarea rows="15" class="form-control col-sm-6 create-table-sql" name="sql" id="_j_input_sql" placeholder=""></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-4">
            <button type="submit" class="btn btn-primary">create table</button>
        </div>
    </div>
</form>
