<?php
/* Smarty version 3.1.29, created on 2016-11-13 13:18:48
  from "/exp/www/TaskManagerOnline/Server/view/error/404.tpl" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_58285a285078a0_08713590',
  'file_dependency' => 
  array (
    '90756ad2d1dc16d77c176b609e43590305456f47' => 
    array (
      0 => '/exp/www/TaskManagerOnline/Server/view/error/404.tpl',
      1 => 1479037223,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_58285a285078a0_08713590 ($_smarty_tpl) {
?>
<h1>A 404 error occurred</h1>
<h2><?php echo '<?php ';?>echo $this->message <?php echo '?>';?></h2>

<?php echo '<?php ';?>if (isset($this->reason) && $this->reason): <?php echo '?>';?>

<?php echo '<?php
';?>$reasonMessage= '';
switch ($this->reason) {
    case 'error-controller-cannot-dispatch':
        $reasonMessage = 'The requested controller was unable to dispatch the request.';
        break;
    case 'error-controller-not-found':
        $reasonMessage = 'The requested controller could not be mapped to an existing controller class.';
        break;
    case 'error-controller-invalid':
        $reasonMessage = 'The requested controller was not dispatchable.';
        break;
    case 'error-router-no-match':
        $reasonMessage = 'The requested URL could not be matched by routing.';
        break;
    default:
        $reasonMessage = 'We cannot determine at this time why a 404 was generated.';
        break;
}
<?php echo '?>';?>

<p><?php echo '<?php ';?>echo $reasonMessage <?php echo '?>';?></p>

<?php echo '<?php ';?>endif <?php echo '?>';?>

<?php echo '<?php ';?>if (isset($this->controller) && $this->controller): <?php echo '?>';?>

<dl>
    <dt>Controller:</dt>
    <dd><?php echo '<?php ';?>$this->escape($this->controller) <?php echo '?>';
echo '<?php
';?>if (isset($this->controller_class) 
    && $this->controller_class
    && $this->controller_class != $this->controller
) {
    echo " (resolves to " . $this->escape($this->controller_class) . ")";
}
<?php echo '?>';?>
</dd>

<?php echo '<?php ';?>endif <?php echo '?>';?>

<?php echo '<?php ';?>if (isset($this->exception) && $this->exception): <?php echo '?>';?>

<h2>Exception:</h2>

<p><b><?php echo '<?php ';?>echo $this->escape($this->exception->getMessage()) <?php echo '?>';?></b></p>

<h3>Stack trace</h3>

<pre>
<?php echo '<?php ';?>echo $this->exception->getTraceAsString() <?php echo '?>';?>
</pre>

<?php echo '<?php ';?>endif <?php echo '?>';
}
}
