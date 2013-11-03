			
			
		<div id="message_stack-block">	
			{if $errors || $message}
				
				{if $errors}
					{foreach item=error from=$errors}
					
						<div class="albox errorbox">
							{$error}
							<a class="close tips" href="#" original-title="закрыть">закрыть</a>
						</div>
					{/foreach}
				{/if}
			
				{if $message}					
					<div class="albox succesbox">
						{$message}
						<a class="close tips" href="#" original-title="закрыть">закрыть</a>
					</div>
				{/if}
			
			{/if}
		</div>	
			
			