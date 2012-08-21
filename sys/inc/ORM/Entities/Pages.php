<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.0                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    Pages Entity                  |
| @copyright     ©Andrey Brykin 2010-2012      |
| @last mod      2012/04/27                    |
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



/**
 *
 */
class PagesEntity extends FpsEntity
{
	
	protected $id;
	protected $name;
	protected $template;
	protected $content;
	protected $url;
	protected $meta_keywords;
	protected $meta_description;
	protected $parent_id;
	protected $path;
	protected $visible;


	
	
	public function save()
	{
		$params = array(
			'name' => $this->name,
			'template' => $this->template,
			'content' => $this->content,
			'url' => $this->url,
			'meta_keywords' => $this->meta_keywords,
			'meta_description' => $this->meta_description,
			'parent_id' => $this->parent_id,
			'path' => $this->path,
			'visible' => $this->visible,
		);
		if ($this->id) $params['id'] = $this->id;
		$Register = Register::getInstance();
		$id = $Register['DB']->save('pages', $params);
		if($id) $this->setId($id);
	}
	
	
	
	public function delete()
	{ 
		$Register = Register::getInstance();
		$Register['DB']->delete('pages', array('id' => $this->id));
	}

}