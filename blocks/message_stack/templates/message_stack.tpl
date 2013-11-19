			
		
		{if $stack_messages}
			<div class="message-stack">
				{foreach item=item from=$stack_messages}
					<div class="message message-{$item.type}">
						<p>{$item.message}</p>
					</div>
				{/foreach}
			</div>
		{/if}
			
