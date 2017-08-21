
	{if $page->content}
	    <div class="content">

	        <h1>{$page->title|escape:"htmlall":"utf-8"|strip_tags:false}</h1>
	
	        {$page->content}
	    </div>
	{/if} 
