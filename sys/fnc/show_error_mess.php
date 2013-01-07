<?php

##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      0.7                            ##
## Project:      CMS                            ##
## package       CMS Fapos                      ##
## subpackege    Show errors messages function  ##
## copyright     ©Andrey Brykin 2010-2011       ##
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

// Вспомогательная функция - выдает сообщение об ошибке
// и делает редирект на нужную страницу с задержкой
function showErrorMessage( $message = '', $error = '', $redirect = false, $queryString = '' ) {
	
	if ($redirect === true) {
		//if (!empty($queryString)) $queryString = '?'.$queryString;
		header('Refresh: ' . Config::read('redirect_delay') . '; url=http://' . $_SERVER['SERVER_NAME'] . get_url($queryString));
	}
	$html = file_get_contents(ROOT . '/template/' . Config::read('template') . '/html/default/infomessage.html');
	$html = str_replace('{INFO_MESSAGE}', $message, $html );
	if (Config::read('debug_mode')) {
		$tpl = file_get_contents(ROOT . '/template/' . Config::read('template') . '/html/default/errormessage.html');
		$tpl = str_replace('{ERROR_MESSAGE}', $error, $tpl );
		$html = $html . $tpl."\n";
	}
	//$template = file_get_contents(ROOT . '/template/' . Config::read('template') . '/html/default/main.html');
	//$template = str_replace('{CONTENT}', $html, $template);
	//$html = preg_replace('#\{.*\}|\{\[.*\]\}#U', '', $template);
	echo $html;
}

?>