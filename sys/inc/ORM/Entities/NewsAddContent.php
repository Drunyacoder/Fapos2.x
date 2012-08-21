<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.1                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    NewsAddContent Entity         |
| @copyright     ©Andrey Brykin 2010-2012      |
| @last mod      2012/03/02                    |
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
class NewsAddContentEntity extends FpsEntity
{
	
	protected $id;
	protected $field_id;
	protected $entity_id;
	protected $content;

	
	
	public function save()
	{
		$params = array(
			'id' => $this->id,
			'entity_id' => $this->entity_id,
			'field_id' => $this->field_id,
			'content' => $this->content,
		);
		$Register = Register::getInstance();
		$Register['DB']->save('news_add_content', $params, array('id' => $this->id));
	}
	
	
	
	public function delete()
	{
		$Register = Register::getInstance();
		$Register['DB']->delete('news_add_content', array('id' => $this->id));
	}

}