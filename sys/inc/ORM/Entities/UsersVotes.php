<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.0                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    UsersVotes Entity             |
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
class UsersVotesEntity extends FpsEntity
{
	
	protected $id;
	protected $from_user;
	protected $to_user;
	protected $comment;
	protected $date;
	protected $points;





    public function save()
    {
        $params = array(
            'from_user' => $this->from_user,
            'to_user' => $this->to_user,
            'comment' => $this->comment,
            'date' => new Expr($this->date),
            'points' => $this->points,
   
        );
        if ($this->id) $params['id'] = $this->id;
        $Register = Register::getInstance();
        $Register['DB']->save('users_votes', $params);
    }
	

	public function delete($id)
	{
        $Register = Register::getInstance();
        $Register['DB']->delete('users_votes', array('id' => $id));
	}
}