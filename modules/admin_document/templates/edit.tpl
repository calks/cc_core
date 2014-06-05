



		<div class="simplebox grid740">
		            	
			<div class="simple-tips" style="z-index: 500;">
                <p>Поля, отмеченные звездочкой (*), обязательны для заполнения</p>
				<a class="close tips" href="#" original-title="закрыть">закрыть</a>
			</div>
		
		
			<div class="titleh">
				<h3> 
		            {if $action == 'add'}
		                Добавление страницы
		            {else}
		                Редактирование страницы
		            {/if}            	
				</h3>
			</div>
			<div class="body">
                                
				<form action="{$form_action}" method="POST" enctype="multipart/form-data">
				
					<div class="st-form-line">	
						<span class="st-labeltext">Родитель</span>	
						{$form->render('parent_id')} 
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Тип</span>	
						{$form->render('link_type')} 
						<div class="clear"></div>
					</div>
					<div class="st-form-line type-page_itself">	
						<span class="st-labeltext">URL *</span>	
		            		{if $object->protected}
		            			{$object->url}
		            		{else}
		            			{$form->render('url')}
		            		{/if}
						<div class="clear"></div>
					</div>
					<div class="st-form-line type-alias">	
						<span class="st-labeltext">Ссылка на существующую страницу *</span>	
						{$form->render('open_link')}
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Активна?</span>	
						{$form->render('active')} 
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Раздел или страница?</span>	
						{$form->render('category')}
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Название в меню *</span>	
						{$form->render('title')} 
						<div class="clear"></div>
					</div>
					<div class="st-form-line type-page_itself">	
						<span class="st-labeltext">Заголовок</span>	
						{$form->render('meta_title')}
						<div class="clear"></div>
					</div>
					<div class="st-form-line type-page_itself">	
						<span class="st-labeltext">Контент</span>
						<br /><br />	
						{$form->render('content')}
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Отображать в меню</span>	
						{$form->render('menu')}
						<div class="clear"></div>
					</div>
					<div class="st-form-line">	
						<span class="st-labeltext">Открывать в новом окне</span>	
						{$form->render('open_new_window')}
						<div class="clear"></div>
					</div>
					<div class="st-form-line type-page_itself">	
						<span class="st-labeltext">Meta Descriptions</span>	
						{$form->render('meta_desc')}
						<div class="clear"></div>
					</div>
					<div class="st-form-line type-page_itself">	
						<span class="st-labeltext">Meta Keywords</span>	
						{$form->render('meta_key')}
						<div class="clear"></div>
					</div>
					
					<div class="button-box">
						<input type="submit" class="st-button" value="Сохранить" id="button" name="save">
						<input type="submit" class="st-button" value="Применить" id="button" name="apply">
						<input type="reset" class="st-clear" value="Сбросить" id="button2" name="button">
						<input type="button" class="st-clear" onclick="javascript:window.location.href='{$back_link}'" name="back" value="Отмена">						
						<input type="hidden" name="action" value="{$action}">		            
						{$form->render('id')}                                   	  
						{$form->render('seq')}
						{$form->render('language_id')}
					</div>                                    
				</form>                                  
			</div>
		</div>        
        
        
  