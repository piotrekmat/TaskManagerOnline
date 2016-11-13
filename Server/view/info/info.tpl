<style>
div.status_info { margin: auto; max-width: 90%; }
div.status_info li { list-style: none; }
</style>
<div class="status_info">
{$this->flashMessenger()->renderCurrent('error',   array('alert', 'alert-dismissable', 'alert-danger'))}
{$this->flashMessenger()->renderCurrent('info',    array('alert', 'alert-dismissable', 'alert-info'))}
{$this->flashMessenger()->renderCurrent('default', array('alert', 'alert-dismissable', 'alert-warning'))}
{$this->flashMessenger()->renderCurrent('success', array('alert', 'alert-dismissable', 'alert-success'))}
</div>