<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.1                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    NewsComments Entity           |
| @copyright     ©Andrey Brykin 2010-2012      |
| @last mod      2012/02/28                    |
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
class NewsCommentsEntity extends FpsEntity
{
	
	protected $id;
	protected $entity_id;
	protected $user_id;
	protected $name;
	protected $message;
	protected $ip;
	protected $mail;
	protected $date;


	public function save()
	{
		$data = array(
			'id' => $this->id,
			'entity_id' => $this->entity_id,
			'user_id' => $this->user_id,
			'name' => $this->name,
			'message' => $this->message,
			'ip' => $this->ip,
			'mail' => $this->mail,
			'date' => $this->date,
		);
		
		$Register = Register::getInstance();
		$Register['DB']->save('news_comments', $data);
	}
	
	
	public function delete()
	{
		$Register = Register::getInstance();
		$Register['DB']->delete('news_comments', array('id' => $this->id));
	}
}