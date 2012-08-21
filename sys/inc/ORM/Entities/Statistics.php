<?php
/*---------------------------------------------\
|											   |
| @Author:       Andrey Brykin (Drunya)        |
| @Version:      1.2                           |
| @Project:      CMS                           |
| @package       CMS Fapos                     |
| @subpackege    Stat Entity                   |
| @copyright     ©Andrey Brykin 2010-2012      |
| @last mod      2012/06/04                    |
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
class StatisticsEntity extends FpsEntity
{
	
	protected $id;
	protected $ips;
	protected $cookie;
	protected $referer;
	protected $date;
	protected $views;
	protected $yandex_bot_views;
	protected $google_bot_views;
	protected $other_bot_views;
	protected $other_site_visits;
	
	
	
	public function save()
	{
		$params = array(
			'ips' => $this->ips,
			'cookie' => $this->cookie,
			'referer' => $this->referer,
			'date' => $this->date,
			'views' => $this->views,
			'yandex_bot_views' => $this->yandex_bot_views,
			'google_bot_views' => $this->google_bot_views,
			'other_bot_views' => $this->other_bot_views,
			'other_site_visits' => $this->other_site_visits,
		);
		if ($this->id) $params['id'] = $this->id;
		$Register = Register::getInstance();
		$Register['DB']->save('statistics', $params);
	}
	
	
	
	public function delete()
	{ 
		$Register = Register::getInstance();
		$Register['DB']->delete('statistics', array('id' => $this->id));
	}


}