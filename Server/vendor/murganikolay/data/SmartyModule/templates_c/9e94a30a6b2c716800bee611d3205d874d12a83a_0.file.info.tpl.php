<?php
/* Smarty version 3.1.29, created on 2016-11-13 13:18:48
  from "/exp/www/TaskManagerOnline/Server/view/info/info.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58285a285e1047_88964356',
  'file_dependency' => 
  array (
    '9e94a30a6b2c716800bee611d3205d874d12a83a' => 
    array (
      0 => '/exp/www/TaskManagerOnline/Server/view/info/info.tpl',
      1 => 1479037223,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58285a285e1047_88964356 ($_smarty_tpl) {
?>
<style>
div.status_info { margin: auto; max-width: 90%; }
div.status_info li { list-style: none; }
</style>
<div class="status_info">
<?php echo $_smarty_tpl->tpl_vars['this']->value->flashMessenger()->renderCurrent('error',array('alert','alert-dismissable','alert-danger'));?>

<?php echo $_smarty_tpl->tpl_vars['this']->value->flashMessenger()->renderCurrent('info',array('alert','alert-dismissable','alert-info'));?>

<?php echo $_smarty_tpl->tpl_vars['this']->value->flashMessenger()->renderCurrent('default',array('alert','alert-dismissable','alert-warning'));?>

<?php echo $_smarty_tpl->tpl_vars['this']->value->flashMessenger()->renderCurrent('success',array('alert','alert-dismissable','alert-success'));?>

</div><?php }
}
