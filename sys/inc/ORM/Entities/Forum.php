<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.0                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    Forum Entity                  |
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
class ForumEntity extends FpsEntity
{
	
	protected $id;
	protected $title;
	protected $description;
	protected $pos;
	protected $in_cat;
	protected $last_theme_id;
	protected $themes;
	protected $posts;
	protected $parent_forum_id;
	protected $lock_posts;
	protected $lock_passwd;

	
	
	
	public function save()
	{
		$params = array(
			'title' => $this->title,
			'description ' => $this->description ,
			'pos' => $this->pos,
			'in_cat' => $this->in_cat,
			'last_theme_id' => $this->last_theme_id,
			'themes' => $this->themes,
			'posts' => $this->posts,
			'parent_forum_id' => $this->parent_forum_id,
			'lock_posts' => $this->lock_posts,
			'lock_passwd' => $this->lock_passwd,
		);
		if ($this->id) $params['id'] = $this->id;
		$Register = Register::getInstance();
		return ($Register['DB']->save('forums', $params));
	}
	
	
	
	public function delete()
	{ 
		$Register = Register::getInstance();
		$Register['DB']->delete('forums', array('id' => $this->id));
	}

}