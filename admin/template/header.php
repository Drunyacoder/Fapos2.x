<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $pageTitle; ?></title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
	<script language="JavaScript" type="text/javascript" src="../sys/js/jquery.js"></script>
	
	<script language="JavaScript" type="text/javascript" src="../sys/js/jquery.validate.js"></script>
	<script language="JavaScript" type="text/javascript" src="../sys/js/jquery-ui-1.8.14.custom.min.js"></script>
	<script type="text/javascript" src="js/drunya.lib.js"></script>

	<script type="text/javascript" src="../sys/js/redactor/redactor.js"></script>
	<link type="text/css" rel="StyleSheet" href="../sys/js/redactor/css/redactor.css" />
	
	<link rel="StyleSheet" type="text/css" href="template/css/style.css" />
	
	
	<script type="text/javascript" src="../sys/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="../sys/js/jquery.hotkeys.js"></script>
	<script type="text/javascript" src="../sys/js/jstree/jstree.min.js"></script>
	<link type="text/css" rel="StyleSheet" href="../sys/js/jstree/_docs/syntax/!style.css" />
	<link type="text/css" rel="StyleSheet" href="../sys/js/jstree/_docs/!style.css" />
	<script type="text/javascript" src="../sys/js/jstree/_docs/syntax/!script.js"></script>
	
	<script type="text/javascript">

	$(document).ready(function(){
		setTimeout(function(){
			$('#overlay').height($('#wrapper').height());
		}, 2000);
		
		$('div.side-menu').height(($('body').height() - 35));
	});
	</script>
