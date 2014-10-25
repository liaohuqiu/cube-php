<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php $header_data->o('title'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">

<!-- Bootstrap core CSS -->
<link href="<?php $base_data->o('static_pre_path'); ?>/bt3/css/bootstrap.min.css" rel="stylesheet">

<!-- Documentation extras -->
<!--[if lt IE 9]><script src="<?php $base_data->o('static_pre_path'); ?>/ie8-responsive-file-warning.js"></script><![endif]-->

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="<?php $base_data->o('static_pre_path'); ?>/html5shiv/3.7.0/html5shiv.js"></script>
<script src="<?php $base_data->o('static_pre_path'); ?>/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- jquery -->
<script src="<?php $base_data->o('static_pre_path'); ?>/jquery.min.js"></script>

<?php $header_data->o('css_html', false); ?>
<?php $header_data->o('js_html', false); ?>
</head>
<body>
<?php if ($header_data['user_data']): ?>
<header class="navbar-static-top bs-docs-nav" role="banner">
    <div class="container">
        <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
            <a href="<?php $header_data->o('base_path'); ?>" class="navbar-brand">Home</a>
            <ul class="nav navbar-nav">
                <?php foreach ($header_data['module_list'] as $key=>$item): ?>
                <?php if ($item['is_current']): ?>
                <li class='active'>
                <?php else: ?>
                <li>
                <?php endif; ?>
                <a href="<?php $item->o('url'); ?>" title=<?php $item->o('des'); ?>><?php $item->o('name'); ?> </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                <a class='' data-toggle="dropdown" href="javascript:void(0)"><?php $header_data['user_data']->o('name'); ?> <span class="caret"></span></a>
            </button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                <?php if (!$header_data['proxy_auth']): ?>
                <li role="presentation"><a id="_j_btn_change_pwd" role="menuitem" tabindex="-1" href="javascript:void(0)">Change Pwd</a></li>
                <li role="presentation" class="divider"></li>
                <?php endif; ?>
                <?php foreach ($header_data['right_links'] as $link): ?>
                <li role="presentation"><a role="menuitem" tabindex="-1" href="<?php $link->o('href');?>"><?php $link->o('name'); ?></a></li>
                <li role="presentation" class="divider"></li>
                <?php endforeach; ?>
                <li role="presentation"><a id="_j_btn_logout" role="menuitem" tabindex="-1" href="javascript:void(0)">Logout</a></li>
            </ul>
            </li>
        </ul>
    </nav>
</div>
</header>
<?php endif; ?>
<?php $_module = $header_data['module_info'];?>
<!--- begin of container --->
<div class="container">
<?php if ($header_data['show_left_side']): ?>
    <!--- begin of row --->
    <div class="row">
        <div class="col-md-2">
            <div class='bs-sidebar'>
                <ul class="nav bs-sidenav">
                    <?php foreach ($_module['units'] as $unit): ?>
                    <li>
                    <a class='active'><?php $unit->o('name'); ?></a>
                    <ul class='nav'>
                        <?php foreach ($unit['list'] as $item): ?>
                        <?php if ($item['is_current']): ?>
                        <li class='active'>
                        <?php else: ?>
                        <li>
                        <?php endif; ?>
                        <a href="<?php $item->o('url'); ?>"><?php $item->o('name'); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <!--- begin of rigth content --->
        <div class="col-md-10">
<?php else: ?>
    <?php if ($_module && !$_module['current_unit']['current_item']['no_right_nav']): ?>
    <ol class="breadcrumb">
        <li><a href="/">Home</a></li>
        <li><a href="<?php $_module->o('url'); ?>"><?php $_module->o('name'); ?></a></li>
        <li class='active'><?php $_module['current_unit']->o('name'); ?></li>
        <li class='active'><?php $_module['current_unit']['current_item']->o('name'); ?></li>
    </ol>
    <?php endif; ?>
<?php endif; ?>
