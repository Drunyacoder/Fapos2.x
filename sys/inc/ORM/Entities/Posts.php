<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.0                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    Posts Entity                  |
| @copyright     ©Andrey Brykin 2010-2012      |
| @last mod      2012/04/28                    |
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
class PostsEntity extends FpsEntity
{
	
	protected $id;
	protected $message;
	protected $attaches;
	protected $id_author;
	protected $time;
	protected $edittime;
	protected $id_editor;
	protected $id_theme;
	protected $locked = null;

	
	
	
	public function save()
	{
		$params = array(
			'message' => $this->message,
			'attaches' => $this->attaches,
			'id_author' => $this->id_author,
			'time' => $this->time,
			'edittime' => $this->edittime,
			'id_editor' => $this->id_editor,
			'id_theme' => $this->id_theme,
			'locked' => $this->locked,
		);
		if ($this->id) $params['id'] = $this->id;
		$Register = Register::getInstance();
		$Register['DB']->save('posts', $params);
	}
	
	
	
	public function delete()
	{ 
		$Register = Register::getInstance();
		$Register['DB']->delete('posts', array('id' => $this->id));
		
		
	}



    /**
     * @param $comments
     */
	public function setAttaches($attaches)
    {
        $this->attaches = $attaches;
    }



    /**
     * @return array
     */
    public function getAttaches()
   	{

        $this->checkProperty('attaches');
   		return $this->attaches;
   	}



    /**
     * @param $author
     */
    public function setAuthor($author)
   	{
   		$this->author = $author;
   	}



    /**
     * @return object
     */
	public function getAuthor()
	{
        if (!$this->checkProperty('author')) {
			
			if (!$this->getId_author()) {
                $Register = Register::getInstance();
				$this->author = $Register['ModManager']->getEntityInstance('users');
			
			
			} else {
				$Model = new PostsModel('posts');
				$this->author = $Model->getAuthorByEntity($this); // TODO (function is not exists)
			}
        }
		return $this->author;
	}

}