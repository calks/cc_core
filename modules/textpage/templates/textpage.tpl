
	{if $page->content}
	    <div class="text">
	    
	    	{if $breadcrumbs}
	    		{$breadcrumbs->render()}
	    	{/if}	
	
	        <h1>{$page->meta_title|escape:"htmlall":"utf-8"|strip_tags:false}</h1>
	
	        {$page->content}
	    </div>
	{/if} 
