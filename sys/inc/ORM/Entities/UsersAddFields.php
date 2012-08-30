<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.1                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    UsersAddFields Entity         |
| @copyright     ©Andrey Brykin 2010-2012      |
| @last mod      2012/08/30                    |
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
class UsersAddFieldsEntity extends FpsEntity
{
	
	protected $id;
	protected $type;
	protected $name;
	protected $label;
	protected $size;
	protected $params;
	protected $content;



    /**
     * @param $content
     */
	public function setContent($content)
    {
        $this->content = $content;
    }



    /**
     * @return array
     */
    public function getContent()
   	{

        $this->checkProperty('content');
   		return $this->content;
   	}
}