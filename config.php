<?
#error_reporting(-1);
#ini_set('display_errors', true);
#ini_set('display_startup_errors', true);
header ("Content-Type: text/html;charset=utf-8"); 
@setlocale(LC_ALL, 'ru_RU.utf8', 'rus_RUS.utf8', 'Russian_Russia.utf8');
@setlocale (LC_NUMERIC, "en_US");



ini_set ("magic_quotes_gpc", "Off");
ini_set ("register_globals", "Off");

session_start();

$config = array(
    'debug'=>true,
    'root'=>$_SERVER['DOCUMENT_ROOT'], /* Полный путь к корневой папке */
    'language'=>array(
      //'ru'=>array('name'=>'Русская версия','default'=>true),
      //'en'=>array('name'=>'Английская версия'),
      //'de'=>array('name'=>'Немецкая версия'),
    ),                
    'db'=>array(/* Подключение к базе MySQL*/
        'host'=>'localhost',/* Сервер */
        'name'=>'c503029_myrecipe_na4u_ru',/* Имя базы*/
        'user'=>'c503029_myrecipe_na4u_ru',/* Пользователь */
        'password'=>'hU(3NPtVMepd+d7'/* Пароль */
    ),
    'framework'=>array(
      'admin'=>array('name'=>'Административная часть','dir'=>'admin'),
      'master'=>array('name'=>'Мастерская','dir'=>'master','password'=>'775577'),
      'site'=>array(
				'name'=>'Основной сайт',
				'dir'=>'site'
			),
    ),
    'config'=>array(
      /*Любые настройки*/
    )
);

include($config['root'].'/lib/MyCMS.php');
spl_autoload_register(array('MyCMS', '_autoload'));
MyCMS::run($config);
MyCMS::build('db',$config['db'],"DB");
MyCMS::start_system();

if((MyCMS::init('type')!="admin" && MyCMS::init('type')!="master") && count($config['framework'])>3){
	$sites = MyCMS::frameworks();
	foreach($sites as $k=>$v){
		$domain = explode(",",$v['domain']);
		$domain = array_map("trim",$domain);
		if(in_array(MyCMS::init('domain'),$domain)){
			MyCMS::initSet('site_id',$k);
			break;
		}
	}
	$siteID = MyCMS::init('site_id');
	if(empty($siteID)){
		reset($sites);
		MyCMS::initSet('site_id',key($sites));
	}     

	MyCMS::initSet('type',MyCMS::init('site_id'));
	MyCMS::initSet('dir',MyCMS::init('root').'/root/'.MyCMS::init('framework->'.MyCMS::init('site_id').'->dir'));
	
	unset($sites);
	unset($siteID);
	unset($v);
	unset($k);
}
unset($config);
?>