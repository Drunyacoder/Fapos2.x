<?php
##################################################
##												##
## @Author:       Andrey Brykin (Drunya)        ##
## @Version:      1.2                           ##
## @Project:      CMS                           ##
## @package       CMS Fapos                     ##
## @subpackege    Admin module                  ##
## @copyright     ©Andrey Brykin 2010-2011      ##
## @Last mod.     2012/02/08                    ##
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


header('Content-Type: text/html; charset=utf-8');





$FpsDB = $Register['DB']; //TODO
$ACL = $Register['ACL'];








if (ADM_REFER_PROTECTED == 1) {
	$script_name = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
	$script_name = strrchr($script_name, '/');
	if ($script_name != '/index.php') {
		$referer = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
		preg_match('#^http://([^/]+)#', $referer, $match);
		if (empty($match[1]) || $match[1] != $_SERVER['SERVER_NAME'])
			redirect('/admin/index.php');
	}
}


///if (empty($_SESSION['user'])) redirect('/');
if (!isset($_SESSION['adm_panel_authorize']) || $_SESSION['adm_panel_authorize'] < time()) {
	if (isset($_POST['send']) && isset($_POST['login']) && isset($_POST['passwd'])) {
		$errors = '';
		$login = strtolower(trim($_POST['login']));
		$pass = trim($_POST['passwd']);
		
		if (empty($login)) $errors .= '<li>Заполните поле "Логин"</li>';
		if (empty($pass)) $errors .= '<li>Заполните поле "Пароль"</li>';
		

		if (empty($errors)) {
			/*
			if ($login != strtolower($_SESSION['user']['name']) || md5($pass) != $_SESSION['user']['passw']) 
				$errors .= '<li>Не верный Пароль или Логин</li>';
			*/
			$user = $FpsDB->select('users', DB_FIRST, array('cond' => array('name' => $login, 'passw' => md5($pass))));
			if (!count($user)) {
				$errors .= '<li>Не верный Пароль или Логин</li>';
			} else {
				//turn access
				$ACL->turn(array('panel', 'entry'), true, $user[0]['status']);
			}
			
			if (empty($errors)) {
				$_SESSION['adm_panel_authorize'] = (time() + Config::read('session_time', 'secure'));
				redirect('/admin/');
			}
		}
	}


    $pageTitle = 'Авторизация в панели Администрирования';
    $pageNav = '';
    $pageNavl = '';
    include_once ROOT . '/admin/template/header.php';
?>
	<div class="all-wrap"></div>
	<script type="text/javascript">
	// background gemor
	document.body.style.height = '100%';
	document.body.style.overflow = 'hidden';
	</script>
	
	
	<div class="fps-win authorize" id="helpBox">
		<div class="title">Вход в панель Администрирования</div>
		<a href="../"><div class="close"></div></a>
		<div style="clear:both;"></div>
		<form method="POST" action="" >
			
			<div class="auth-form-img"><img src="/admin/template/img/protected_key.png" style="float:left; margin-right:10px;" /></div>
			<div class="auth-form">
				<?php 
				if (!empty($errors)) {
					echo '<ul class="error">' . $errors . '</ul>';
					unset($errors);
				}
				?>
				<div class="form-item">
					Логин:&nbsp;<input name="login" style="float:right;" type="text" value="" />
					<div style="clear:both;"></div>
				</div>
				<div class="form-item">
					Пароль:&nbsp;<input name="passwd" style="float:right;" type="password" value="" />
					<div style="clear:both;"></div>
				</div>
				<div class="form-item center" style="text-align:center;">
					<input type="submit" name="send" value="Авторизация" />
					<div style="clear:both;"></div>
				</div>
			</div>
			<div style="clear:both;"></div>
		</form>
	</div>

<?php	
	include_once 'template/footer.php';
	die();

	
	
} else if (!empty($_SESSION['adm_panel_authorize'])) {
	$_SESSION['adm_panel_authorize'] = (time() + Config::read('session_time', 'secure'));
}






if (!empty($_GET['install'])) {
	$instMod = (string)$_GET['install'];
	if (!empty($instMod) && preg_match('#^[a-z]+$#i', $instMod)) {
		$ModulesInstaller = new FpsModuleInstaller();
		$ModulesInstaller->installModule($instMod);
	}
}






function getAdmFrontMenuParams()
{
    $out = array();
    $modules = glob(ROOT . '/modules/*', GLOB_ONLYDIR);
    if (count($modules)) {
        foreach ($modules as $key => $modPath) {
            if (file_exists($modPath . '/info.php')) {
                include($modPath . '/info.php');
                if (isset($menuInfo)) {
                    $mod = basename($modPath);
                    $out[$mod] = $menuInfo;
                }
            }
        }
    }
    return $out;
}
?>