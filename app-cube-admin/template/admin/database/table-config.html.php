<div class='row'>
    <h4>Copy the configuration data to destination file</h4>
    <hr>
    <div class='col-md-10'>
        <p>Database deploy data:</p>
        <p><code><?php $page_data->o('deploy_data_path');?></code></p>
        <textarea id='deploy_data_str' rows='30' class='form-control col-md-5'/><?php $page_data->o('deploy_data_str', false); ?></textarea>
    </div>
</div>
