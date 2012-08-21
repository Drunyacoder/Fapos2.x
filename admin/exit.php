<?php
include_once '../sys/boot.php';
include_once ROOT . '/admin/inc/adm_boot.php';


if (isset($_SESSION['adm_panel_authorize'])) unset($_SESSION['adm_panel_authorize']);
redirect('/');

?>
