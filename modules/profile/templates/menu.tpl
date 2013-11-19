	
	{strip}
		<ul class="profile-menu">
			{foreach item=item key=key from=$items}
				<li {if $item.active}class="active"{/if}>
					{if $item.active}
						<span>{$item.name}</span>
					{else}
						<a href="{$item.link}">{$item.name}</a>
					{/if}			
				</li>
			{/foreach}
		</ul>
	{/strip}	
	