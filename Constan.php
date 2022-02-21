<?php 
/**
 * 
 * @version 1.0
 * @author Edwin Velasquez Jimnez
 * @link lion_3214@hotmail.com
 * aqui se ponen los logueos de ambiente real y otros datos importantes al momento de arrancar el desarrollo
 */

const PRUEBAS = true;

const HOST = 'localhost';
const TYPE_DB = 1; ////1->mysql,2->sqlserver,3->oracle,4->postgre

const USER_DB_TEST = 'edwin1';
const CONTRASENA_DB_TEST = '123.abc';
const NOMBRE_DB_TEST = 'db_tickets';

const USER_DB_DOMAIN = 'id15318054_edwin';
const CONTRASENA_DB_DOMAIN = '>Oczv$fDuhhFoFd6';
const NOMBRE_DB_DOMAIN = 'id15318054_tickets';

//coje si esta PRUEBAS true
const USER_DB = PRUEBAS ? USER_DB_TEST : USER_DB_DOMAIN;
const CONTRASENA_DB = PRUEBAS ? CONTRASENA_DB_TEST : CONTRASENA_DB_DOMAIN;
const NOMBRE_DB = PRUEBAS ? NOMBRE_DB_TEST : NOMBRE_DB_DOMAIN;