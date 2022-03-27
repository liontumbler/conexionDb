<?php
//mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

//se conecta a la db
$db = new db();

echo 'consulta test';
echo '<br>';

$usuario = $db->includeModelo('usuarios', __DIR__);
$log = $db->includeModelo('logs', __DIR__);

/*
consulta preparada
con array
$usuario->setCorreo(['lion_3214@hotmail.com', 'lion_3214@hotmail.com123']);
print_r($usuario->getCorreo());
$arr = $db->selectPre($usuario, 'correo = :correo');
$res = $db->Ejecutar($arr);

un solo valor
$usuario->setCorreo('lion_3214@hotmail.com');
$arr = $db->selectPre($usuario, 'correo = :correo');
$res = $db->Ejecutar($arr);

$usuario->setCorreo('lion_3214@hotmail.com');
$usuario->setActivo('0');
$arr = $db->selectPre($usuario, 'correo = :correo and activo = :activo');
$res = $db->Ejecutar($arr);

$usuario->setCorreo(['lion_3214@hotmail.com', 'lion_3214@hotmail.com123']);
$usuario->setActivo('1');
$arr = $db->selectPre($usuario, 'correo = :correo and activo = :activo');
print_r($usuario->getCorreo());
$res = $db->Ejecutar($arr);

//buscar sin preparar
$usuario->setCorreo('lion_3214@hotmail.com');
$arr = $db->select($usuario->getFrom(), 'correo = "'.$usuario->getCorreo().'"');
//$arr = $db->select('usuarios', 'correo = "lion_3214@hotmail.com"');

//buscar sin preparar
$usuario->setCorreo('lion_3214@hotmail.com');
$usuario->setActivo('1');
//$arr = $db->select($usuario->getFrom(), 'correo = "'.$usuario->getCorreo().'" and activo = "'.$usuario->getActivo().'"');
$arr = $db->select('usuarios', 'correo = "lion_3214@hotmail.com" and activo = "1"');
*/


/*
//eliminar
//$usuario->setId([2, 11]);
$usuario->setId(2);
$arr = $db->DeletePre($usuario, 'id = :id');
$res = $db->Ejecutar($arr);

//eliminar
$usuario->setId([12, 13]);
//$usuario->setId(2);
$usuario->setActivo('1');
$arr = $db->DeletePre($usuario, 'id = :id and activo = :activo');
$res = $db->Ejecutar($arr);

//sin preparar
$usuario->setId(2);
//$arr = $db->Delete($usuario->getFrom(), 'id = "'.$usuario->getId().'"');
$arr = $db->Delete('usuarios', 'id = "2"');

$usuario->setId(2);
$usuario->setActivo(1);
$arr = $db->Delete($usuario->getFrom(), 'id = "'.$usuario->getId().'" and activo = "'.$usuario->getActivo().'"');
//$arr = $db->Delete('usuarios', 'id = "2" and activo = "1"');
*/


/*
$usuario->setCorreo('creadp php');
$usuario->setContrasena('contrasena');
$usuario->setNickname('nickname');
$usuario->setNombres('nombres');
$usuario->setApellidos('apellidos');
$usuario->setNoDocumento('noDocumento');
$usuario->setActivo(1);
$usuario->setRol('1');
$usuario->setTipoDocumento('1');
$usuario->setCodigo('codigo');
//$usuario->setCargos('1');
//$usuario->setEmpresa(1);

$arr = $db->InsertPre($usuario);
$res = $db->Ejecutar($arr);

$usuario->setCorreo(['creadp php', 'creadp php2']);
$usuario->setContrasena(['contrasena', 'contrasena2']);
$usuario->setNickname(['nickname', 'nickname2']);
$usuario->setNombres(['nombres', 'nombres2']);
$usuario->setApellidos(['apellidos', 'apellidos2']);
$usuario->setNoDocumento(['noDocumento', 'noDocumento2']);
$usuario->setActivo(['1', '0']);
$usuario->setRol('1');
$usuario->setTipoDocumento(['1']);
$usuario->setCodigo(['codigoNew', NULL]);
//$usuario->setCargos('1');
//$usuario->setEmpresa(1);

$arr = $db->InsertPre($usuario);
$res = $db->Ejecutar($arr);

//solo para un valor
$usuario->setCorreo('creadp phpwwwwww');
$usuario->setContrasena('contrasena');
$usuario->setNickname('nickname');
$usuario->setNombres('nombres');
$usuario->setApellidos('apellidos');
$usuario->setNoDocumento('noDocumento');
$usuario->setActivo(1);
$usuario->setRol('1');
$usuario->setTipoDocumento('1');
$usuario->setCodigo('codigo');
//$usuario->setCargos('1');
//$usuario->setEmpresa(1);

//$arr = $db->Insert($usuario);
*/


/*
//se encarga de ejecutar una ves si los valores ya fueron cambiados
$usuario->setCorreo('actualizado2');
$usuario->setNickname('actualizado2');
$usuario->setId(4);
$arr = $db->UpdatePre($usuario, '`id` = :id');
$res = $db->Ejecutar($arr);

$usuario->setCorreo('actualizado3');
$usuario->setNickname('actualizado3');
$arr = $db->UpdatePre($usuario, '`contrasena` = "contrasena" and `nickname` = "nickname"');
$res = $db->Ejecutar($arr);

$usuario->setCorreo('lion_3214@hotmail.com.l..l.');
$usuario->setNickname('XXX');
$arr = $db->Update($usuario, 'id = "3"');
*/


/*
 * para ejecutar sql en forma de tansaccion hay que llamar primero un metodo y 
 * terminar con un metodo como se muestra en el siguiente ejemplo de insercion: asi tambien para todo el CRUD

$db->readyTransaction();

$log->setDescripcion('new7');
$log->setTitulo('titulo');
$arr2 = $db->InsertPre($log);
$res2 = $db->Ejecutar($arr2);

$usuario->setCorreo('new7');
$usuario->setContrasena('contrasena');
$usuario->setNickname('nickname');
$usuario->setNombres('nombres');
$usuario->setApellidos('apellidos');
$usuario->setNoDocumento('noDocumento');
$usuario->setActivo('1');
$usuario->setRol('1');
$usuario->setTipoDocumento('1');
$usuario->setCodigo('codigo');
$arr = $db->InsertPre($usuario);
$res = $db->Ejecutar($arr);

$db->endTransaction();
*/

echo '<br>';
echo '<br>';
echo '<br>';
var_dump($res);
echo '<br>';
echo '<br>';
var_dump($res2);

echo '<br>';
echo 'ARR';
var_dump($arr);
echo '<br>';
echo '<br>';
var_dump($arr2);

echo phpinfo();