
	{if $page->content}
	    <div class="text">
	    
	    	{*$breadcrumbs*}
	
	        <h1>{$page->meta_title|escape:"htmlall":"utf-8"|strip_tags:false}</h1>
	
	        {$page->content}
	    </div>
	{/if} 
