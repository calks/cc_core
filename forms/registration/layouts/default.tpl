	<form action="{$action}" method="{$method}" enctype="multipart/form-data">
	    
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
			<input type="submit" name="save" value="Register">
			<a href="{$login_link}">Login</a>
		</div>	

	</form>

	