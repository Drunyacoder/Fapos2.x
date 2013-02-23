<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Email:        drunyacoder@gmail.com         |
| @Site:         http://fapos.net              |
| @Version:      1.7.8                         |
| @Project:      CMS                           |
| @Package       CMS Fapos                     |
| @Subpackege    Stats Module                  |
| @Copyright     ©Andrey Brykin 2010-2013      |
| @Last mod      2013/02/22                    |
|----------------------------------------------|
|											   |
| any partial or not partial extension         |
| CMS Fapos,without the consent of the         |
| author, is illegal                           |
|----------------------------------------------|
| Любое распространение                        |
| CMS Fapos или ее частей,                     |
| без согласия автора, является не законным    |
\---------------------------------------------*/




Class StatModule extends Module {

	/**
	* @module_title  title of module
	*/
	var $module_title = 'Статьи';
	/**
	* @template  layout for module
	*/
	var $template = 'stat';
	/**
	* @module module indentifier
	*/
	var $module = 'stat';


	
	/**
	* default action ( show main page )
	*/
	function index($tag = null) {
		//turn access
		$this->ACL->turn(array('stat', 'view_list'));
		
		
		//формируем блок со списком  разделов
		$this->_getCatsTree();
		
		
		if ($this->cached && $this->Cache->check($this->cacheKey)) {
			$source = $this->Cache->read($this->cacheKey);
			return $this->_view($source);
		}
	
		// we need to know whether to show hidden
		$query_params = array('cond' => array());
		if (!$this->ACL->turn(array('other', 'can_see_hidden'), false)) {
			$query_params['cond']['available'] = 1;
		}
		if (!empty($tag)) $query_params['cond'][] = "`tags` LIKE '%{$tag}%'";
		

		$total = $this->Model->getTotal($query_params);
		list ($pages, $page) = pagination( $total, Config::read('per_page', 'stat'), '/stat/');
		$this->Register['pages'] = $pages;
		$this->Register['page'] = $page;
		$this->page_title .= ' (' . $page . ')';


		
		$navi = array();
		$navi['add_link'] = ($this->ACL->turn(array('stat', 'add_materials'), false)) 
			? get_link(__('Add material'), '/stat/add_form/') : '';
		$navi['navigation'] = $this->_buildBreadCrumbs();
		$navi['pagination'] = $pages;
		$navi['meta'] = __('Count all material') . $total;
		$this->_globalize($navi);


		if($total <= 0) {
			$html = __('Materials not found');
			return $this->_view($html);
		}
	  
	  
		$params = array(
			'page' => $page,
			'limit' => Config::read('per_page', 'stat'),
			'order' => getOrderParam(__CLASS__),
		);
		$where = array();
		if (!$this->ACL->turn(array('other', 'can_see_hidden'), false)) $where['available'] = '1';
		if (!empty($tag)) $where[] = "`tags` LIKE '%{$tag}%'";


		$this->Model->bindModel('attaches');
		$this->Model->bindModel('author');
		$this->Model->bindModel('category');
		$records = $this->Model->getCollection($where, $params);



		if (is_object($this->AddFields) && count($records) > 0) {
			$records = $this->AddFields->mergeRecords($records);
		}


		// create markets
		$addParams = array();
		foreach ($records as $result) {
			$this->Register['current_vars'] = $result;
			$_addParams = array();
			
			
			$_addParams['moder_panel'] = $this->_getAdminBar($result);
			$entry_url = get_url(entryUrl($result, $this->module));
			$_addParams['entry_url'] = $entry_url;
			
			
			$announce = $result->getMain();
			
			
			$announce = $this->Textarier->getAnnounce($announce
				, $entry_url
				, 0 
				, $this->Register['Config']->read('announce_lenght', 'stat')
				, $result
			);
			
			
			// replace image tags in text
			$attaches = $result->getAttaches();
			if (!empty($attaches) && count($attaches) > 0) {
				$attachDir = ROOT . '/sys/files/' . $this->module . '/';
				foreach ($attaches as $attach) {
					if ($attach->getIs_image() == 1 && file_exists($attachDir . $attach->getFilename())) {
						
						
						$announce = $this->insertImageAttach(
							$announce, 
							$attach->getFilename(), 
							$attach->getAttach_number()
						);
					}
				}
			}

			$_addParams['announce'] = $announce;
			
			
			$_addParams['category_url'] = get_url('/stat/category/' . $result->getCategory_id());
			$_addParams['profile_url'] = getProfileUrl($result->getAuthor()->getId());
			$result->setTags(explode(',', $result->getTags()));


			//set users_id that are on this page
			$this->setCacheTag(array(
				'user_id_' . $result->getAuthor()->getId(),
				'record_id_' . $result->getId(),
			));
		

			$result->setAdd_markers($_addParams);
		}
		
		
		$source = $this->render('list.html', array('entities' => $records));
		
		
		//write int cache
		if ($this->cached)
			$this->Cache->write($source, $this->cacheKey, $this->cacheTags);
		
	
		return $this->_view($source);
	}


	

	 
	function category($id = null) {
		//turn access
		$this->ACL->turn(array('stat', 'view_list'));
		$id = intval($id);
		if (empty($id) || $id < 1) redirect('/');

		
		$SectionsModel = $this->_loadModel(ucfirst($this->module) . 'Sections');
		$category = $SectionsModel->getById($id);
		if (!$category)
			return showInfoMessage(__('Can not find category'), '/stat/');
		if (!$this->ACL->checkCategoryAccess($category->getNo_access())) 
			return showInfoMessage(__('Permission denied'), '/stat/');
		
		
		$this->page_title = h($category->getTitle()) . ' - ' . $this->page_title;
		
		
		//формируем блок со списком  разделов
		$this->_getCatsTree($id);
		
		
		if ($this->cached && $this->Cache->check($this->cacheKey)) {
			$source = $this->Cache->read($this->cacheKey);
			return $this->_view($source);
		}
	
		// we need to know whether to show hidden
		$childCats = $SectionsModel->getOneField('id', array('parent_id' => $id));
		$childCats[] = $id;
		$childCats = implode(', ', $childCats);
		$query_params = array('cond' => array(
			'`category_id` IN (' . $childCats . ')'
		));
		
		
		
		if (!$this->ACL->turn(array('other', 'can_see_hidden'), false)) {
			$query_params['cond']['available'] = 1;
		}
		

		$total = $this->Model->getTotal($query_params);
		list ($pages, $page) = pagination( $total, Config::read('per_page', 'stat'), '/stat/');
		$this->Register['pages'] = $pages;
		$this->Register['page'] = $page;
		$this->page_title .= ' (' . $page . ')';


		
		$navi = array();
		$navi['add_link'] = ($this->ACL->turn(array('stat', 'add_materials'), false)) 
			? get_link(__('Add material'), '/stat/add_form/') : '';
		$navi['navigation'] = $this->_buildBreadCrumbs($id);
		$navi['pagination'] = $pages;
		$navi['meta'] = __('Count material in cat') . $total;
		$navi['category_name'] = h($category->getTitle());
		$this->_globalize($navi);


		if($total <= 0) {
			$html = __('Materials not found');
			return $this->_view($html);
		}
	  
	  
		$params = array(
			'page' => $page,
			'limit' => Config::read('per_page', 'stat'),
			'order' => getOrderParam(__CLASS__),
		);
		$where = $query_params['cond'];
		if (!$this->ACL->turn(array('other', 'can_see_hidden'), false)) $where['available'] = '1';


		$this->Model->bindModel('attaches');
		$this->Model->bindModel('author');
		$this->Model->bindModel('category');
		$records = $this->Model->getCollection($where, $params);


		if (is_object($this->AddFields) && count($records) > 0) {
			$records = $this->AddFields->mergeRecords($records);
		}


		// create markets
		$addParams = array();
		foreach ($records as $result) {
			$this->Register['current_vars'] = $result;
			$_addParams = array();
			
			
			$_addParams['moder_panel'] = $this->_getAdminBar($result);
			$entry_url = get_url(entryUrl($result, $this->module));
			$_addParams['entry_url'] = $entry_url;
			
			
			$announce = $result->getMain();
			
			
			$announce = $this->Textarier->getAnnounce($announce
				, $entry_url
				, 0 
				, $this->Register['Config']->read('announce_lenght', 'stat')
				, $result
			);
			
			
			// replace image tags in text
			$attaches = $result->getAttaches();
			if (!empty($attaches) && count($attaches) > 0) {
				$attachDir = ROOT . '/sys/files/' . $this->module . '/';
				foreach ($attaches as $attach) {
					if ($attach->getIs_image() == 1 && file_exists($attachDir . $attach->getFilename())) {
						
						
						$announce = $this->insertImageAttach(
							$announce, 
							$attach->getFilename(), 
							$attach->getAttach_number()
						);
					}
				}
			}

			$_addParams['announce'] = $announce;
			
			
			$_addParams['category_url'] = get_url('/stat/category/' . $result->getCategory_id());
			$_addParams['profile_url'] = getProfileUrl($result->getAuthor()->getId());
			$result->setTags(explode(',', $result->getTags()));


			//set users_id that are on this page
			$this->setCacheTag(array(
				'user_id_' . $result->getAuthor()->getId(),
				'record_id_' . $result->getId(),
			));
		

			$result->setAdd_markers($_addParams);
		}
		
		
		$source = $this->render('list.html', array('entities' => $records));
		
		
		//write int cache
		if ($this->cached)
			$this->Cache->write($source, $this->cacheKey, $this->cacheTags);
		
	
		return $this->_view($source);
	}
	  
	  


	function view ($id = null) {
		//turn access
		$this->ACL->turn(array('stat', 'view_materials'));
		$id = intval($id);
		if (empty($id) || $id < 1) redirect('/');

		
		$this->Model->bindModel('attaches');
		$this->Model->bindModel('author');
		$this->Model->bindModel('category');
		$entity = $this->Model->getById($id);
		
		
		if (empty($entity)) redirect('/error.php?ac=404');
		if ($entity->getAvailable() == 0 && !$this->ACL->turn(array('other', 'can_see_hidden'), false)) 
			return showInfoMessage(__('Permission denied'), '/stat/');
		if (!$this->ACL->checkCategoryAccess($entity->getCategory()->getNo_access())) 
			return showInfoMessage(__('Permission denied'), '/stat/');
			
		
		// Some gemor with add fields
		if (is_object($this->AddFields)) {
			$entity = $this->AddFields->mergeRecords(array($entity));
			$entity = $entity[0];
		}
		
		
		$max_attaches = $this->Register['Config']->read('max_attaches', $this->module);
		if (empty($max_attaches) || !is_numeric($max_attaches)) $max_attaches = 5;
		
		
		//category block
		$this->_getCatsTree($entity->getCategory()->getId());
		/* COMMENT BLOCK */
		if (Config::read('comment_active', 'stat') == 1 
		&& $this->ACL->turn(array('stat', 'view_comments'), false) 
		&& $entity->getCommented() == 1) {
			if ($this->ACL->turn(array('stat', 'add_comments'), false)) 
				$this->comments_form = $this->_add_comment_form($id);
			$this->comments = $this->_get_comments($entity);
		}
		$this->Register['current_vars'] = $entity;
		
		
		//производим замену соответствующих участков в html шаблоне нужной информацией
		$this->page_title = h($entity->getTitle()) . ' - ' . $this->page_title;
		$tags = $entity->getTags();
		$description = $entity->getDescription();
		if (!empty($tags)) $this->page_meta_keywords = h($tags);
		if (!empty($description)) $this->page_meta_description = h($description);
		
		$navi = array();
		$navi['module_url'] = get_url('/stat/');
		$navi['category_url'] = get_url('/stat/category/' . $entity->getCategory()->getId());
		$navi['category_name'] = h($entity->getCategory()->getTitle());
		$navi['navigation'] = $this->_buildBreadCrumbs($entity->getCategory()->getId());
		$this->_globalize($navi);
		
		
		$markers = array();
		$markers['moder_panel'] = $this->_getAdminBar($entity);
		$markers['profile_url'] = getProfileUrl($entity->getAuthor()->getId());
		
		
		$entry_url = get_url(entryUrl($entity, $this->module));
		$markers['entry_url'] = $entry_url;
		
		
		$announce = $entity->getMain();
		$announce = $this->Textarier->print_page($announce, $entity->getAuthor()->getStatus(), $entity->getTitle());
		
		// replace image tags in text
		$attaches = $entity->getAttaches();
		if (!empty($attaches) && count($attaches) > 0) {
			$attachDir = ROOT . '/sys/files/' . $this->module . '/';
			foreach ($attaches as $attach) {
				if ($attach->getIs_image() == 1 && file_exists($attachDir . $attach->getFilename())) {
					
					
					$announce = $this->insertImageAttach(
						$announce, 
						$attach->getFilename(), 
						$attach->getAttach_number()
					);
				}
			}
		}

		$markers['mainText'] = $announce;
		$entity->setAdd_markers($markers);
		$entity->setTags(explode(',', $entity->getTags()));
		
		
		$this->setCacheTag(array(
			'user_id_' . $entity->getAuthor()->getId(),
			'record_id_' . $entity->getId(),
			(!empty($_SESSION['user']['status'])) ? 'user_group_' . $_SESSION['user']['status'] : 'user_group_' . 'guest',
		));
		
		
		$source = $this->render('material.html', array('entity' => $entity));
		
		
		$entity->setViews($entity->getViews() + 1);
		$entity->save();
		$this->Register['DB']->cleanSqlCache();
		
		return $this->_view($source);
	}




	/**
	 *
	 */
	public function add_form () {
		//turn access
		$this->ACL->turn(array('stat', 'add_materials'));
		$writer_status = (!empty($_SESSION['user']['status'])) ? $_SESSION['user']['status'] : 0;
		
		
		// categories block
		$this->_getCatsTree();
		
		
		// Additional fields
		$markers = array();
		if (is_object($this->AddFields)) {
			$_addFields = $this->AddFields->getInputs(array(), true, $this->module);
			foreach($_addFields as $k => $field) {
				$markers[strtolower($k)] = $field;
			}
		}
		
		
        // Check for preview or errors
        $data = array('title' => null, 'mainText' => null, 'in_cat' => null, 'description' => null, 'tags' => null, 'sourse' => null, 'sourse_email' => null, 'sourse_site' => null, 'commented' => null, 'available' => null);
		$data = array_merge($data, $markers);
        $data = Validate::getCurrentInputsValues($data);
        $add = $data['mainText'];
        
		
		
        $data['preview'] = $this->Parser->getPreview($data['mainText']);
        $data['errors'] = $this->Parser->getErrors();
        if (isset($_SESSION['viewMessage'])) unset($_SESSION['viewMessage']);
        if (isset($_SESSION['FpsForm'])) unset($_SESSION['FpsForm']);
		
		
		$SectionsModel = $this->_loadModel(ucfirst($this->module) . 'Sections');
		$sql = $SectionsModel->getCollection();
		$data['cats_selector'] = $this->_buildSelector($sql, ((!empty($data['in_cat'])) ? $data['in_cat'] : false));
		
		
		//comments and hide
		$data['commented'] = (!empty($commented) || !isset($_POST['submitForm'])) ? 'checked="checked"' : '';
		if (!$this->ACL->turn(array('stat', 'record_comments_management'), false)) $data['commented'] .= ' disabled="disabled"';
		$data['available'] = (!empty($available) || !isset($_POST['submitForm'])) ? 'checked="checked"' : '';
		if (!$this->ACL->turn(array('stat', 'hide_material'), false)) $data['available'] .= ' disabled="disabled"';
		
		
		$data['action'] = get_url('/stat/add/');
		$data['max_attaches'] = $this->Register['Config']->read('max_attaches', $this->module);
		if (empty($data['max_attaches']) || !is_numeric($data['max_attaches'])) $data['max_attaches'] = 5;
			
			
		//navigation panel
		$navi = array();
		$navi['navigation'] = $this->_buildBreadCrumbs();
		$this->_globalize($navi);
		
		
		$source = $this->render('addform.html', array('context' => $data));
		return $this->_view($source);
	}



	/**
	 * 
	 * Validate data and create a new record into 
	 * Data Base. If an errors, redirect user to add form
	 * and show error message where speaks as not to admit 
	 * errors in the future
	 * 
	 */
	public function add() {
		
		//turn access
		$this->ACL->turn(array('stat', 'add_materials'));
		if (!isset($_POST['title']) 
		|| !isset($_POST['mainText']) 
		|| !isset($_POST['cats_selector'])) {
			redirect('/');
		}
		if (!is_numeric($_POST['cats_selector'])) redirect('/');
		$error  = '';
		
		
		// Check additional fields if an exists.
		// This must be doing after define $error variable.
		if (is_object($this->AddFields)) {
			$_addFields = $this->AddFields->checkFields();
			if (is_string($_addFields)) $error .= $_addFields; 
		}
		
		
		$fields = array('description', 'tags', 'sourse', 'sourse_email', 'sourse_site');
		$fields_settings = $this->Register['Config']->read('fields', 'stat');
		foreach ($fields as $field) {
			if (empty($_POST[$field]) && in_array($field, $fields_settings)) {
				$error = $error.'<li>' . __('Empty field') . ' "' . $field . '"</li>'."\n";
				$$field = null;
			} else {
				$$field = trim($_POST[$field]);
			}
		}
		
		// Обрезаем переменные до длины, указанной в параметре maxlength тега input
		$title  = trim(mb_substr($_POST['title'], 0, 128));
		$add 	= trim($_POST['mainText']);
		$in_cat = intval($_POST['cats_selector']);
		$commented = (!empty($_POST['commented'])) ? 1 : 0;
		$available = (!empty($_POST['available'])) ? 1 : 0;

		// Если пользователь хочет посмотреть на сообщение перед отправкой
		if ( isset( $_POST['viewMessage'] ) ) {
			$_SESSION['viewMessage'] = array_merge(array('title' => null, 'mainText' => null, 'in_cat' => $in_cat,
				'description' => null, 'tags' => null, 'sourse' => null, 'sourse_email' => null, 
				'sourse_site' => null, 'commented' => null, 'available' => null), $_POST);
			redirect('/stat/add_form/');
		}

		// Check fields
		$valobj = new Validate;
		if (empty($in_cat))                     	
			$error = $error . '<li>' . __('Category not selected') . '</li>'."\n";
		if (empty($title))                       	
			$error = $error.'<li>' . __('Empty field "title"') . '</li>'."\n";
		elseif (!$valobj->cha_val($title, V_TITLE))  
			$error = $error.'<li>' . __('Wrong chars in "title"') . '</li>'."\n";
		if (empty($add))                    		 
			$error = $error.'<li>' . __('Empty field "material"') . '</li>'."\n";
		else if (mb_strlen($add) > Config::read('max_lenght', 'stat'))
			$error = $error .'<li>'. sprintf(__('Wery big "material"'), Config::read('max_lenght', 'stat')) .'</li>'."\n";
		if (!empty($tags) && !$valobj->cha_val($tags, V_TITLE)) 
			$error = $error.'<li>' . __('Wrong chars in "tags"') . '</li>'."\n";
		if (!empty($sourse) && !$valobj->cha_val($sourse, V_TITLE)) 
			$error = $error.'<li>' . __('Wrong chars in "sourse"') . '</li>'."\n";
		if (!empty($sourse_email) && !$valobj->cha_val($sourse_email, V_MAIL)) 
			$error = $error.'<li>' . __('Wrong chars in "email"') . '</li>'."\n";
		if (!empty($sourse_site) && !$valobj->cha_val($sourse_site, V_URL)) 
			$error = $error.'<li>' . __('Wrong chars in "sourse site"') . '</li>'."\n";

			
		// Check attaches size and format
		$max_attach = $this->Register['Config']->read('max_attaches', $this->module);
		if (empty($max_attach) || !is_numeric($max_attach)) $max_attach = 5;
		$max_attach_size = $this->Register['Config']->read('max_attaches_size', $this->module);
		if (empty($max_attach_size) || !is_numeric($max_attach_size)) $max_attach_size = 1000;
		for ($i = 1; $i <= $max_attach; $i++) {
			$attach_name = 'attach' . $i;
			if (!empty($_FILES[$attach_name]['name'])) {
			
				$img_extentions = array('.png','.jpg','.gif','.jpeg', '.PNG','.JPG','.GIF','.JPEG');
				$ext = strrchr($_FILES[$attach_name]['name'], ".");
				
				
				if ($_FILES[$attach_name]['size'] > $max_attach_size) {
					$error .= '<li>' . sprintf(__('Wery big file'), $i, round(($max_attach_size / 1000), 2)) . '</li>'."\n";
				}
				if (($_FILES[$attach_name]['type'] != 'image/jpeg'
				&& $_FILES[$attach_name]['type'] != 'image/jpg'
				&& $_FILES[$attach_name]['type'] != 'image/gif'
				&& $_FILES[$attach_name]['type'] != 'image/png')
				|| !in_array(strtolower($ext), $img_extentions)) {
					$error .= '<li>' . __('Wrong file format') . '</li>'."\n";
				}
			}
		}
			
		
		$categoryModel = ucfirst($this->module) . 'SectionsModel';
		$categoryModel = new $categoryModel;
		$cat = $categoryModel->getCollection(array('id' => $in_cat));
		if (empty($cat)) $error .= '<li>' . __('Can not find category') . '</li>'."\n";
			
			
		// Errors
		if (!empty($error)) {
			$_SESSION['FpsForm'] = array_merge(array('title' => null, 'mainText' => null, 'in_cat' => $in_cat,
				'description' => null, 'tags' => null, 'sourse' => null, 'sourse_email' => null, 
				'sourse_site' => null, 'commented' => null, 'available' => null), $_POST);
			$_SESSION['FpsForm']['error']   = '<p class="errorMsg">' . __('Some error in form') . '</p>'.
				"\n".'<ul class="errorMsg">' . "\n" . $error . '</ul>' . "\n";
			redirect('/stat/add_form/');
		}

		
		if (!$this->ACL->turn(array('stat', 'record_comments_management'), false)) $commented = '1';
		if (!$this->ACL->turn(array('stat', 'hide_material'), false)) $available = '1';

		// Защита от того, чтобы один пользователь не добавил
		// 100 материалов за одну минуту
		if ( isset( $_SESSION['unix_last_post'] ) and ( time()-$_SESSION['unix_last_post'] < 10 ) ) {
			return $this->showInfoMessage(__('Your message has been added'), '/stat/');
		}
		

		
		// Auto tags generation
		if (empty($tags)) {
			$TagGen = new MetaTags;
			$tags = $TagGen->getTags($add);
			$tags = (!empty($tags) && is_array($tags)) ? implode(',', array_keys($tags)) : '';
		}		
	
		
		//remove cache
		$this->Register['Cache']->clean(CACHE_MATCHING_ANY_TAG, array('module_' . $this->module));
		$this->Register['DB']->cleanSqlCache();
		// Формируем SQL-запрос на добавление темы	
		$add = mb_substr($add, 0, $this->Register['Config']->read('max_lenght', $this->module));
		$res = array(
			'title'        => $title,
			'main'         => $add,
			'date'         => new Expr('NOW()'),
			'author_id'    => $_SESSION['user']['id'],
			'category_id'  => $in_cat,
			'description'  => $description,
			'tags'         => $tags,
			'sourse'  	   => $sourse,
			'sourse_email' => $sourse_email,
			'sourse_site'  => $sourse_site,
			'commented'    => $commented,
			'available'    => $available,
			'view_on_home' => '1',
		);
		$className = ucfirst($this->module) . 'Entity';
		$new = new $className($res);
		
		$new->save();
		
		// Get last insert ID and save additional fields if an exists and activated.
		// This must be doing only after save main(parent) material
		$last_id = mysql_insert_id();

		if (is_object($this->AddFields)) {
			$this->AddFields->save($last_id, $_addFields);
		}
		
		downloadAttaches($this->module, $last_id);
		
		
		//clean cache
		$this->Register['Cache']->clean(CACHE_MATCHING_TAG, array('module_' . $this->module));
		$this->Register['DB']->cleanSqlCache();
		if ($this->Log) $this->Log->write('adding stat', 'ent. id(' . $last_id . ')');
		return $this->showInfoMessage(__('Material successful added'), '/stat/view/' . $last_id);				  
	}



	/**
	 * 
	 * Create form and fill his data from record which ID
	 * transfered into function. Show errors if an exists
	 * after unsuccessful attempt. Also can get data for filling
	 * from SESSION if user try preview message or create error.
	 *
	 * @param int $id material then to be edit
	 */
	public function edit_form($id = null)
    {
		$id = (int)$id;
		if ( $id < 1 ) redirect('/');
		$writer_status = (!empty($_SESSION['user']['status'])) ? $_SESSION['user']['status'] : 0;

		
		$this->Model->bindModel('attaches');
		$this->Model->bindModel('author');
		$this->Model->bindModel('category');
		$entity = $this->Model->getById($id);
		
		if (count($entity) == 0) redirect('/stat/');
		
		
		if (is_object($this->AddFields) && count($entity) > 0) {
			$entity = $this->AddFields->mergeRecords(array($entity), true);
			$entity = $entity[0];
		}
		
		
		//turn access
		if (!$this->ACL->turn(array('stat', 'edit_materials'), false) 
		&& (!empty($_SESSION['user']['id']) && $entity->getAuthor()->getId() == $_SESSION['user']['id'] 
		&& $this->ACL->turn(array('stat', 'edit_mine_materials'), false)) === false) {
			return $this->showInfoMessage(__('Permission denied'), '/stat/');
		}
		
		
		$attaches = $entity->getAttaches();
		
		$this->Register['current_vars'] = $entity;
		
		//forming categories list
		$this->_getCatsTree($entity->getCategory()->getId());
		

        $data = array(
			'title' 		=> '', 
			'mainText' 		=> $entity->getMain(), 
			'in_cat' 		=> $entity->getCategory_id(), 
			'description' 	=> '', 
			'tags' 			=> '', 
			'sourse' 		=> '', 
			'sourse_email' 	=> '', 
			'sourse_site' 	=> '', 
			'commented' 	=> '', 
			'available' 	=> '',
		);
		$data = Validate::getCurrentInputsValues($entity, $data);

		
        $preview = $this->Parser->getPreview($data->getMain());
        $errors = $this->Parser->getErrors();
        if (isset($_SESSION['viewMessage'])) unset($_SESSION['viewMessage']);
        if (isset($_SESSION['FpsForm'])) unset($_SESSION['FpsForm']);
		
		$html = '';
        if (!empty($preview)) $html .= $preview;
        else if (!empty($errors)) $html .= $errors;

		
		$className = $this->Register['ModManager']->getModelNameFromModule($this->module . 'Sections');
		$sectionModel = new $className;
		$cats = $sectionModel->getCollection();
		$selectedCatId = ($data->getIn_cat()) ? $data->getIn_cat() : $data->getCategory_id();
		$cats_change = $this->_buildSelector($cats, $selectedCatId);
		
		
		//comments and hide
		$commented = ($data->getCommented()) ? 'checked="checked"' : '';
		if (!$this->ACL->turn(array('stat', 'record_comments_management'), false)) $commented .= ' disabled="disabled"';
		$available = ($data->getAvailable()) ? 'checked="checked"' : '';
		$action = get_url('/stat/update/' . $data->getId());
		
		
		$data->setCommented($commented);
		$data->setAvailable($available);
		
		
		$attaches = $data->getAttaches();
		$attDelButtons = '';
        if (count($attaches)) {
            foreach ($attaches as $key => $attach) {
                $attDelButtons .= '<input type="checkbox" name="' . $attach->getAttach_number()
                . 'dattach"> ' . $attach->getAttach_number() . '. (' . $attach->getFilename() . ')' . "<br />\n";
            }
        }
		
		
		$markers = $data;
		$markers->setMain_text($data->getMaintext());
		$markers->setAction($action);
		$markers->setCats_selector($cats_change);
		$markers->setAttaches_delete($attDelButtons);
		$markers->setMax_attaches($this->Register['Config']->read('max_attaches', $this->module));


		//navigation panel
		$navi = array();
		$navi['navigation']  = $this->_buildBreadCrumbs($entity->getCategory_id());
		$this->_globalize($navi);


		setReferer();
		$source = $this->render('editform.html', array('context' => $markers));
		return $this->_view($source);
	}


	/**
	 * 
	 * Validate data and update record into 
	 * Data Base. If an errors, redirect user to add form
	 * and show error message where speaks as not to admit 
	 * errors in the future
	 * 
	 */
	public function update($id = null) {
		// Если не переданы данные формы - функция вызвана по ошибке
		if (!isset($id) 
		|| !isset($_POST['title']) 
		|| !isset($_POST['mainText']) 
		|| !isset($_POST['cats_selector'])) {
			redirect('/');
		}
		$id = (int)$id;
		if ($id < 1) redirect('/stat/');
		$error = '';
		

		$target = $this->Model->getbyId($id);
		if (!$target) redirect('/stat/');
		
		
		//turn access
		if (!$this->ACL->turn(array('stat', 'edit_materials'), false) 
		&& (!empty($_SESSION['user']['id']) && $target->getAuthor_id() == $_SESSION['user']['id'] 
		&& $this->ACL->turn(array('stat', 'edit_mine_materials'), false)) === false) {
			return $this->showInfoMessage(__('Permission denied'), '/stat/');
		}
		
		
		// Check additional fields if an exists.
		// This must be doing after define $error variable.
		if (is_object($this->AddFields)) {
			$_addFields = $this->AddFields->checkFields();
			if (is_string($_addFields)) $error .= $_addFields; 
		}
		
		
		$valobj = $this->Register['Validate'];
		$fields = array('description', 'tags', 'sourse', 'sourse_email', 'sourse_site');
		$fields_settings = $this->Register['Config']->read('fields', 'stat');
		foreach ($fields as $field) {
			if (empty($_POST[$field]) && in_array($field, $fields_settings)) {
				$error = $error.'<li>' . __('Empty field') . '"' . $field . '"</li>'."\n";
				$$field = '';
			} else {
				$$field = trim($_POST[$field]);
			}
		}
		
		// Обрезаем переменные до длины, указанной в параметре maxlength тега input
		$title  = trim(mb_substr($_POST['title'], 0, 128));
		$edit   = trim($_POST['mainText']);
		$commented = (!empty($_POST['commented'])) ? 1 : 0;
		$available = (!empty($_POST['available'])) ? 1 : 0;
        $in_cat = intval($_POST['cats_selector']);

		
		// Если пользователь хочет посмотреть на сообщение перед отправкой
		if (isset($_POST['viewMessage'])) {
			$_SESSION['viewMessage'] = array_merge(array('title' => null, 'mainText' => null, 'in_cat' => $in_cat,
				'description' => null, 'tags' => null, 'sourse' => null, 'sourse_email' => null, 
				'sourse_site' => null, 'commented' => null, 'available' => null), $_POST);
			redirect('/stat/edit_form/' . $id);
		}
		
		
		// Check fields
		if (empty($title))                   	
			$error = $error.'<li>' . __('Empty field "title"') . '</li>'."\n";
		if (!$valobj->cha_val($title, V_TITLE))  	
			$error = $error.'<li>' . __('Wrong chars in "title"') . '</li>'."\n";
		if (empty($edit))                 		
			$error = $error.'<li>' . __('Empty field "material"') . '</li>'."\n";
		else if (mb_strlen($edit) > Config::read('max_lenght', 'stat'))
			$error = $error . '<li>' . sprintf(__('Wery big "material"'), Config::read('max_lenght', 'stat')) .'</li>'."\n";
		if (!empty($tags) && !$valobj->cha_val($tags, V_TITLE)) 
			$error = $error.'<li>' . __('Wrong chars in "tags"') . '</li>'."\n";
		if (!empty($sourse) && !$valobj->cha_val($sourse, V_TITLE)) 
			$error = $error.'<li>' . __('Wrong chars in "sourse"') . '</li>'."\n";
		if (!empty($sourse_email) && !$valobj->cha_val($sourse_email, V_MAIL)) 
			$error = $error.'<li>' . __('Wrong chars in "email"') . '</li>'."\n";
		if (!empty($sourse_site) && !$valobj->cha_val($sourse_site, V_URL)) 
			$error = $error.'<li>' . __('Wrong chars in "sourse site"') . '</li>'."\n";
		
		
		
		$className = $this->Register['ModManager']->getModelNameFromModule($this->module . 'Sections');
		$catModel = new $className;
		$category = $catModel->getById($in_cat);
		if (!$category) $error = $error.'<li>' . __('Can not find category') . '</li>'."\n";
		

        // Check attaches size and format
        $max_attach = $this->Register['Config']->read('max_attaches', $this->module);
        if (empty($max_attach) || !is_numeric($max_attach)) $max_attach = 5;
        $max_attach_size = $this->Register['Config']->read('max_attaches_size', $this->module);
        if (empty($max_attach_size) || !is_numeric($max_attach_size)) $max_attach_size = 1000;
        for ($i = 1; $i <= $max_attach; $i++) {
            // Delete attaches. If need
            $dattach = $i . 'dattach';
            if (array_key_exists($dattach, $_POST)) {
                deleteAttach($this->module, $id, $i);
            }

            $attach_name = 'attach' . $i;
            if (!empty($_FILES[$attach_name]['name'])) {

                $img_extentions = array('.png','.jpg','.gif','.jpeg', '.PNG','.JPG','.GIF','.JPEG');
                $ext = strrchr($_FILES[$attach_name]['name'], ".");


                if ($_FILES[$attach_name]['size'] > $max_attach_size) {
                    $error .= '<li>' . sprintf(__('Wery big file'), $i, round(($max_attach_size / 1000), 2)) . '</li>'."\n";
                }
                if (($_FILES[$attach_name]['type'] != 'image/jpeg'
                && $_FILES[$attach_name]['type'] != 'image/jpg'
                && $_FILES[$attach_name]['type'] != 'image/gif'
                && $_FILES[$attach_name]['type'] != 'image/png')
                || !in_array(strtolower($ext), $img_extentions)) {
                    $error .= '<li>' . __('Wrong file format') . '</li>'."\n";
                }
            }
        }
        downloadAttaches($this->module, $id);
		

		// Errors
		if (!empty( $error )) {
			$_SESSION['FpsForm'] = array_merge(array('title' => null, 'mainText' => null, 'in_cat' => $in_cat, 
				'description' => null, 'tags' => null, 'sourse' => null, 'sourse_email' => null, 
				'sourse_site' => null, 'commented' => null, 'available' => null), $_POST);
			$_SESSION['FpsForm']['error']   = '<p class="errorMsg">' . __('Some error in form') . '</p>'
				."\n".'<ul class="errorMsg">'."\n".$error.'</ul>'."\n";
			redirect('/stat/edit_form/' . $id);
		}
		

		if (!$this->ACL->turn(array('stat', 'record_comments_management'), false)) $commented = '1';
		if (!$this->ACL->turn(array('stat', 'hide_material'), false)) $available = '1';
		
		
		
		// Auto tags generation
		if (empty($tags)) {
			$TagGen = new MetaTags;
			$tags = $TagGen->getTags($edit);
			$tags = (!empty($tags) && is_array($tags)) ? implode(',', array_keys($tags)) : '';
		}
		
		
		//remove cache
		$this->Cache->clean(CACHE_MATCHING_TAG, array('module_stat', 'record_id_' . $id));
		$this->Register['DB']->cleanSqlCache();
		
		$edit = mb_substr($edit, 0, $this->Register['Config']->read('max_lenght', 'stat'));
		$data = array(
			'title' 	   => $title,
			'main' 		   => $edit,
			'category_id'  => $in_cat,
			'description'  => $description,
			'tags'         => $tags,
			'sourse'  	   => $sourse,
			'sourse_email' => $sourse_email,
			'sourse_site'  => $sourse_site,
			'commented'    => $commented,
			'available'    => $available,
		);
		$target->__construct($data);
		$target->save();
		if (is_object($this->AddFields)) {
			$this->AddFields->save($id, $_addFields);
		}
		
		
		if ($this->Log) $this->Log->write('editing stat', 'ent. id(' . $id . ')');
		return $this->showInfoMessage(__('Operation is successful'), getReferer());
	}



	/**
	 * Check user access and if all right
	 * delete record with geting ID.
	 *
	 * @param int $id
	 */
	public function delete($id = null) {
		$this->cached = false;
		$id = (int)$id;
		if ($id < 1) redirect('/');


		$target = $this->Model->getById($id);
		if (!$target) redirect('/');
		
		
		//turn access
		if (!$this->ACL->turn(array('stat', 'delete_materials'), false) 
		&& (!empty($_SESSION['user']['id']) && $target->getAuthor_id() == $_SESSION['user']['id'] 
		&& $this->ACL->turn(array('stat', 'delete_mine_materials'), false)) === false) {
			return showInfoMessage(__('Permission denied'), '/stat/');
		}
		
		
		//remove cache
		$this->Cache->clean(CACHE_MATCHING_TAG, array('module_stat', 'record_id_' . $id));
		$this->Register['DB']->cleanSqlCache();

		$target->delete();
		
		$user_id = (!empty($_SESSION['user']['id'])) ? intval($_SESSION['user']['id']) : 0;
		if ($this->Log) $this->Log->write('delete stat', 'ent. id(' . $id . ') user id('.$user_id.')');
		return $this->showInfoMessage(__('Operation is successful'), getReferer());
	}


	
	/**
	* add comment to stat
	*
	* @id (int)    stat ID
	* @return      info message
	*/
	public function add_comment($id = null) {
		include_once(ROOT . '/sys/inc/includes/add_comment.php');
	}
	
	
	/**
	* add comment form to stat
	*
	* @id (int)    stat ID
	* @return      html form
	*/
	private function _add_comment_form($id = null) {
		include_once(ROOT . '/sys/inc/includes/_add_comment_form.php');
		return $html;
	}
	
	
	
	/**
	* edit comment form to stat
	*
	* @id (int)    comment ID
	* @return      html form
	*/
	public function edit_comment_form($id = null) {
		include_once(ROOT . '/sys/inc/includes/edit_comment_form.php');
	}
	
	
	
	/**
	* update comment
	*
	* @id (int)    comment ID
	* @return      info message
	*/
	public function update_comment($id = null) {
		include_once(ROOT . '/sys/inc/includes/update_comment.php');
	}
	
	
	
	/**
	* get comments for stat
	*
	* @id (int)    stat ID
	* @return      html comments list
	*/
	private function _get_comments($entity = null) {
		include_once(ROOT . '/sys/inc/includes/_get_comments.php');
		return $html;
	}
	
	
	
	/**
	* delete comment
	*
	* @id (int)    comment ID
	* @return      info message
	*/
	public function delete_comment($id = null) {
		include_once(ROOT . '/sys/inc/includes/delete_comment.php');
	}	
	
	

	/**
	* @param int $id - record ID
	*
	* update date by record also up record in recods list
	*/
	public function upper($id) {
		//turn access
		$this->ACL->turn(array('stat', 'up_materials'));
		$id = (int)$id;
		if ($id < 1) redirect('/stat/');

		
		$entity = $this->Model->getById($id);
		if (!$entity) redirect('/stat/');
		
		$entity->setDate(date("Y-m-d H-i-s"));
		$entity->save();
		return $this->showInfoMessage(__('Operation is successful'), '/stat/');
	}

	
	
	/**
	* @param int $id - record ID
	*
	* allow record be on home page
	*/
	public function on_home($id) {
		//turn access
		$this->ACL->turn(array('stat', 'on_home'));
		$id = (int)$id;
		if ($id < 1) redirect('/stat/');

		
		$entity = $this->Model->getById($id);
		if (!$entity) redirect('/stat/');
		
		$entity->setView_on_home('1');
		$entity->save();
		return $this->showInfoMessage(__('Operation is successful'), '/stat/');
	}


	
	/**
	* @param int $id - record ID
	*
	* denied record be on home page
	*/
	public function off_home($id) {
		//turn access
		$this->ACL->turn(array('stat', 'on_home'));
		$id = (int)$id;
		if ($id < 1) redirect('/stat/');

		
		$entity = $this->Model->getById($id);
		if (!$entity) redirect('/stat/');
		
		$entity->setView_on_home('0');
		$entity->save();
		return $this->showInfoMessage(__('Operation is successful'), '/stat/');
	}
	
	
	
	/**
	* @param int $id - record ID
	*
	* fix or unfix record on top on home page
	*/
	public function fix_on_top($id) {
		$this->ACL->turn(array('stat', 'on_home'));
		$id = (int)$id;
		if ($id < 1) redirect('/stat/');

		$target = $this->Model->getById($id);
		if (!$target) redirect('/');
		
		$curr_state = $target->getOn_home_top();
		$dest = ($curr_state) ? '0' : '1';
		$target->setOn_home_top($dest);
		$target->save();
		return $this->showInfoMessage(__('Operation is successful'), '/stat/');
	}
	
		
	
	
	/**
	* @param array $record - assoc record array
	* @return string - admin buttons
	*
	* create and return admin bar
	*/
	protected function _getAdminBar($record) {
		$moder_panel = '';
		$id = $record->getId();
		
		if ($this->ACL->turn(array('stat', 'edit_materials'), false) 
		|| (!empty($_SESSION['user']['id']) && $record->getAuthor_id() == $_SESSION['user']['id'] 
		&& $this->ACL->turn(array('stat', 'edit_mine_materials'), false))) {
			$moder_panel .= get_link(get_img('/sys/img/edit_16x16.png'), '/stat/edit_form/' . $id) . '&nbsp;';
		}
		
		if ($this->ACL->turn(array('stat', 'up_materials'), false)) {
			$moder_panel .= get_link(get_img('/sys/img/star.png'), 
			'/stat/fix_on_top/' . $id, array('onClick' => "return confirm('" . __('Are you sure') . "')")) . '&nbsp;';
			$moder_panel .= get_link(get_img('/sys/img/up_arrow_16x16.png'), 
			'/stat/upper/' . $id, array('onClick' => "return confirm('" . __('Are you sure') . "')")) . '&nbsp;';
		}
		if ($this->ACL->turn(array('stat', 'on_home'), false)) {
				if ($record->getView_on_home() == 1) {
					$moder_panel .= get_link(get_img('/sys/img/round_ok.png', array('title' => __('On home'))), 
					'/stat/off_home/' . $id, array('onClick' => "return confirm('" . __('Are you sure') . "')")) . '&nbsp;';
				} else {
					$moder_panel .= get_link(get_img('/sys/img/round_not_ok.png', array('title' => __('On home'))), 
					'/stat/on_home/' . $id, array('onClick' => "return confirm('" . __('Are you sure') . "')")) . '&nbsp;';
				}
		}
		
		if ($this->ACL->turn(array('stat', 'delete_materials'), false) 
		|| (!empty($_SESSION['user']['id']) && $record->getAuthor_id() == $_SESSION['user']['id'] 
		&& $this->ACL->turn(array('stat', 'delete_mine_materials'), false))) {
			$moder_panel .= get_link(get_img('/sys/img/delete_16x16.png'), 
			'/stat/delete/' . $id, array('onClick' => "return confirm('" . __('Are you sure') . "')")) . '&nbsp;';
		}
		return $moder_panel;
	}
	
	
	

    /**
     * RSS 
	 *
     */
    function rss() {
		include_once ROOT . '/sys/inc/includes/rss.php';
    }	
	
}
