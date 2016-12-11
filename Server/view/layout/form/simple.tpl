{if isset($form) }
    {$this->form()->openTag($form)}
    {$this->formCollection($form)}
    {$this->form()->closeTag()}
{/if}


{*

{if isset($form) }
<div class="form-horizontal">
{$this->form()->openTag($form)}
{foreach from=$form item=element}
{if $element}
<div class="form-group">
<label class="col-sm-2 control-label">
{$element->getLabel()}
</label>
<div class="col-sm-10">
{$this->formElement($element)}
{$this->formElementErrors($element)}
</div>
</div>
{/if}
{/foreach}
<div class="col-sm-offset-2 col-sm-10">
<button type="submit" class="btn btn-primary">Zapisz</button>
</div>
{$this->form()->closeTag()}
</div>
{/if}

*}