</head> 
<body>
	<div id="wrapper">
		<div class="headmenu">
			<div class="logo"></div>
			<div class="menu" id="topmenu">
				<ul>
					<li><a href="#">Общее</a></li>
					<li><a href="#">Плагины</a></li>
					<li><a href="#">Сниппеты</a></li>
					<li><a href="#">Дизайн</a></li>
					<li><a href="#">Статистика</a></li>
					<li><a href="#">Безопасность</a></li>
					<li><a href="#">Дополнительно</a></li>
					<li><a href="#">Помощь</a></li>
					<div class="clear"></div>
				</ul>
			</div>
			<div class="userbar">
				<?php
				$ava_path = (file_exists(ROOT . '/sys/avatars/' . $_SESSION['user']['id'] . '.jpg'))
				? WWW_ROOT . '/sys/avatars/' . $_SESSION['user']['id'] . '.jpg'
				:  WWW_ROOT . '/sys/img/noavatar.png';
				?>
				<div class="ava"><img src="<?php echo $ava_path; ?>" alt="user ava" title="user ava" /></div>
				<div class="name"><a href="#"><?php echo h($_SESSION['user']['name']); ?></a><span>Admin</span></div>
				<a href="#" class="exit"></a>
			</div>
			<div class="clear"></div>
		</div>
		
		<!-- AdminBar -->
		<script type="text/javascript">

		document.top_menu = new drunyaMenu([
		['<?php echo __('General'); ?>',
		  [
		  '<a href="/admin"><?php echo __('Main page'); ?></a>',
		  'sep',
		  '<span><?php echo __('Version of Fapos'); ?> [ <b><?php echo FPS_VERSION ?></b> ]</span>',
		  'sep',
		  '<a href="/admin/settings.php?m=sys"><?php echo __('Common settings'); ?></a>',
		  'sep',
		  '<a href="/admin/clean_cache.php"><?php echo __('Clean cache'); ?></a>'
		  ]],

		['<?php echo __('Plugins'); ?>',
		  [
		  '<a href="/admin/plugins.php"><?php echo __('List'); ?></a>'
		  //'sep',
		  //'<a href="/admin/chcreat.php?a=ed">Редактировать</a>'
		  ]],

		  
		  
		['<?php echo __('Snippets'); ?>',
		  [
		  '<a href="/admin/snippets.php"><?php echo __('Create'); ?></a>',
		  'sep',
		  '<a href="/admin/snippets.php?a=ed"><?php echo __('Edit'); ?></a>'
		  ]],

		  
		 
		['<?php echo __('Design'); ?>',
		  [
		  '<a href="design.php?d=default&t=main"><?php echo __('General design and css'); ?></a>',
		  'sep',
		  '<a href="menu_editor.php"><?php echo __('Menu editor'); ?></a>'
		  ]],
		  
		  
		['<?php echo __('Statistic'); ?>',
		  [
		  '<a href="/admin/statistic.php"><?php echo __('View'); ?></a>',
		  'sep',
		  '<a href="/admin/settings.php?m=statistics"><?php echo __('Settings of module'); ?></a>'
		  ]],



		['<?php echo __('Security'); ?>',
		  [
		  '<a href="settings.php?m=secure"><?php echo __('Security settings'); ?></a>',
		  'sep',
		  '<a href="system_log.php"><?php echo __('Action log'); ?></a>',
		  'sep',
		  '<a href="ip_ban.php"><?php echo __('Bann by IP'); ?></a>',
		  'sep',
		  '<a href="dump.php"><?php echo __('Backup controll'); ?></a>'
		  ]],
		  
		  
		['<?php echo __('Additional'); ?>',
		  [
		  '<a href="settings.php?m=hlu"><?php echo __('SEO settings'); ?></a>',
		  '<a href="settings.php?m=rss"><?php echo __('RSS settings'); ?></a>',
		  '<a href="settings.php?m=sitemap"><?php echo __('Sitemap settings'); ?></a>'
		  ]],
		  

		['<?php echo __('Help'); ?>',
		  [
		  '<a href="http://fapos.net" target="_blank"><?php echo __('Fapos CMS Comunity'); ?></a>',
		  '<a href="faq.php"><?php echo __('FAQ'); ?></a>',
		  'sep',
		  '<a href="authors.php"><?php echo __('Dev. Team'); ?></a>',
		  ]]
		]);

		</script>
		<!-- /AdminBar -->
		
		
		<div class="center-wrapper">
		
			<table class="side-separator" cellpadding="0" cellspacing="0" width="100%" height="100%" >
				<tr>
					<td width="237" height="100%">
						<div class="side-menu">
							<div class="search">
								<form>
									<div class="input"><input type="text" name="search" placeholder="Search..." /></div>
									<input class="submit-butt" type="submit" name="send" value="" />
								</form>
							</div>
							<ul>
							
							
							
							<?php
							$modsInstal = new FpsModuleInstaller;
							$nsmods = $modsInstal->checkNewModules();

							if (count($nsmods)):
								foreach ($nsmods as $mk => $mv):
							?>	
							
								<li>
									<div class="icon new-module"></div><a href="#"><?php echo $mk; ?></a>
									<div class="sub-opener" onClick="subMenu('sub<?php echo $mk; ?>')"></div>
									<div class="clear"></div>
									<div id="sub<?php echo $mk; ?>" class="sub">
										<div class="shadow">
											<ul>
												<li><a href="<?php echo WWW_ROOT; ?>/admin?install=<?php echo $mk ?>">Install</a></li>
											</ul>
										</div>
									</div>
								</li>
							<?php
								endforeach;
							endif;




							$modules = getAdmFrontMenuParams();

							foreach ($modules as $modKey => $modData): 
								if (!empty($nsmods) && array_key_exists($modKey, $nsmods)) continue;
							?>
							
								<li>
									<div class="icon <?php echo $modKey ?>"></div><a href="<?php echo $modData['url']; ?>"><?php echo $modData['ankor']; ?></a>
									<div class="sub-opener" onClick="subMenu('sub<?php echo $modKey ?>')"></div>
									<div class="clear"></div>
									<div id="sub<?php echo $modKey ?>" class="sub">
										<div class="shadow">
											<ul>
												<?php foreach ($modData['sub'] as $url => $ankor): ?>
												<li><a href="<?php echo $url; ?>"><?php echo $ankor; ?></a></li>
												<?php endforeach; ?>
											</ul>
										</div>
									</div>
								</li>
							<?php endforeach; ?>
							</ul>
							<div class="clear"></div>
						</div>
					</td>
					<td style="position:relative; padding-bottom:100px;">
						<div class="rcrumbs">
							<?php echo (!empty($pageNavr)) ? $pageNavr : ''; ?>
						</div>
						<div class="crumbs">
							<?php echo (!empty($pageNav)) ? $pageNav : ''; ?>
						</div>

						<div id="content-wrapper">






<?php /*
<!-- navi -->
<div class="topnav">
<div class="left"><?php echo $pageNav; ?><div class="right"><?php echo $pageNavl; ?></div></div>
</div>
<!-- /navi -->
*/ ?>










