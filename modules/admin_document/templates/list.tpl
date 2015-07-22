


		<div class="simplebox grid740">
		
			<div class="dataTables_wrapper">

				<div class="titleh">
					<h3>Список страниц</h3>
					<div class="shortcuts-icons">
						<a class="shortcut tips" href="" title="Обновить"><img src="{$img_base_url}/icons/shortcut/refresh.png" width="25" height="25" alt="icon" /></a>
						{if $group_delete_link}
							<a class="shortcut tips hidden delete_multiple" href="{$group_delete_link}" title="Удалить"><img src="{$img_base_url}/icons/shortcut/delete.png" width="25" height="25" alt="icon" /></a>
						{/if}

					</div>
				</div>
				
				<table cellpadding="0" cellspacing="0" border="0" class="display data-table">
	                            
					<thead>
						<tr>
							<th class="header ui-state-default center"><input id="select_all_rows" type="checkbox" name="select_all_rows" /></th>
					        <th class="header ui-state-default">Название</th>
					        <th class="header ui-state-default">URL</th>
					        <th class="header ui-state-default">Порядок</th>
					        <th class="header ui-state-default">Активна</th>
					        <th class="header ui-state-default">В меню</th>
						</tr>
					</thead>
	                       		    		
					<tbody>
					
						{assign var="tr" value="0"}
						{include file=$line_template_path objects=$objects}
						
					</tbody>
				</table>
				<div class="fg-toolbar ui-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix">
					<div class="dataTables_info" id="example_info">
						{$count_str}
					</div>
					{if $pagenav}{$pagenav->render()}{/if}	
				</div>
			</div>			

			
		</div>
                        
    
