<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      1.3                            ##
## Project:      CMS                            ##
## package       CMS Fapos                      ##
## subpackege    ACL library                    ##
## copyright     ©Andrey Brykin 2010-2011       ##
## last mod.     2011/12/12                     ##
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

//rules and groups files



class ACL {

	private $rules;
	private $groups;


	public function __construct($path) {
        include_once $path . 'acl_rules.php';
        include_once $path . 'acl_groups.php';

		$this->rules = $acl_rules;
		$this->groups = $acl_groups;
	}


	public function turn($params, $redirect = true, $group = false) {
		if ($group === false) {
			$user_group = (!empty($_SESSION['user']['status'])) ? $_SESSION['user']['status'] : 0;
		} else {
			$user_group = (int)$group;
		}
		if (!isset($this->rules[$params[0]]) || !is_array($this->rules[$params[0]])) return false;
		switch (count($params)) {
			case 1:
				$access = (bool)in_array($user_group, $this->rules[$params[0]]);
				break;
			case 2:
				if (!empty($this->rules[$params[0]][$params[1]]) 
				&& is_array($this->rules[$params[0]][$params[1]])) {
					$access = (bool)in_array($user_group, $this->rules[$params[0]][$params[1]]);
				} else {
					$access = false;
				}
				break;
			default:
				$access = false;
				break;
		}
	
		if (empty($access) && $redirect) {
			redirect('/error.php?ac=403');
		} else {
			return $access;
		}
	}
	
	
	/**
	*
	*/
	public function save_rules($rules) {
		if ($fopen = fopen(ROOT . '/sys/settings/acl_rules.php', 'w')) {
			fputs($fopen, '<?php ' . "\n" . '$acl_rules = ' . var_export($rules, true) . "\n" . '?>');
			fclose($fopen);
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	*
	*/
	public function save_groups($groups) {
		if ($fopen=@fopen(ROOT . '/sys/settings/acl_groups.php', 'w')) {
			@fputs($fopen, '<?php ' . "\n" . '$acl_groups = ' . var_export($groups, true) . "\n" . '?>');
			@fclose($fopen);
			return true;
		} else {
			return false;
		}
	}
	
	
	public function getGroups()
	{
		$out= array();
		foreach ($this->groups as $k => $v) {
			$out[$k] = $v;
			$out[$k]['id'] = $k;
		}
		return $out;
	}
	
	
	/**
	* @return user group info
	*/
	public function get_group_info() {
		return $this->groups;
	}


    public function getRules()
    {
        return $this->rules;
    }
	
	
	/**
	* @param int $id - user group ID
	*
	* @return string group title
	*/
	public function get_user_group($id) {
		if (!empty($this->groups[$id])) return $this->groups[$id];
		return false;
	}
	
	
	
	/**
	 *
	 */
	static public function checkCategoryAccess($catAccessStr) {
		if ($catAccessStr === '') return true;
		$uid = (!empty($_SESSION['user']['status'])) ? intval($_SESSION['user']['status']) : 0;
		
		$accessAr = explode(',', $catAccessStr);
		if (count($accessAr) < 1) return true;
		foreach ($accessAr as $key => $groupId) {
			if ($groupId === $uid) {
				return false;
			}
		}
		return true;
	}

}
?>