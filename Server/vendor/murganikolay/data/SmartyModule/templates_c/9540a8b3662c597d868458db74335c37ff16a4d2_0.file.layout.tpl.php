<?php
/* Smarty version 3.1.29, created on 2016-11-13 13:18:48
  from "/exp/www/TaskManagerOnline/Server/view/layout/layout.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58285a2857c028_22209379',
  'file_dependency' => 
  array (
    '9540a8b3662c597d868458db74335c37ff16a4d2' => 
    array (
      0 => '/exp/www/TaskManagerOnline/Server/view/layout/layout.tpl',
      1 => 1479037223,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../info/info.tpl' => 1,
  ),
),false)) {
function content_58285a2857c028_22209379 ($_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['this']->value->doctype();?>

<html lang="pl">
    <head>
        <meta charset="utf-8">
        <?php echo $_smarty_tpl->tpl_vars['this']->value->headTitle()->setSeparator(' - ')->setAutoEscape(false);?>


        <?php echo $_smarty_tpl->tpl_vars['this']->value->headMeta();?>

        <?php $_smarty_tpl->tpl_vars['basePath'] = new Smarty_Variable($_smarty_tpl->tpl_vars['this']->value->basePath(), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'basePath', 0);?>

        <!--  Styles -->
        <?php echo $_smarty_tpl->tpl_vars['this']->value->headLink(array('rel'=>'shortcut icon','type'=>'image/vnd.microsoft.icon','href'=>((string)$_smarty_tpl->tpl_vars['basePath']->value)."/images/favicon.ico"));?>

        <?php echo $_smarty_tpl->tpl_vars['this']->value->headLink()->appendStylesheet(((string)$_smarty_tpl->tpl_vars['basePath']->value)."/css/bootstrap.css")->appendStylesheet(((string)$_smarty_tpl->tpl_vars['basePath']->value)."/css/bootstrap-theme.css")->appendStylesheet(((string)$_smarty_tpl->tpl_vars['basePath']->value)."/css/bootstrap-responsive.min.css");?>


        <!-- Le styles -->
        <?php echo $_smarty_tpl->tpl_vars['this']->value->headLink();?>


        <?php echo $_smarty_tpl->tpl_vars['this']->value->headScript()->appendFile(((string)$_smarty_tpl->tpl_vars['basePath']->value)."/js/html5.js","text/javascript",array('conditional'=>'lt IE9'));?>

        <?php echo $_smarty_tpl->tpl_vars['this']->value->headScript()->appendFile(((string)$_smarty_tpl->tpl_vars['basePath']->value)."/js/jquery-1.10.2.js","text/javascript");?>

        <?php echo $_smarty_tpl->tpl_vars['this']->value->headScript()->appendFile(((string)$_smarty_tpl->tpl_vars['basePath']->value)."/js/bootstrap.min.js","text/javascript");?>

        <?php echo $_smarty_tpl->tpl_vars['this']->value->headScript()->appendFile(((string)$_smarty_tpl->tpl_vars['basePath']->value)."/js/bootstrap.js","text/javascript");?>


        <!-- Scripts -->
        <?php echo $_smarty_tpl->tpl_vars['this']->value->headScript();?>


    </head>
    <body>
        
        <nav class="navbar navbar-inverse " role="navigation">
            <div class="container-full">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="#">
                        <span class="btn-lg glyphicon glyphicon-menu-hamburger white"></span>
                    </a>

<!-- <a class="navbar-brand" href="<?php echo $_smarty_tpl->tpl_vars['this']->value->url('home');?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['this']->value->basePath('img/zf2-logo.png');?>
" alt="Zend Framework 2"/><?php echo $_smarty_tpl->tpl_vars['this']->value->translate('Skeleton Application');?>
</a> -->
                </div>
                <div class="collapse navbar-collapse">
                   GENEROWANIE RAPORTU
                </div>
            </div>
        </nav>
        <div class="">
            <?php $_smarty_tpl->smarty->ext->_subtemplate->render($_smarty_tpl, "file:../info/info.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

            <div class="container">
                <div class="col-lg-3">
                    <nav class="nav-stacked bs-docs-sidebar hidden-print hidden-xs hidden-sm">
                    </nav>
                </div>
                <div class="col-lg-9">
                    

                    <?php echo $_smarty_tpl->tpl_vars['this']->value->content;?>


                </div>
                <div class="clearfix"></div>
                <hr>
                <footer>

                    <p>&copy; 2006 - 2012 by Zend Technologies Ltd. All rights reserved.</p>
                </footer>

            </div>
        </div>
        <!-- /container -->

    </body>
</html>
<?php }
}
