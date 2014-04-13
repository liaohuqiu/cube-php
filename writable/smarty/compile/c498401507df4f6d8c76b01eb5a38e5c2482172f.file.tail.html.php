<?php /* Smarty version Smarty-3.1.18, created on 2014-04-13 14:30:38
         compiled from "/home/huqiu/git/ali-tech/cube-admin/template/admin/base/tail.html" */ ?>
<?php /*%%SmartyHeaderCode:76503318534a2f0ebc3724-93342820%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c498401507df4f6d8c76b01eb5a38e5c2482172f' => 
    array (
      0 => '/home/huqiu/git/ali-tech/cube-admin/template/admin/base/tail.html',
      1 => 1397326351,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '76503318534a2f0ebc3724-93342820',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'tail_data' => 0,
    'base_data' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_534a2f0ebc7b25_77196693',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_534a2f0ebc7b25_77196693')) {function content_534a2f0ebc7b25_77196693($_smarty_tpl) {?>                </div> <!--- end of right content --->
            </div> <!--- end of fluid row --->
        </div><!--- end of container --->
        <?php echo $_smarty_tpl->tpl_vars['tail_data']->value['js_html'];?>

        <script src="<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['base_data']->value['static_pre_path'],$_smarty_tpl);?>
/bt3/js/bootstrap.min.js"></script>
    </body>
</html>
<?php }} ?>
