

				<div class="text_block registration">
					<h1>{$document->meta_title|escape:"htmlall":"utf-8"|strip_tags:false}</h1>
					<div class="middle">
						<div class="text">
							{$document->content}
							
							{if $task != 'completed'}
								<form class="reg_form" action="{$reg_form_action}" method="post">
									{*<div class="line avatar">
										<label>Фото на аватар</label>
										{$user_form->render('avatar')}
									</div>*}
									<div class="line name {if $errors.name}error{/if}">
										<label><span class="star">*</span>ФИО</label>
										{$user_form->render('name')}
									</div>
									<div class="line city {if $errors.city}error{/if}">
										<label><span class="star">*</span>Город</label>
										{$user_form->render('city')}
									</div>
									{if $moscow_only_popup}
										<div class="moscow_only">
											{$moscow_only_popup}
										</div>
									{/if}

									<div class="line email {if $errors.email}error{/if}">
										<label><span class="star">*</span>Email</label>
										{$user_form->render('email')}
									</div>
									<div class="line pass {if $errors.pass}error{/if}">
										<label><span class="star">*</span>Пароль</label>
										{$user_form->render('pass')}
									</div>
									<div class="line phone {if $errors.phone}error{/if}">
										<label><span class="star">*</span>Телефон</label>
										{$user_form->render('phone')}
									</div>
									<div class="line university {if $errors.university}error{/if}">
										<label><span class="star">*</span>Университет</label>
										{$user_form->render('university')}
									</div>
									<div class="line job_title {if $errors.job_title}error{/if}">
										<label><span class="star">*</span>Место работы</label>
										{$user_form->render('job_title')}
									</div>
									<div class="line birthdate {if $errors.birthdate}error{/if}">
										<label><span class="star">*</span>Дата рождения</label>
										{$user_form->render('birthdate')}
									</div>
									
									<div class="line question have_a_business_idea {if $errors.have_a_business_idea}error{/if}">
										<label><span class="star">*</span>Есть ли у Вас бизнес-идея?</label>
										{$user_form->render('have_a_business_idea')}
									</div>
									
									<div class="line question want_to_start_business {if $errors.want_to_start_business}error{/if}">
										<label><span class="star">*</span>Вы хотите открыть бизнес и стать предпринимателем?</label>
										{$user_form->render('want_to_start_business')}
									</div>

									<div class="line question what_support_sources_do_you_know {if $errors.what_support_sources_do_you_know}error{/if}">
										<label><span class="star">*</span>О каких формах поддержки в г.Москве вы знаете?</label>
										{$user_form->render('what_support_sources_do_you_know')}
									</div>

									<div class="line question univercity_supports_business {if $errors.univercity_supports_business}error{/if}">
										<label><span class="star">*</span>Если вы учитесь, то функционируют ли у Вас структуры в ВУЗе оказывающие поддержку начинающим предпринимателям?</label>
										{$user_form->render('univercity_supports_business')}
									</div>
									
									<div class="line question want_to_develop_business_in_univercity {if $errors.want_to_develop_business_in_univercity}error{/if}">
										<label><span class="star">*</span>Хотели ли бы Вы развивать направления молодёжного предпринимательства у себя в Университете?</label>
										{$user_form->render('want_to_develop_business_in_univercity')}
									</div>


									<div class="line question have_a_business {if $errors.have_a_business}error{/if}">
										<label><span class="star">*</span>Есть ли у Вас собственный бизнес?</label>
										{$user_form->render('have_a_business')}
									</div>

									<div class="line question what_business {if $errors.what_business}error{/if}">
										<label>Если ДА, то какой?</label>
										{$user_form->render('what_business')}
									</div>

									<div class="line question field_of_business {if $errors.field_of_business}error{/if}">
										<label><span class="star">*</span>В какой сфере Вы собираетесь открыть бизнес?</label>
										{$user_form->render('field_of_business')}
									</div>
									
									
									<div class="line submit">
										<input type="submit" name="submit" value="Отправить" />
									</div>
									{if $errors}
										<div class="errors">
											{foreach item=error from=$errors|@array_unique}
												<div class="error">{$error}</div>
											{/foreach}
										</div>								
									{/if}
								</form>
							{/if}	
							
						</div>		
					</div>
					<div class="bottom">
					</div>
				</div>	
				
				
				