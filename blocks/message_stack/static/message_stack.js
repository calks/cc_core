
	//type = enum{'error', 'success', 'information', 'warning'}

	function addMessageStackMessage(type, content) {
		var container = jQuery('#message_stack-block');
		if (container.size==0) return;
		
		content = content + '<a class="close tips" original-title="закрыть" href="#">закрыть</a>'; 
		
		var messagebox = jQuery('<div />').addClass('albox').addClass(type + 'box').html(content).appendTo(container);
		
	}