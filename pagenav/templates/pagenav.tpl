

{strip}
					<div class="pagenav">
						{foreach item=page from=$page_links}
							{if $page->type!='prev' && $page->type!='next'}
								{if $page->disabled}
									<span class="disabled">
										{$page->caption}
									</span>
								{else}
									<a class="default" href="{$page->link}">
										{$page->caption}
									</a>
								{/if}	
							{/if}
						{/foreach}
					</div>
{/strip}