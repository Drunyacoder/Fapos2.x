<?php

##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      1.6.5                          ##
## Project:      CMS                            ##
## package       CMS Fapos                      ##
## subpackege    Document parser library        ##
## copyright     ©Andrey Brykin 2010-2012       ##
## last mod.     2012/09/17                     ##
##################################################


##################################################
##												##
## any partial or not partial extension         ##
## CMS Fapos,without the consent of the         ##
## author, is illegal                           ##
##################################################
## Любое распространение                        ##
## CMS Fapos или ее частей,                     ##
## без согласия автора, является не законным    ##
##################################################

/**
* Document parser
*
* Parse pages and data. Replaced chanks, snippets.
* Quote/unquote global tags.
*
* @author        Andrey Brykin
* @package       CMS Fapos
* @subpackage    Document parser
* @link          http://fapos.net
*/
class Document_Parser {

	/**
	 * @var object
	 */
	private $Cache;
	
	/**
	 * @var string 
	 */
	public $templateDir;

    /**
     * @var bool|int
     */
	private static $levels = false;

    /**
     * @var int
     */
    private $maxLevels = 3;

    /**
     * @var object
     */
    private $Register;

    /**
     * @var array
     */
    private $markes = array();



	/**
	 *
	 */
	public function __construct()
    {
        $this->Register = Register::getInstance();
		$this->Cache = new Cache;
		$this->Cache->prefix = 'block';
		$this->Cache->cacheDir = ROOT . '/sys/cache/blocks/';
		$this->Cache->lifeTime = 3600;
	}
	

    /**
     * @param $message
     * @return mixed|string
     */
    public function getPreview($message)
    {
        $outputContent = '';
		
		if (!empty($_SESSION['viewMessage'])) {
			$viewer = new Fps_Viewer_Manager;
			$context = array(
				'message' => $this->Register['PrintText']->print_page($message),
			);
			$outputContent = $viewer->view('previewmessage.html', $context);
		}
        return $outputContent;
    }


    /**
     * @return mixed|string
     */
    public function getErrors()
    {
		$viewer = new Fps_Viewer_Manager;
        $outputContent = '';
        if (!empty($_SESSION['FpsForm']['error'])) {
            $outputContent = $viewer->view('infomessage.html', array('info_message' => $_SESSION['FpsForm']['error']));
        }
        return $outputContent;
    }


