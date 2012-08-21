<?php





$menuInfo = array(
    'url' => 'settings.php?m=statistic',
    'ankor' => 'Статистика',
	'sub' => array(
        'settings.php?m=statistic' => 'Настройки',
        'statistic.php' => 'Просмотр статистики',
	),
);







$settingsInfo = array(
    'active' => array(
        'type' => 'checkbox',
        'title' => 'Статус',
        'checked' => '1',
        'value' => '1',
        'description' => '(Активирован/Деактивирован)',
    ),
);