{$this->headLink()->appendStylesheet("`$basePath`/css/bootstrap.min.css")
    ->appendStylesheet("`$basePath`/css/jquery.dataTables.css")
    ->appendStylesheet("`$basePath`/css/jquery.dataTables.min.css")}
{$this->headScript()->appendFile("`$basePath`/js/jquery.dataTables.min.js", "text/javascript")}

<script type="text/javascript">
    $(document).ready(function () {
        $("#datatable").dataTable({
            "language": {
                "url": "/js/datatable/polish.json",
                "pageLength": 100,
            }
        });
    });
</script>
<style>
    {*
    #datatable td {
        padding: 10px 1px;
    }
    #datatable td a {
        border-right: 2px solid rgba(0,0,0,0.6);
        border-bottom: 1px solid rgba(0,0,0,0.6);
        box-shadow: 0px 1px 1px rgba(0,0,0,0.1); 
        padding: 2px 5px;
        border-radius: 0 20px 20px;
        margin: 0 10px;
    }
    
    #datatable td a:last-child {

    }
    #datatable td a:hover {
        text-decoration: none;
        text-decoration-color: black;
        box-shadow: 0px 2px 3px rgba(0,0,0,0.2); 
    }
    *}
</style>
{if $datatable->isForm()}
    {$this->form()->openTag($datatable->getForm())}
{/if}
<table id="datatable" class="display" cellspacing="0" width="100%">
    <thead>
        <tr>
            {foreach $datatable->getColumn() as $column}
                <th>
                    {$column}
                </th>
            {/foreach}
        </tr>
    </thead>
    <tfoot>
        <tr>
            {foreach $datatable->getColumn() as $column}
                <th>
                    {$column}
                </th>
            {/foreach}
        </tr>
    </tfoot>

    <tbody>
        {foreach $datatable->getData() as $row}
            <tr>
                {foreach $datatable->getColumn() as $column}
                    <td>
                        {$row.$column}
                    </td>
                {/foreach}
            </tr>
        {/foreach}
    </tbody>
</table>
{if $datatable->isForm()}
    <div style="position:relative; height: 30px; margin-top: 20px;">
        <input style="float: right; width: 100px; height: 30px;" name="save" type="submit" value="Zapisz">
    </div>

    {$this->form()->closeTag()}

{/if}

