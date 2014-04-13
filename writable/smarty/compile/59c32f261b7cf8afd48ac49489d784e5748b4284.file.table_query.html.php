<?php /* Smarty version Smarty-3.1.18, created on 2014-04-13 14:30:38
         compiled from "/home/huqiu/git/ali-tech/cube-admin/template/admin/database/table_query.html" */ ?>
<?php /*%%SmartyHeaderCode:7052450534a2f0ebbe4a2-52168478%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '59c32f261b7cf8afd48ac49489d784e5748b4284' => 
    array (
      0 => '/home/huqiu/git/ali-tech/cube-admin/template/admin/database/table_query.html',
      1 => 1397360354,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7052450534a2f0ebbe4a2-52168478',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'pageData' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_534a2f0ebc1556_62996090',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_534a2f0ebc1556_62996090')) {function content_534a2f0ebc1556_62996090($_smarty_tpl) {?><div class="form-horizontal" action='<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['pageData']->value['post_url'],$_smarty_tpl);?>
' method='post'>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="_j_id_input_kind">table kind</label>
        <div class="col-sm-4">
            <input class='form-control' name="kind" type='text' id="_j_id_input_kind">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label" for="_j_id_input_sql">sql </label>
        <div class="col-sm-4">
            <textarea class='form-control' name="sql" class="span6" rows="10" id="_j_id_input_sql" placeholder='select * from table_kind limit 1'>select * from table_kind limit 1</textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-4">
            <button type="submit" class="btn btn-primary" id='_j_id_btn_query'>query</button>
        </div>
    </div>
</div>
<div>
    <div id="_j_id_msg">
    </div>
</div>
<?php }} ?>
