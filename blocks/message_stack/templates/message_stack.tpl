			
		
		
		<div class="message-stack">
			{if $stack_messages}
				{foreach item=item from=$stack_messages}
					<div class="message message-{$item.type}">
						<p>{$item.message}</p>
					</div>
				{/foreach}
			{/if}
		</div>
		
			
