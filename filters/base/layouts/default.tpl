

	<div class="col-sm-6 object-filter">

		<form action="{$action}" method="{$method}" class="panel panel-default">
			
			{if $heading}
				<div class="panel-heading">{$heading}</div>
			{/if}	
			
			<div class="panel-body">
			
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
			
			
				<div class="form-group pull-right">
					<button type="submit" name="find" class="btn btn-primary"><span aria-hidden="true" class="glyphicon glyphicon-search"></span> Найти</button>					
				</div>
			</div>		
		</form>

	</div>
	<div class="clearfix"></div>
	