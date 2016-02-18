
	<form action="{$form_action}" method="POST" enctype="multipart/form-data">
		<nav class="navbar navbar-default object-edit-form">
		
			<h4 class="navbar-text">
	            {$module->gettext('Settings')}
			</h4>
	
			<div class="col-sm-4 pull-right">
				<input type="reset" class="btn btn-default navbar-btn pull-right back-button" name="button" value="{Application::gettext('reset')}">				
				<input type="submit" class="btn btn-primary navbar-btn pull-right save-button" name="apply" value="{Application::gettext('apply')}">
			</div>		
	    	
	    </nav>
	    
	    
	    
		{foreach item=group key=group_name from=$tree}
			<h3> 
				{$module->gettext($group_names.$group_name)}
			</h3>

			<div class="body">
				{foreach item=param key=param_name from=$group}
					<div class="form-group">
						<label class="control-label">{$module->gettext($param->param_displayed_name)}</label>
						{$param->renderField()}
					</div>				
				{/foreach}
			</div>				
		
		{/foreach}	    
		
				
		<div class="form-group">								
			<input type="submit" class="btn btn-primary navbar-btn save-button" name="apply" value="{Application::gettext('apply')}">
			<input type="reset" class="btn btn-default navbar-btn back-button" name="button" value="{Application::gettext('reset')}">
		</div>


		<input type="hidden" name="action" value="{$action}">

	</form>
        


