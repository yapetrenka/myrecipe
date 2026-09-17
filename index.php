<?
include("config.php");
$start_time = MyCMS::get_sec();
if(is_file(MyCMS::init('dir').'/index.php')){
  include(MyCMS::init('dir').'/index.php');
}else{
  MyCMS::fatalError('Ошибка, директория '.MyCMS::init('site_id').' не найдена.');
}
$exec_time = MyCMS::get_sec() - $start_time;
echo "\n<!-- Время формирования страницы на сервере: ".sprintf("%01.3f", $exec_time)." сек. -->";

?>