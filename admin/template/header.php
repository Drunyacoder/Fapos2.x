<!DOCTYPE HTML>
<html>
<head>
	<title><?php echo $pageTitle; ?></title>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
	<link rel="icon" href="/sys/img/favicon.ico" type="image/x-icon">
	<link type="text/css" rel="StyleSheet" href="template/css/style.css" />
	<script language="JavaScript" type="text/javascript" src="../sys/js/jquery.js"></script>
	

	
	<script language="JavaScript" type="text/javascript" src="../sys/js/jquery.validate.js"></script>
	<script language="JavaScript" type="text/javascript" src="../sys/js/jquery-ui-1.8.14.custom.min.js"></script>
	<script type="text/javascript" src="js/drunya.lib.js"></script>

	<script type="text/javascript" src="../sys/js/redactor/redactor.js"></script>
	<link type="text/css" rel="StyleSheet" href="../sys/js/redactor/css/redactor.css" />
	
	
	<script type="text/javascript" src="../sys/js/jquery.cookie.js"></script>
	<script type="text/javascript" src="../sys/js/jquery.hotkeys.js"></script>
	<script type="text/javascript" src="../sys/js/jstree/jstree.min.js"></script>
	<link type="text/css" rel="StyleSheet" href="../sys/js/jstree/_docs/syntax/!style.css" />
	<link type="text/css" rel="StyleSheet" href="../sys/js/jstree/_docs/!style.css" />
	<script type="text/javascript" src="../sys/js/jstree/_docs/syntax/!script.js"></script>
	
	

</head>
            
<body>


<!-- AdminBar -->
<div id="puzadpnadm" align="left" class="topmenu">
<div class="cuzadpn" id="uzadpn" onmousedown=""><div id="uzadmp"></div>

<div class="pbarFiller" style="width:0%;"></div>
</div>
</div></div>

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
  '<a href="/admin/settings_statistic.php"><?php echo __('Settings of module'); ?></a>'
  ]],



['<?php echo __('Security'); ?>',
  [
  '<a href="secure_settings.php"><?php echo __('Security settings'); ?></a>',
  'sep',
  '<a href="system_log.php"><?php echo __('Action log'); ?></a>',
  'sep',
  '<a href="ip_ban.php"><?php echo __('Bann by IP'); ?></a>',
  'sep',
  '<a href="dump.php"><?php echo __('Backup controll'); ?></a>'
  ]],
  
  
['<?php echo __('Additional'); ?>',
  [
  '<a href="settings_seo.php"><?php echo __('SEO settings'); ?></a>',
  '<a href="settings_rss.php"><?php echo __('RSS settings'); ?></a>',
  '<a href="settings_sitemap.php"><?php echo __('Sitemap settings'); ?></a>'
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


<!-- navi -->
<div class="topnav">
<div class="left"><?php echo $pageNav; ?><div class="right"><?php echo $pageNavl; ?></div></div>
</div>
<!-- /navi -->



<!-- Body -->
<table id="mainConttainer" class="f-tb">
<tr>

<td class="f-td">
<div  class="front-block">

<div class="f-tl"><div class="f-tr"><div class="f-tc"></div></div></div>
<div class="f-cl"><div class="f-cr"><div class="f-cc"><div id="f-wrap">




<?php
$modsInstal = new FpsModuleInstaller;
$nsmods = $modsInstal->checkNewModules();

if (count($nsmods)):
	foreach ($nsmods as $mk => $mv):
?>	
	<div onmouseover="this.className='MmenuOver';" class="MmenuOut" onmouseout="this.className='MmenuOut'">
	<div class="fm-item" onmouseover="showSubMenu('mp<?php echo $mk ?>');" onClick="hideSubMenu('mp<?php echo $mk ?>');">
	<a class="f-link" href="#"><?php echo $mk; ?></a>
	</div>

	<div class="d_submenu" id="mp<?php echo $mk ?>" style="display:none;">
	<div class="fmsub-tl"><div class="fmsub-tr"><div class="fmsub-tc"></div></div></div>
	<div class="fmsub-cl"><div class="fmsub-cr"><div class="fmsub-cc">
	<div class="sbm"><a href="<?php echo WWW_ROOT; ?>/admin?install=<?php echo $mk ?>">Install</a></div>
	</div></div></div>
	<div class="fmsub-bl"><div class="fmsub-br"><div class="fmsub-bc"></div></div></div>
	</div>
	</div>
<?php
	endforeach;
endif;


$modules = getAdmFrontMenuParams();






foreach ($modules as $modKey => $modData): 
	if (!empty($nsmods) && array_key_exists($modKey, $nsmods)) continue;
?>
<div onmouseover="this.className='MmenuOver';" class="MmenuOut" onmouseout="this.className='MmenuOut'">
<div class="fm-item" onmouseover="showSubMenu('mp<?php echo $modKey ?>');" onClick="hideSubMenu('mp<?php echo $modKey ?>');">
<a class="f-link" href="<?php echo $modData['url']; ?>"><?php echo $modData['ankor']; ?></a>
</div>

<div class="d_submenu" id="mp<?php echo $modKey ?>" style="display:none;">
<div class="fmsub-tl"><div class="fmsub-tr"><div class="fmsub-tc"></div></div></div>
<div class="fmsub-cl"><div class="fmsub-cr"><div class="fmsub-cc">
    <?php foreach ($modData['sub'] as $url => $ankor): ?>
    <div class="sbm"><a href="<?php echo $url; ?>"><?php echo $ankor; ?></a></div>
    <?php endforeach; ?>
</div></div></div>
<div class="fmsub-bl"><div class="fmsub-br"><div class="fmsub-bc"></div></div></div>
</div>
</div>
<?php endforeach; ?>


</div></div>

</div></div><div class="f-bl"><div class="f-br"><div class="f-bc"></div></div></div>

      

<div align="center" style="padding:5px 0">
<img src="template/img/logo.png" />

</div>

</div></td><td class="container-td">
<div class="main-cont">


