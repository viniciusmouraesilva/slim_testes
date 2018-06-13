<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

session_start();

require '../vendor/autoload.php';
require '../settings.php';

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$app = new \Slim\App(['settings' => $config]);

$container = $app->getContainer();
$container['renderer'] = new \Slim\Views\PhpRenderer('../templates');

$container['db'] = function ($c) {
	$db = $c['settings']['db'];
	
	try {
		$pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=". $db['dbname'] . ";charset=" . $db['charset'], $db['user'], $db['pass']);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $ex) {
		exit('No momento não estamos funcionando corretamente. :(');
	} catch (Exception $ex) {
		exit('No momento não estamos funcionando corretamente. :(');
	}
	
	return $pdo;
};

$container['logger'] = function ($c) {
	$logger = new \Monolog\Logger('financas_log');
	$fileHandler = new \Monolog\Handler\StreamHandler('../log/financas.log');
	$logger->pushHandler($fileHandler);
	return $logger;
};

$container['postandgetdatareceipt'] = function ($c) {
	$postandgetdatareceipt = new Vinicinho052\PostAndGetDataReceipt\Receipt();
	return $postandgetdatareceipt;
};

$app->get('/', function (Request $request, Response $response, $args) {
	return $this->renderer->render($response, '/login.php', $args);
});

$app->post('/', function (Request $request, Response $response) {
	$this->postandgetdatareceipt->setDataAndVerify('email|senha');
	$dados = print_r($this->postandgetdatareceipt->getData());
	$login = new Login();
	#$resu = $login->verificarLogin($dados);
	return $this->renderer->render($response, '/login.php');	
});

$app->get('/teste', function (Request $request, Response $response) {
        $this->postandgetdatareceipt->setDataAndVerify('email|senha');
        $dados = print_r($this->postandgetdatareceipt->getData());
        #$login = new Login();
        #$resu = $login->verificarLogin($dados);
        return $this->renderer->render($response, '/teste.php');        
});

$app->run();
