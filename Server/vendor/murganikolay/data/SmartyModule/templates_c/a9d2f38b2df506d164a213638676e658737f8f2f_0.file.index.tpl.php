<?php
/* Smarty version 3.1.29, created on 2016-11-13 13:43:19
  from "/exp/www/TaskManagerOnline/Server/view/error/index.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58285fe7a984b3_16596420',
  'file_dependency' => 
  array (
    'a9d2f38b2df506d164a213638676e658737f8f2f' => 
    array (
      0 => '/exp/www/TaskManagerOnline/Server/view/error/index.tpl',
      1 => 1479037223,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58285fe7a984b3_16596420 ($_smarty_tpl) {
?>
<h1>An error occurred</h1>
<h2><?php echo $_smarty_tpl->tpl_vars['this']->value->message;?>
</h2>

<?php if ($_smarty_tpl->tpl_vars['this']->value->display_exceptions) {?>

<?php if ($_smarty_tpl->tpl_vars['this']->value->exception) {?>
<hr/>
<h2>Additional information:</h2>
<h3><?php echo get_class($_smarty_tpl->tpl_vars['this']->value->exception);?>
</h3>
<dl>
    <dt>File:</dt>
    <dd>
        <pre class="prettyprint linenums"><?php echo $_smarty_tpl->tpl_vars['this']->value->exception->getFile();?>
:<?php echo $_smarty_tpl->tpl_vars['this']->value->exception->getLine();?>
</pre>
    </dd>
    <dt>Message:</dt>
    <dd>
        <pre class="prettyprint linenums"><?php echo $_smarty_tpl->tpl_vars['this']->value->exception->getMessage();?>
</pre>
    </dd>
    <dt>Stack trace:</dt>
    <dd>
        <pre class="prettyprint linenums"><?php echo $_smarty_tpl->tpl_vars['this']->value->exception->getTraceAsString();?>
</pre>
    </dd>
</dl>
<?php $_smarty_tpl->tpl_vars['e'] = new Smarty_Variable($_smarty_tpl->tpl_vars['this']->value->exception->getPrevious(), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'e', 0);
if ($_smarty_tpl->tpl_vars['e']->value) {?>
<hr/>
<h2>Previous exceptions:</h2>
<ul class="unstyled">
    <?php
while ($_smarty_tpl->tpl_vars['e']->value) {?>
    <li>
        <h3><?php echo get_class($_smarty_tpl->tpl_vars['e']->value);?>
</h3>
        <dl>
            <dt>File:</dt>
            <dd>
                <pre class="prettyprint linenums"><?php echo $_smarty_tpl->tpl_vars['e']->value->getFile();?>
:<?php echo $_smarty_tpl->tpl_vars['e']->value->getLine();?>
</pre>
            </dd>
            <dt>Message:</dt>
            <dd>
                <pre class="prettyprint linenums"><?php echo $_smarty_tpl->tpl_vars['e']->value->getMessage();?>
</pre>
            </dd>
            <dt>Stack trace:</dt>
            <dd>
                <pre class="prettyprint linenums"><?php echo $_smarty_tpl->tpl_vars['e']->value->getTraceAsString();?>
</pre>
            </dd>
        </dl>
        <?php $_smarty_tpl->tpl_vars['e'] = new Smarty_Variable($_smarty_tpl->tpl_vars['e']->value->getPrevious(), null);
$_smarty_tpl->ext->_updateScope->updateScope($_smarty_tpl, 'e', 0);?>
        <?php }?>

    </li>
</ul>
<?php }
} else { ?>
<h3>No Exception available</h3>
<?php }
}
}
}
