<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


$from = '
/
/articles
/articles/calendar-layout.html
/articles/chto-takoe-broshjura.html
/articles/chto-takoe-buklet.html
/articles/chto-takoe-flaer-i-zachem-on-nuzhen.html
/articles/kak-sozdat-broshjuru.html
/articles/lure-on-live-bait-in-the-advertising-world.html
/articles/poligrafiya.html
/articles/what-is-digital-printing.html
/content/contacts
/content/sitemap
/prices/bigovka-falcovka-perforaciya.html
/prices/bw-print.html
/prices/color-print.html
/prices/corner-rounding.html
/prices/digital-print.html
/prices/lamination.html
/prices/pereplet.html
/prices/plotternaya-porezka.html
/prices/post-press.html
/production/badges.html
/production/blanks.html
/production/booklets.html
/production/brochures.html
/production/business-cards.html
/production/calendars.html
/production/cards.html
/production/certificates.html
/production/envelopes.html
/production/flyers.html
/production/leaflets.html
/production/menu.html
/production/notepads.html
/production/plastic-cards.html
/production/posters.html
/production/presentations.html
/production/stickers.html';

$to = '
http://hotprint.ua/
http://hotprint.ua/articles
http://hotprint.ua/articles/calendar-layout.html
http://hotprint.ua/articles/chto-takoe-broshjura.html
http://hotprint.ua/articles/chto-takoe-buklet.html
http://hotprint.ua/articles/chto-takoe-flaer-i-zachem-on-nuzhen.html
http://hotprint.ua/articles/kak-sozdat-broshjuru.html
http://hotprint.ua/articles/lure-on-live-bait-in-the-advertising-world.html
http://hotprint.ua/articles/poligrafiya.html
http://hotprint.ua/articles/what-is-digital-printing.html
http://hotprint.ua/content/contacts
http://hotprint.ua/content/sitemap
http://hotprint.ua/prices/bigovka-falcovka-perforaciya.html
http://hotprint.ua/prices/bw-print.html
http://hotprint.ua/prices/color-print.html
http://hotprint.ua/prices/corner-rounding.html
http://hotprint.ua/prices/digital-print.html
http://hotprint.ua/prices/lamination.html
http://hotprint.ua/prices/pereplet.html
http://hotprint.ua/prices/plotternaya-porezka.html
http://hotprint.ua/prices/post-press.html
http://hotprint.ua/production/badges.html
http://hotprint.ua/production/blanks.html
http://hotprint.ua/production/booklets.html
http://hotprint.ua/production/brochures.html
http://hotprint.ua/production/business-cards.html
http://hotprint.ua/production/calendars.html
http://hotprint.ua/production/cards.html
http://hotprint.ua/production/certificates.html
http://hotprint.ua/production/envelopes.html
http://hotprint.ua/production/flyers.html
http://hotprint.ua/production/leaflets.html
http://hotprint.ua/production/menu.html
http://hotprint.ua/production/notepads.html
http://hotprint.ua/production/plastic-cards.html
http://hotprint.ua/production/posters.html
http://hotprint.ua/production/presentations.html
http://hotprint.ua/production/stickers.html';

$fromList = explode(PHP_EOL, $from);
$toList = explode(PHP_EOL, $to);

$str='';
foreach($fromList as $key => $from){
	
	$str.="'{$from}'=>'{$toList[$key]}',<br>";

}
echo $str;


 
