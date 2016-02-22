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
		
										
		<input type="submit" name="save" value="Register">
		<a href="{$login_link}">Login</a>

	</form>

	{*<form action="{$form_action}" method="POST" enctype="multipart/form-data">
		<div class="form-group required">
			<label class="control-label" for="name">Имя</label>
			{$profile_form->render('name')}
		</div>
		<div class="form-group required">
			<label class="control-label" for="name">Фамилия</label>
			{$profile_form->render('family_name')}		</div>

		<div class="form-group required">	
			<label class="control-label" for="email">Email</label>
			{$profile_form->render('email')}
		</div>
		<div class="form-group">	
			<label class="control-label" for="email">Новый пароль</label>
			{$profile_form->render('new_pass')}
		</div>

		<div class="form-group">	
			<label class="control-label" for="email">Новый пароль еще раз</label>
			{$profile_form->render('new_pass_confirmation')}
		</div>

		<div class="form-group">								
			<input type="submit" class="btn btn-primary navbar-btn" name="save" value="Сохранить">
			<input type="reset" class="btn btn-default navbar-btn" name="reset" value="Сбросить">
		</div>

		
		<input type="hidden" name="action" value="{$action}">

	</form>*}