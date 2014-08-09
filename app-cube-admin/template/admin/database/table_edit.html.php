<div class='row'>
    <div class='col-md-6 form-horizontal'>
        <div class="form-group">
            <div class="col-sm-12">
                <input class='form-control' id="table_kind" type="text" value="<?php echo $page_data->o('kind'); ?>" placeholder='table kind'>
            </div>
        </div>
        <div class="col-sm-12 mt-10 mb-30">
            <button type="button" class="btn btn-danger mr-30" id='j_btn_delte'>delete</button>
            <button type="button" class="btn btn-outline mr-10" id='j_btn_alter'>alter</button>
            <button type="button" class="btn btn-outline mr-10" id='j_btn_get_info'>get info</button>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <textarea class='form-control' name="sql" class='span5' rows=11 id='sql'></textarea>
            </div>
        </div>
    </div>
    <div class='col-md-6 msgbox-info' data-msgbox-width='800'>
        <pre><code id='result'><?php $page_data->o('msg');?></code></pre>
    </div>
</div>
