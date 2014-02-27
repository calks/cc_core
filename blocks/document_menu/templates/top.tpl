

	<ul id="topnav" class="sf-menu">
		{foreach item=item from=$menu name=top_nemu_loop}
			<li {if $item->active}class="current-menu-item"{/if}>
				<a {if $item->children}href="" onclick="return false"{else}href="{$item->link}"{/if} {if $item->open_new_window} target="_blank"{/if}>
					{$item->title|@mb_strtolower:"utf8"}
				</a>
						
				{if $item->children}
					<ul class="sub-menu">
						{foreach item=sub_item from=$item->children}
							<li {if $sub_item->active}class="current-menu-item"{/if}>
								<a href="{$sub_item->link}"  {if $sub_item->open_new_window} target="_blank"{/if}>
									{$sub_item->title|@mb_strtolower:"utf8"}
								</a>
							</li>
						{/foreach}
					</ul>
				{/if}						
			</li>									
		{/foreach}	
	</ul>


