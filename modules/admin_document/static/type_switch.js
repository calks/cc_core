
	jQuery(document).ready(function(){
		
		
		function setFieldsVisibility() {
			var link_type = jQuery('input[name=link_type]:checked').val();			
			jQuery('.st-form-line.type-alias, .st-form-line.type-page_itself').addClass('hidden');
			jQuery('.st-form-line.type-' + link_type).removeClass('hidden');
		}
		
		setFieldsVisibility();
		jQuery('input[name=link_type]').click(function(){
			setFieldsVisibility();	
		});
	});