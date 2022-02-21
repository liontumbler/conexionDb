<?php
include 'db.php';
class Usuario
{
    private $from = 'usuarios';

    public $id;
    public $correo;
    public $contrasena;
    public $nickname;
    public $nombres;
    public $apellidos;
    public $noDocumento;
    public $activo;
    public $codigo;
    public $cargos;
    public $empresa;
    public $rol;
    public $tipoDocumento;

    public function __construct($id = 0){
        $this->id = $id;
    }

    public function GetFrom()
    {
        return $this->from;
    }

    public function Crear($correo = null, $contrasena = null, $nickname = null, $nombres = null, $apellidos = null, $noDocumento = null, $activo = null, $codigo = null, $rol = null, $tipoDocumento = null, $cargos = null, $empresa = null)
    {
        $this->correo = $correo;
        $this->contrasena = $contrasena;
        $this->nickname = $nickname;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->noDocumento = $noDocumento;
        $this->activo = $activo;
        $this->codigo = $codigo;
        $this->cargos = $cargos;
        $this->empresa = $empresa;
        $this->rol = $rol;
        $this->tipoDocumento = $tipoDocumento;
    }
    // ....Código de la clase....
}



echo 'consulta test';
echo '<br>';

//se conecta a la db
$db = new DB();

//////////////////////////////////////////////////////////////////

//buscar sin inyeccion
//$usuario = new Usuario();
//$usuario->correo = 'lion_3214@hotmail.com';
//$usuario->correo = ['lion_3214@hotmail.com', 'lion_3214@hotmail.com123'];
//$arr = $db->SelectPre($usuario, 'correo = :correo');

//buscar con inyeccion
//$usuario = new Usuario();
//$usuario->correo = 'lion_3214@hotmail.com';
//$arr = $db->Select($usuario->GetFrom(), 'correo = "'.$usuario->correo.'"');
//$arr = $db->Select('usuarios', 'correo = "lion_3214@hotmail.com"');

//////////////////////////////////////////////////////////////////

//eliminar
//$usuario = new Usuario([10, 11, 12, 13, 14]);
//$usuario = new Usuario(2);
//$arr = $db->DeletePre($usuario, 'id = :id');

//$usuario = new Usuario(2);
//$arr = $db->Delete($usuario->GetFrom(), 'id = "'.$usuario->id.'"');
//$arr = $db->Delete('usuarios', 'id = "2"');

//////////////////////////////////////////////////////////////////

//insertar
//$usuario = new Usuario();
//$usuario->Crear('correo2', 'contrasena', 'nickname', 'nombres', 'apellidos', 'noDocumento', '1', 'codigo', '1', '1');
//$arr = $db->InsertPre($usuario, ':correo, :contrasena, :nickname, :nombres, :apellidos, :noDocumento, :activo, :codigo, :rol, :tipoDocumento');//, :cargos, :empresa,


//$usuario = new Usuario();
//$usuario->Crear('correo2', 'contrasena', 'nickname', 'nombres', 'apellidos', 'noDocumento', '1', 'codigo', '1', '1');
//$arr = $db->Insert($usuario->GetFrom(), 'correo, contrasena, nickname, nombres, apellidos, noDocumento, activo, codigo, rol, tipoDocumento', '"'.$usuario->correo.'","'.$usuario->contrasena.'","'.$usuario->nickname.'","'.$usuario->nombres.'","'.$usuario->apellidos.'","'.$usuario->noDocumento.'","'.$usuario->activo.'","'.$usuario->codigo.'","'.$usuario->rol.'","'.$usuario->tipoDocumento.'"');
//$arr = $db->Insert('usuarios', 'correo, contrasena, nickname, nombres, apellidos, noDocumento, activo, codigo, rol, tipoDocumento', '"correo2","contrasena123","nickname","nombres","apellidos","noDocumento","1","codigo123","1","1"');

//////////////////////////////////////////////////////////////////

//actualizar
//$usuario = new Usuario(2);
//$usuario->correo = 'lion_3214@hotmail.com123';
//$usuario->nickname = 'actualizado';
//$arr = $db->UpdatePre($usuario, '`correo` = :correo, `nickname` = :nickname', '`id` = :id');

//$usuario = new Usuario(3);
//$usuario->correo = 'lion_3214@hotmail.com.l..l.';
//$usuario->nickname = 'actualizado';
//$arr = $db->Update($usuario->GetFrom(), 'correo = "'.$usuario->correo.'", nickname = "'.$usuario->nickname.'"', 'id = "'.$usuario->id.'"');
//$arr = $db->Update('usuarios', 'correo = "lion_3214@hotmail.com123", nickname = "actualizado123"', 'id = "3"');

//////////////////////////////////////////////////////////////////

//sin inyeccion
//$res = $db->Ejecutar($arr);

var_dump($arr);
echo '<br>';
var_dump($res);
echo '<br>';
echo $res['contrasena'];
echo $arr['contrasena'];