<?php
error_reporting(0);
header('Content-Type:text/html;charset=gbk');
//define('TEXT_PATH','D:\AppServ\www\BlindFeedback\SogouC.reduced.20061127\Reduced\C000010');
//define('TEXT_PATH','D:\AppServ\www\BlindFeedback\SogouC.mini.20061127\SogouC.mini\Sample\C000010');
define('ROOT_PATH',dirname(__FILE__));
define('TEXT_PATH', ROOT_PATH.'\C000010');
//йЩ╬щ©БеДжц
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS',123);
define('DB_NAME','feedback');

//╥жрЁеДжц
define('PAGE_SIZE',10);

require_once 'include/DB.class.php';
require_once 'include/func.inc.php';
require_once 'include/Tool.class.php';

define('TEXT_COUNT',count(Tool::fileDir(TEXT_PATH)));

?>