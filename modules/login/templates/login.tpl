

	<div class="ui-grid-b">
		<div class="ui-block-a"></div>
		<div class="ui-block-b">
		
			
			<form id="login_form" name="login_form" class="login-form" method="post" action="{$form_action}">
				<label>Номер телефона</label>
				<input name="login_form[login]" type="text" class="login-input-user" id="textfield" value="{$login_form.login}"/>
            
				<label>Пароль</label>
				<input name="login_form[pass]" type="password" id="textfield" value="{$login_form.pass}"/>
				
				<div class="ui-body ui-body-b" >
					<input type="submit" name="button" id="button" value="Войти" data-theme="b" />
					<a href={$register_link} data-role="button" data-theme="c">Регистрация</a>
					<a href={$remind_link} data-role="button" data-theme="c">Восстановление пароля</a>
				</div>	
			</form>
			
			<div class="messages">
				{$message_stack->render()}
			</div>	
		
		</div>
		<div class="ui-block-c"></div>
	</div>
	
	<script type="text/javascript">
		{literal}
		    jQuery(function($) {
		        $('.login-input-user').mask('+7 (999) 999-99-99');
		    });
		{/literal}
	</script>


