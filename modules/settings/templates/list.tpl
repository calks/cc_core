
	<form action="{$form_action}" method="POST" enctype="multipart/form-data">
		<nav class="navbar navbar-default object-edit-form">
		
			<h4 class="navbar-text">
	            Настройки			
			</h4>
	
			<div class="col-sm-4 pull-right">
				<input type="reset" class="btn btn-default navbar-btn pull-right back-button" name="button" value="Сбросить">				
				<input type="submit" class="btn btn-primary navbar-btn pull-right save-button" name="apply" value="Применить">
			</div>		
	    	
	    </nav>
	    
	    
	    
		{foreach item=group key=group_name from=$tree}
			<h3> 
				{$group_names.$group_name}
			</h3>

			<div class="body">
				{foreach item=param key=param_name from=$group}
					<div class="form-group">
						<label class="control-label">{$param->param_displayed_name}</label>
						{$param->renderField()}
					</div>				
				{/foreach}
			</div>				
		
		{/foreach}	    
		
				
		<div class="form-group">								
			<input type="submit" class="btn btn-primary navbar-btn save-button" name="apply" value="Применить">
			<input type="reset" class="btn btn-default navbar-btn back-button" name="button" value="Сбросить">
		</div>


		<input type="hidden" name="action" value="{$action}">

	</form>
        


