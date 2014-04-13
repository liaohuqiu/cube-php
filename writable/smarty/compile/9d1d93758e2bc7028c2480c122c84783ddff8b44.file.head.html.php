<?php /* Smarty version Smarty-3.1.18, created on 2014-04-13 14:30:38
         compiled from "/home/huqiu/git/ali-tech/cube-admin/template/admin/base/head.html" */ ?>
<?php /*%%SmartyHeaderCode:57821375534a2f0eb6a546-93859789%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9d1d93758e2bc7028c2480c122c84783ddff8b44' => 
    array (
      0 => '/home/huqiu/git/ali-tech/cube-admin/template/admin/base/head.html',
      1 => 1397365838,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '57821375534a2f0eb6a546-93859789',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'header_data' => 0,
    'base_data' => 0,
    'item' => 0,
    'unit' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_534a2f0ebb9e13_44331902',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_534a2f0ebb9e13_44331902')) {function content_534a2f0ebb9e13_44331902($_smarty_tpl) {?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['header_data']->value['title'],$_smarty_tpl);?>
</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Bootstrap core CSS -->
<link href="<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['base_data']->value['static_pre_path'],$_smarty_tpl);?>
/bt3/css/bootstrap.min.css" rel="stylesheet">

<!-- Documentation extras -->
<!--[if lt IE 9]><script src="<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['base_data']->value['static_pre_path'],$_smarty_tpl);?>
/ie8-responsive-file-warning.js"></script><![endif]-->

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['base_data']->value['static_pre_path'],$_smarty_tpl);?>
/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['base_data']->value['static_pre_path'],$_smarty_tpl);?>
/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- jquery -->
<script src="<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['base_data']->value['static_pre_path'],$_smarty_tpl);?>
/jquery.min.js"></script>

<?php echo $_smarty_tpl->tpl_vars['header_data']->value['css_html'];?>

<?php echo $_smarty_tpl->tpl_vars['header_data']->value['js_html'];?>

</head>
<body>
<?php if ($_smarty_tpl->tpl_vars['header_data']->value['user']) {?>
<header class="navbar-static-top bs-docs-nav" role="banner">
    <div class="container">
        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
            <a href="<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['header_data']->value['base_path'],$_smarty_tpl);?>
" class="navbar-brand">Home</a>
            <ul class="nav navbar-nav">
                <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['header_data']->value['module_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['item']->key;
?>
                <?php if ($_smarty_tpl->tpl_vars['item']->value['is_current']) {?>
                <li class='active'>
                <?php } else { ?>
                <li>
                <?php }?>
                <a href="<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['item']->value['url'],$_smarty_tpl);?>
" title=<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['item']->value['des'],$_smarty_tpl);?>
><?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['item']->value['name'],$_smarty_tpl);?>
 </a>
                </li>
                <?php } ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                <a href="javascript:void(0)">Srain</a>
                </li>
            </ul>
        </nav>
    </div>
</header>
<?php }?>
<div class="container">
    <div class="row">
        <?php if ($_smarty_tpl->tpl_vars['header_data']->value['module_info']) {?>
        <div class="col-md-2">
            <div class='bs-sidebar'>
                <ul class="nav bs-sidenav">
                    <li>
                    <?php  $_smarty_tpl->tpl_vars['unit'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['unit']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['header_data']->value['module_info']['units']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['unit']->key => $_smarty_tpl->tpl_vars['unit']->value) {
$_smarty_tpl->tpl_vars['unit']->_loop = true;
?>
                    <a class='active'><?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['unit']->value['name'],$_smarty_tpl);?>
</a>
                    <ul class='nav'>
                        <?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['unit']->value['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
                        <?php if ($_smarty_tpl->tpl_vars['item']->value['is_current']) {?>
                        <li class='active'>
                        <?php } else { ?> 
                        <li>
                        <?php }?>
                        <a href="<?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['item']->value['url'],$_smarty_tpl);?>
"><?php echo smarty_variablefilter_htmlspecialchars($_smarty_tpl->tpl_vars['item']->value['name'],$_smarty_tpl);?>
</a>
                        </li>
                        <?php } ?> 
                    </ul>
                    </li>
                    <?php } ?> 
                </ul>
            </div>
        </div>
        <div class="col-md-10">
        <?php } else { ?>
        <div class="col-md-12">
        <?php }?>
<?php }} ?>