	/**
	* @param       string $page
	* @return      data with parsed snippets
	*/
	public function parseSnippet($page)
    {
		$Register = Register::getInstance();
        $FpsDB = $Register['DB'];

        $tpl = preg_match_all('#\{\[([!]*)(\w+)\]\}#U', $page, $mas);
        for ($i= 0; $i < count($mas[2]); $i++) {
			$cached = true;
			$block_name = $mas[2][$i];
			if ($mas[1][$i] === '!') $cached = false;

			// Check cache
			if ($cached === true) {
				$cache_key = 'snippet_' . strtolower($block_name);
				$cache_key .= (!empty($_SESSION['user']['status'])) ? '_' . $_SESSION['user']['status'] : '_guest';
				
				if ($this->Cache->check($cache_key)) {
					$res = $this->Cache->read($cache_key);
					$page = str_replace($mas[0][$i], $res, $page);
					continue;
				}
			}
			
			
			// If no cache
			$sql = $FpsDB->select('snippets', DB_FIRST, array('cond' => array('name' => strtolower($block_name))));
			if (empty($sql[0])) continue;
            $limit = $sql[0];
			
			if (strtolower($block_name) == strtolower($limit['name'])) {
				ob_start();
				$str = eval($limit['body']);
				$res = ob_get_contents();
				ob_end_clean();
				$page = str_replace($mas[0][$i], $res, $page); 

				if ($cached === true) 
					$this->Cache->write($res, $cache_key, array());
			}
	    }
		return $page;
	}
	
		
	/**
	* @param       string $page
	* @return      data with parsed global tags
	*/
	public function getGlobalMarkers($page = '')
    {
        $Register = Register::getInstance();
		$markets = array();
		
		$markets['fps_wday'] = date("D");
		$markets['fps_date'] = date("d-m-Y");
		$markets['fps_time'] = date("H:i");
		$markets['fps_year'] = date("Y");
		
		$markets['powered_by'] = 'Fapos';
		$markets['site_title'] = Config::read('site_title');
		
		if (isset($_SESSION['user']) && isset($_SESSION['user']['name'])) {
			$markets['personal_page_link'] = get_url('/users/info/' . $_SESSION['user']['id']);
			$markets['fps_user_name'] = $_SESSION['user']['name'];
			$userGroup = $Register['ACL']->get_user_group($_SESSION['user']['status']);
			$markets['fps_user_group'] = $userGroup['title'];
		} else {
			$markets['personal_page_link'] = get_url('/users/add_form/');
			$markets['fps_user_name'] = 'Гость'; //TODO
			$markets['fps_user_group'] = 'Гости';
		}
		
		
		$markets['fps_admin_access'] = ($Register['ACL']->turn(array('panel', 'entry'), false)) ? '1' : '0';
		$markets['fps_user_id'] = (!empty($_SESSION['user']['id'])) ? $_SESSION['user']['id'] : 0;
		
		
		$online = getWhoOnline();
		$markets['all_online'] = ($online['users'] + $online['guests']);
		$markets['users_online'] = $online['users'];
		$markets['guests_online'] = $online['guests'];
		$markets['online_users_list'] = (!empty($_SESSION['online_users_list'])) ? $_SESSION['online_users_list'] : '';
		$markets['count_users'] = getAllUsersCount();
		
		$overal_stats = getOveralStat();
		$markets['max_online_all_time'] = (!empty($overal_stats['max_users_online'])) 
		? intval($overal_stats['max_users_online']) : 0;
		$markets['max_online_all_time_date'] = (!empty($overal_stats['max_users_online_date'])) 
		? h($overal_stats['max_users_online_date']) : 'Uncnown';
		
	
		if (strstr($page, '{{ fps_chat }}')) {
			include_once ROOT . '/modules/chat/index.php';
			$chat_link = get_url('/chat/view_messages/');
			$markets['fps_chat'] = '<iframe id="fpsChat" src="' . $chat_link 
			. '" width="100%" height="400" style="overflow:auto; margin:0px; padding:0px; border:none;"></iframe>';
			$markets['fps_chat'] .= ChatModule::add_form();
		}
		
		
		$markets['counter'] = get_url('/sys/img/counter.png');
		$markets['template_path'] = get_url('/template/' . Config::read('template'));
		$markets['www_root'] = WWW_ROOT;
		
		
		$markets['fps_rss'] = $this->getRss();
		
		if (false !== (strpos($page, '{{ mainmenu }}'))) {
			$markets['mainmenu'] = $this->builMainMenu();
		}
		
		// today borned users
		$today_born = getBornTodayUsers();
		$tbout = '';
		if (count($today_born) > 0) {
			$names = array();
			foreach ($today_born as $user) {
				$names[] = get_link($user['name'], '/users/info/' . $user['id']);
			}
			$tbout = implode(', ', $names);
		}
		$markets['today_born_users'] = (!empty($tbout)) ? $tbout : __('No today born users');
		
		return 	$markets;
	}
	
	
	/**
	* @return     list with RSS links
	*/
	public function getRss()
    {
		$rss = '';
		if (Config::read('rss_news', 'common')) {
			$rss .= get_img('/sys/img/rss_icon_mini.png') . get_link(__('News RSS'), '/news/rss/') . '<br />';
		}
		if (Config::read('rss_stat', 'common')) {
			$rss .= get_img('/sys/img/rss_icon_mini.png') . get_link(__('Stat RSS'), '/stat/rss/') . '<br />';
		}
		if (Config::read('rss_loads', 'common')) {
			$rss .= get_img('/sys/img/rss_icon_mini.png') . get_link(__('Loads RSS'), '/loads/rss/') . '<br />';
		}
		
		return $rss;
	}
	
	
	/** DEPRECATED
	* @param      string $page
	* @param      string $modul - current module
	* @return     data with head menu
	*/
	public function headMenu($page, $modul=NULL)
    {
        $Register = Register::getInstance();
		$this->ACL = $this->Register['ACL'];
		
		$menu = get_link('Главная', '/');
		if(isset($_SESSION['user']['name'])) {
			$menu = $menu . get_link('Мой профиль', '/users/info/' . $_SESSION['user']['id']) 
			. get_link('Выход', '/users/logout/');
			$menu = $menu . get_link('Пользователи', '/users/index/');
			if ($modul == 'forum') {
			    $menu = $menu . get_link('Поиск', '/search/');
			}
			
			// Есть ли непрочитанные сообщения в папке "Входящие"?
			$cntNewMsg = UserAuth::countNewMessages();
			if ( $cntNewMsg < 1 ) {
				$menu = $menu . get_link('Личные&nbsp;сообщения', '/users/in_msg_box/');
			} else {
				$menu = $menu . get_link('Новые&nbsp;сообщения', '/users/in_msg_box/', array('class' => 'newMessages'));
			}
		} else {
			$menu = $menu . get_link('Регистрация', '/users/add_form/') . get_link('Вход', '/users/login_form/');
		}		
		
		if ( isset( $_SESSION['user']['name'] ) and $this->ACL->turn(array('panel', 'entry'), false)) {
		$menu = $menu . get_link('Админка', '/admin/', array('target' => '_blank'));
	    }
		
		$menu .= '<a onClick="add_favorite(this);" title="Добавить в закладки" href="javascript:void(0);" >В закладки</a>';
		
		$html = str_replace('{headmenu}', $menu, $page);
	
		return $html;
	}
	
	
	/**
     * @return string
     *
	 * Build menu which creating in Admin Panel
	 */
	public function builMainMenu()
    {
		$menu_conf_file = ROOT . '/sys/settings/menu.dat';	
		if (!file_exists($menu_conf_file)) return false;
		$menudata = unserialize(file_get_contents($menu_conf_file));
	
		
		if (!empty($menudata) && count($menudata) > 0) {
			$out = $this->buildMenuNode($menudata, 'class="fpsMainMenu"');
		} else {
			return false;
		}
		return $out;
	}


    /**
     * @param  $node
     * @param string $class
     * @return string
     */
	public function buildMenuNode($node, $class = 'class="fpsMainMenu"')
    {
		$out = '<ul ' . $class . '>';
		foreach ($node as $point) {
			if (empty($point['title']) || empty($point['url'])) continue;
			$out .= '<li>';
			
			
			$out .= $point['prefix'];
			$target = (!empty($point['newwin'])) ? ' target="_blank"' : '';
			$out .= '<a href="' . $point['url'] . '"' . $target . '>' . $point['title'] . '</a>';
			$out .= $point['sufix'];
			
			if (!empty($point['sub']) && count($point['sub']) > 0) {
				$out .= $this->buildMenuNode($point['sub']);
			}
			
			$out .= '</li>';
		}
		$out .= '</ul>';
		return $out;
	}
	
}

