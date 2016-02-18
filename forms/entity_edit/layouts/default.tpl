
	<form action="{$action}" method="{$method}" enctype="multipart/form-data">
		<nav class="navbar navbar-default object-edit-form">		
			{if $heading}
				<h4 class="navbar-text">{$heading}</h4>
			{/if}	
	
			<div class="col-sm-4 pull-right">
				<input type="button" class="btn btn-default navbar-btn pull-right back-button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="{$form->gettext('Back')}">
				<input type="submit" class="btn btn-primary navbar-btn pull-right apply-button" name="apply" value="{$form->gettext('Apply')}">				
				<input type="submit" class="btn btn-primary navbar-btn pull-right save-button" name="save" value="{$form->gettext('Save')}">
			</div>
	    </nav>
	    
	    
	    {foreach item=field_data from=$fields}
	    	{if $field_data.field->getResourceName() == 'hidden'}
	    		{$field_data.field->render()}
	    	{else}
				<div class="form-group{if $field_data.required} required{/if}">
					{if $field_data.caption}
						<label class="control-label">{$field_data.caption}</label>
					{/if}	
					{$field_data.field->render()}					 
				</div>
	    	{/if}
	    {/foreach}
		
		<div class="form-group">								
			<input type="submit" class="btn btn-primary navbar-btn save-button" name="save" value="{$form->gettext('Save')}">
			<input type="submit" class="btn btn-primary navbar-btn apply-button" name="apply" value="{$form->gettext('Apply')}">
			<input type="button" class="btn btn-default navbar-btn back-button" onclick="javascript:window.location.href='{$back_link}'" name="back" value="{$form->gettext('Back')}">
		</div>

	</form>
