<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.0                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    Themes Entity                 |
| @copyright     ©Andrey Brykin 2010-2012      |
| @last mod      2012/05/20                    |
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
class ThemesEntity extends FpsEntity
{
	
	protected $id;
	protected $title;
	protected $id_author;
	protected $time;
	protected $id_last_author;
	protected $last_post;
	protected $id_forum;
	protected $locked;
	protected $posts;
	protected $views;
	protected $important;
	protected $description;
	protected $group_access;

	
	
	
	public function save()
	{
		$params = array(
			'title' => $this->title,
			'id_author' => $this->id_author,
			'time' => $this->time,
			'id_last_author' => $this->id_last_author,
			'last_post' => $this->last_post,
			'id_forum' => $this->id_forum,
			'locked' => $this->locked,
			'posts' => $this->posts,
			'views' => $this->views,
			'important' => $this->important,
			'description' => $this->description,
			'group_access' => implode('.', (array)$this->group_access),
		);
		if ($this->id) $params['id'] = $this->id;
		$Register = Register::getInstance();
		$Register['DB']->save('themes', $params);
	}
	
	
	public function getGroup_access()
	{
		$out = (is_array($this->group_access)) ? $this->group_access : explode('.', $this->group_access);
		foreach ($out as $k => $v) if ('' === $v) unset($out[$k]);
		return $out;
	}

	
	public function delete()
	{ 
		$Register = Register::getInstance();
		$Register['DB']->delete('themes', array('id' => $this->id));
	}

}