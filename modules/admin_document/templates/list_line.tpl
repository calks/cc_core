


					    {foreach key=key item=object from=$objects name=objectlist}
					    
					    	{assign var="tr" value=$tr+1}
							{if $object->parent_id==""}
								{assign var='level' value=0}
							{/if}
    						{if $level>10}
    							{assign var='level' value=10}
    						{/if}
					    
					    	
			    	        {if $smarty.foreach.objectlist.first}			    	        
	            				{assign var=no_up value="1"}
	            			{else}
	            				{assign var=no_up value="0"}
	            			{/if}
	            				
	        				{if $smarty.foreach.objectlist.last}	        						
	            				{assign var=no_down value="1"}
	            			{else}	            					
	            				{assign var=no_down value="0"}
	        				{/if}

						    <tr class="gradeA {if $tr % 2 == 0}even{else}odd{/if}">
						        <td class="center">
						        	{if !$object->protected}<input type="checkbox" class="select_row" name="ids[]" value="{$object->id}">{/if}
						        </td>
						        <td style="padding-left:{$level*20+5}px">
						        	<a href="{$object->edit_link}">
						        		<img src="{if $object->category==2}{$img_base_url}/page.gif{else}{$img_base_url}/section.gif{/if}" alt="">
						        		{$object->title}
						        	</a>	
						        </td>
						        <td>
						        	{if $object->open_link != ''}
						        		[{$object->front_link}]
						        	{else}
						        		{$object->front_link}
						        	{/if}
						        </td>
						        
							        <td class="center ordering">
							        	{if !$no_up}<a href="{$object->moveup_link}"><img src="{$img_base_url}/icons/mini/arrow-up.png" class='up' alt="выше"></a>{/if}
							        	{if !$no_down}<a href="{$object->movedown_link}"><img src="{$app_img_dir}/icons/mini/arrow-down.png" class='down' alt="ниже"></a>{/if}
							        </td>
						        
						        <td class="center">
						        	{if $object->active}да{else}нет{/if}
						        </td>
						        <td class="center">						        
						        	{$object->menu_str}
						        </td>
						    </tr>
						    
						    {if $object->children}
						        {assign var='level' value=$level+1}
						        {include file=$line_template_path objects=$object->children}
						        {assign var='level' value=$level-1}
						    {/if}

				    	{/foreach}





