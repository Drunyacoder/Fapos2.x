<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.0                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    UsersWarnings Entity          |
| @copyright     ©Andrey Brykin 2010-2012      |
| @last mod      2012/05/19                    |
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
class UsersEntity extends FpsEntity
{
	
	protected $id;
	protected $user_id;
	protected $admin_id;
	protected $cause;
	protected $date;
	protected $points;




    public function save()
    {
        $params = array(
            'user_id' => $this->user_id,
            'admin_id' => $this->admin_id,
            'cause' => $this->cause,
            'date' => new Expr($this->date),
            'points' => $this->points,
        );
        if ($this->id) $params['id'] = $this->id;
        $Register = Register::getInstance();
        $Register['DB']->save('users_warnings', $params);
    }
	


}