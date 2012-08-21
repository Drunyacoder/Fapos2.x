<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.0                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    ForumCat Entity               |
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
class ForumCatEntity extends FpsEntity
{
	
	protected $id;
	protected $title;
	protected $previev_id;

	
	
	
	public function save()
	{
		$params = array(
			'title' => $this->title,
			'previev_id ' => $this->preview_id,
		);
		if ($this->id) $params['id'] = $this->id;
		$Register = Register::getInstance();
		$Register['DB']->save('forum_cat', $params);
	}
	
	
	
	public function delete()
	{ 
		$Register['DB']->delete('forum_cat', array('id' => $this->id));
	}

}