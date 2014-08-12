
		{$message_stack_block->render()}
		
		<div class="simplebox grid740">
			<form class="size-optimized" action="{$form_action}" method="POST" enctype="multipart/form-data">
				{foreach item=group key=group_name from=$tree}
					<br />
					<div class="titleh">
						<h3> 
				            {$group_names.$group_name}
		            	</h3>
					</div>
					<div class="body">
						{foreach item=param key=param_name from=$group}
							{assign var=width_field value=0}
							{if $param->param_type=='rich_editor'}
								{assign var=width_field value=1}
							{/if}
							<div class="st-form-line {cycle values="odd,even"}">
								<span class="st-labeltext" style="{if $width_field}width: 100%{else}width: 200px{/if}">{$param->param_displayed_name}{if $param->is_mandatory} *{/if}</span>
								{if $width_field}<br /><br />{/if}	
								{$param->renderField()}
								<div class="clear"></div>
							</div>
						{/foreach}
					</div>				
				
				{/foreach}
				<br />
				<div class="body">
					<div class="button-box">					
						<input type="submit" class="st-button" value="Применить" id="button" name="apply">
						<input type="reset" class="st-clear" value="Сбросить" id="button2" name="button">
					</div>
				</div>	
			</form>	                                    
		</div>        
        
