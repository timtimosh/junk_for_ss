<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header('Content-Type: text/html; charset=utf-8');
// подключение файла конфигурации
require_once( 'configuration.php' );

// Соединяемся, выбираем базу данных
$link = mysql_connect($mosConfig_host, $mosConfig_user, $mosConfig_password)
    or die('Не удалось соединиться: ' . mysql_error());
mysql_set_charset('utf8',$link);
//echo 'Соединение успешно установлено';
mysql_select_db($mosConfig_db) or die('Не удалось выбрать базу данных');

// Выполняем SQL-запрос

$query = 'SELECT univer.ident as univer_id, univer.name as univer_name, univer.image, univer.thumbnail, univer.url, city.ident as city_id FROM `wa_university` univer LEFT JOIN `wa_university_city` city ON univer.city_ident = city.ident LEFT JOIN `wa_university_state` country ON city.ident = country.ident';
$r = mysql_query($query) or die('Запрос не удался: ' . mysql_error());

$i=0;

while ($univer= mysql_fetch_array($r)) {$i++;
    $list[$i]['Program ID']=$univer['univer_id'];
    $list[$i]['Location ID']=$univer['city_id'];
    $list[$i]['School name']=$univer['univer_name'];
    $list[$i]['Final URL']='http://www.univerpl.com.ua/uk/'.$univer['url'];
    $list[$i]['Location ID']=$univer['city_id'];
    $list[$i]['Image URL']='http://www.univerpl.com.ua/images/stories/wa_university/'.$univer['image'];
    $list[$i]['Thumbnail image URL']='http://www.univerpl.com.ua/images/stories/wa_university/'.$univer['thumbnail'];
    
}


$fp = fopen('/home/univerpl/univerpl.com.ua/www/datalayer.csv', 'w');
fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

// output the column headings
fputcsv($fp, array('Program ID', 'Location ID', 'School name', 'Final URL', 'Location ID', 'Image URL', 'Thumbnail image URL'));


foreach ($list as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);

echo 'csv был успешно сгенерирован';
