
		{$message_stack_block->render()}
		
		<div class="simplebox grid740">
			<form action="{$form_action}" method="POST" enctype="multipart/form-data">
				{foreach item=group key=group_name from=$tree}
					<div class="titleh">
						<h3> 
				            {$group_names.$group_name}
		            	</h3>
					</div>
					<div class="body">
						{foreach item=param key=param_name from=$group}
							<div class="st-form-line">	
								<span class="st-labeltext" style="width: 200px">{$param->param_displayed_name}{if $param->is_mandatory} *{/if}</span>	
								{$param->renderField()}
								<div class="clear"></div>
							</div>
						{/foreach}
					</div>				
				
				{/foreach}
				<div class="body">
					<div class="button-box">					
						<input type="submit" class="st-button" value="Применить" id="button" name="apply">
						<input type="reset" class="st-clear" value="Сбросить" id="button2" name="button">
					</div>
				</div>	
			</form>	                                    
		</div>        
        
