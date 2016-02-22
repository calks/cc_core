
	{if $page->content}
	    <div class="content">
	    
	    	{if $breadcrumbs}
	    		{$breadcrumbs->render()}
	    	{/if}	
	
	        <h1>{$page->title|escape:"htmlall":"utf-8"|strip_tags:false}</h1>
	
	        {$page->content}
	    </div>
	{/if} 
