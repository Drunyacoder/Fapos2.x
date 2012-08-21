<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.0                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    ForumAttaches Entity          |
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
class ForumAttachesEntity extends FpsEntity
{
	
	protected $id;
	protected $post_id;
	protected $theme_id;
	protected $user_id;
	protected $attach_number;
	protected $filename;
	protected $size;
	protected $date;
	protected $is_image;
	
	
	
	public function save()
	{
		$params = array(
			'post_id' => $this->post_id,
			'theme_id ' => $this->theme_id ,
			'user_id' => $this->user_id,
			'attach_number' => $this->attach_number,
			'filename' => $this->filename,
			'size' => $this->size,
			'date' => $this->date,
			'is_image' => $this->is_image,
		);
		if ($this->id) $params['id'] = $this->id;
		$Register = Register::getInstance();
		$Register['DB']->save('forum_attaches', $params);
	}
	
	
	
	public function delete()
	{ 
		$Register = Register::getInstance();
		$Register['DB']->delete('forum_attaches', array('id' => $this->id));
	}

}