<?php
/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       $Date:$ Nov-2015
 * @Version:    $Rev:$ 1.1
 * @Developer:  Federico Guzman (federicoguzman@gmail.com)
 * @Modified:   $Date:$ MAY-2021
 * @Developer:  Wladimir Perez (tropaguararia28@gmail.com)
 **/

/* Los headers permiten acceso desde otro dominio (CORS) a nuestro REST API o desde un cliente remoto via HTTP
 * Removiendo las lineas header() limitamos el acceso a nuestro RESTfull API a el mismo dominio
 * Nótese los métodos permitidos en Access-Control-Allow-Methods. Esto nos permite limitar los métodos de consulta a nuestro RESTfull API
 * Mas información: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
 **/
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"'); 

/********************** Importaciones de scripts necesarios ********************************
*   errorDev.php    script para mostrar errores de php en el explorador o cliente api.
*   auth.php        script para autenticar a los usuarios con acceso a la api.
*   Slim.php        libreria encargada de manejar las rutas o endpoint.
*   Da*.php         acceso a datos encargados de realizar los CRUD por cada tabla de la BBDD        
**/
include_once '../helpers/error.php';
include_once '../security/auth.php';
require '../libs/Slim/Slim.php'; 
include_once '../class/DaBalones.php';
include_once '../class/DaClientes.php';

/** inicializando la libreria Slim **/
\Slim\Slim::registerAutoloader(); 
$app = new \Slim\Slim();

$app->get('/', function() use ($app){$app->redirect('../', 301);});

/********* Controlador de autenticacion *********************/
/* Usando POST para autenticar usuario */
$app->post('/auth', function() use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = Auth::login($param);
    echoResponse($response);
});

/********* Controlador para balones *************************/
/* Usando GET para traer todos los balones */
$app->get('/balones', 'authenticate', function() {    
    $response = DaBalones::getAll();
    echoResponse($response);  
});

/* Usando GET con parametro para traer el registro de un balones */
$app->get('/balones/:id', 'authenticate', function ($id) {
    $response = DaBalones::getById($id);
    echoResponse($response);
});

/* Usando POST para crear un balones */
$app->post('/balones', 'authenticate', function() use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaBalones::save($param);
    echoResponse($response);
});

/* Usando PUT para actualizar un balones */
$app->put('/balones/:id', 'authenticate', function($id) use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaBalones::update($id, $param);
    echoResponse($response);
});

/* Usando DELETE para eliminar un registro de un balon */
$app->delete('/balones/:id', 'authenticate', function ($id) {
    $response = DaBalones::delete($id);
    echoResponse($response);
});

/********* controlador para clientes **********************************/
$app->get('/clientes', 'authenticate', function() {    
    $response = DaClientes::getAll();
    echoResponse($response);  
});

$app->get('/clientes/:id', 'authenticate', function ($id) {
    $response = DaClientes::getById($id);
    echoResponse($response);
});

$app->post('/clientes', 'authenticate', function() use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaClientes::save($param);
    echoResponse($response);
});

$app->put('/clientes/:id', 'authenticate', function($id) use ($app) {
    $param = $app->request()->getBody();
    $param = json_decode($param, true);
    $response = DaClientes::update($id, $param);
    echoResponse($response);
});

$app->delete('/clientes/:id', 'authenticate', function ($id) {
    $response = DaClientes::delete($id);
    echoResponse($response);
});

/********* controlador para Usuarios **********************************/


/********* controlador para Movimientos *******************************/


/********* controlador para pagos *************************************/


/* corremos la aplicación */
$app->run();

/**
 * Mostrando la respuesta en formato json al cliente o navegador
 */
function echoResponse($response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($response["status_id"]);
    // setting response content type to json
    $app->contentType('application/json'); 
    echo json_encode($response);
}
?>
