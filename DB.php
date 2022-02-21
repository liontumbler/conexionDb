<?php 
/**
 * 
 * @version 1.0
 * @author Edwin Velasquez Jimnez
 * @link lion_3214@hotmail.com
 * 
 */
include 'Constan.php';
class DB
{
    private $conexion;
    private $transaction = false;
    private $prepare = '';

    public function __construct($nombre_db = NOMBRE_DB, $usuario_db = USER_DB, $contrasena_db = CONTRASENA_DB, $host = HOST, $type_db = TYPE_DB)
	{
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            switch ($type_db) {
                case '1':
                    $this->conexion = new PDO('mysql:host='.$host.';dbname='.$nombre_db, $usuario_db, $contrasena_db, $options);
                    break;
                case '2':
                    $this->conexion = new PDO('sqlsrv:Server='.$host.';Database='.$nombre_db, $usuario_db, $contrasena_db, $options);
                    break;
                case '3':
                    $this->conexion = new PDO('oci:dbname='.$nombre_db, $usuario_db, $contrasena_db, $options);
                    break;
                case '4':
                    $this->conexion = new PDO('pgsql:dbname='.$nombre_db.' host='.$host, $usuario_db, $contrasena_db, $options);
                    break;
                default:
                    throw new Exception('no se definio el tipo de conexion de DB');
                    break;
            }

            $this->conexion->exec('SET NAMES utf8');
        } catch (PDOException $e){ 
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
	}

    private function EnviarArray($a)
    {   
        //var_dump($a);
        if(count($a) === 1)
            return $a[0];
        else
            return $a;
    }

    private function ArmarArray($objeto, $query)
    {
        $array = [];
        foreach($objeto as $clave => $valor){
            
            $pos = strpos($query, ':'.$clave);
            if ($pos === false) {
            }else{
                if (is_array($clave)) {
                    foreach($clave as $v){
                        $array += [$clave => $v];
                    }
                }else{
                    if ($valor != '') 
                        $array += [$clave => $valor];
                    else
                        throw new Exception('Error al preparar el array, porque alguno de los campos suministrados está vacío'); 
                }
            }
        }
        return $array;
    }

	public function Select($from, $where = '', $campos = '*')
    {
        try{
            $array = [];
            $formato = '';
            $query = '';
            if ($where != '') {
                $formato = 'SELECT %1$s FROM %2$s WHERE %3$s';
                $query = sprintf($formato, $campos, $from, $where);
            }else{
                $formato = 'SELECT %1$s FROM %2$s';
                $query = sprintf($formato, $campos, $from);
            }
            //var_dump($query);
            $res = $this->conexion->query($query, PDO::FETCH_NAMED);

            foreach($res as $row) {
                array_push($array, $row);
            }
            
            return $this->EnviarArray($array);
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function Delete($from, $where = '')
    {
        try{
            $formato = '';
            $query = '';
            if ($where != '') {
                $formato = 'DELETE FROM %1$s WHERE %2$s';
                $query = sprintf($formato, $from, $where);
            }else{
                $formato = 'DELETE FROM %1$s';
                $query = sprintf($formato, $from);
            }
            //var_dump($query);
            $res = $this->conexion->query($query, PDO::FETCH_BOUND);

            return $res->rowCount();
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function Insert($from, $variables, $values)
    {
        try{
            $formato = 'INSERT INTO `%1$s` (%2$s) VALUES (%3$s)';
            $query = sprintf($formato, $from, $variables, $values);

            var_dump($query);
            $res = $this->conexion->query($query, PDO::FETCH_BOUND);

            return $res->rowCount();
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function Update($from, $set, $where)
    {
        try{
            $formato = 'UPDATE `%1$s` SET %2$s WHERE %3$s';
            $query = sprintf($formato, $from, $set, $where);

            //var_dump($query);
            $res = $this->conexion->query($query, PDO::FETCH_BOUND);

            return $res->rowCount();
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function SelectPre($objeto, $where = '', $campos = '*')
    {   
        try{
            //var_dump($objeto);
            if (!method_exists($objeto, 'GetFrom')) 
                throw new Exception('No se ha declarado el método GetFrom en la clase '.get_class($objeto)); 

            $from = $objeto->GetFrom();
            
            $formato = '';
            $query = '';
            if ($where != '') {
                $formato = 'SELECT %1$s FROM %2$s WHERE %3$s';
                $query = sprintf($formato, $campos, $from, $where);
            }else{
                $formato = 'SELECT %1$s FROM %2$s';
                $query = sprintf($formato, $campos, $from);
            }
            //var_dump($query);
            $this->prepare = $this->conexion->prepare($query);

            return $this->ArmarArray($objeto, $query);
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function DeletePre($objeto, $where = '')
    {
        try{
            //var_dump($objeto);
            if (!method_exists($objeto, 'GetFrom')) 
                throw new Exception('No se ha declarado el método GetFrom en la clase '.get_class($objeto));
            
            $from = $objeto->GetFrom();
        
            $formato = '';
            $query = '';
            if ($where != '') {
                $formato = 'DELETE FROM %1$s WHERE %2$s';
                $query = sprintf($formato, $from, $where);
            }else{
                $formato = 'DELETE FROM %1$s';
                $query = sprintf($formato, $from);
            }
            //var_dump($query);
            $this->prepare = $this->conexion->prepare($query);

            return $this->ArmarArray($objeto, $query);
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
        
    }

    public function InsertPre($objeto, $values = '')
    {
        try{
            //var_dump($objeto);
            if (!method_exists($objeto, 'GetFrom')) 
                throw new Exception('No se ha declarado el método GetFrom en la clase '.get_class($objeto));

            if ($values == '') 
                throw new Exception('Declare la el valor de la inserción');

            //parte la cadena para armar una cadena nueva
            $variables = explode(",", $values);
            $cadenaVariables = '';
            foreach($variables as $key => $v){ 
                $v = str_replace(':', '', $v);
                $v = str_replace(' ', '', $v);

                if (array_key_last($variables) == $key) 
                    $cadenaVariables .= '`'.$v.'`';
                else
                    $cadenaVariables .= '`'.$v.'`,';
            }

            $from = $objeto->GetFrom();
            $formato = 'INSERT INTO `%1$s` (%2$s) VALUES (%3$s)';
            $query = sprintf($formato, $from, $cadenaVariables, $values);

            //var_dump($query);
            $this->prepare = $this->conexion->prepare($query);

            return $this->ArmarArray($objeto, $query);
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function UpdatePre($objeto, $set, $where)
    {
        try{
            //var_dump($objeto);
            if (!method_exists($objeto, 'GetFrom')) 
                throw new Exception('No se ha declarado el método GetFrom en la clase '.get_class($objeto));

            $from = $objeto->GetFrom();

            $formato = 'UPDATE `%1$s` SET %2$s WHERE %3$s';
            $query = sprintf($formato, $from, $set, $where);

            //var_dump($query);
            $this->prepare = $this->conexion->prepare($query);

            return $this->ArmarArray($objeto, $query);
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    //funciones para una transaccion y preparaciones
    public function Transaccion()
    {
        $this->conexion->beginTransaction();
        $this->transaction = true;
    }

    public function Ejecutar($objeto = [])
    {
        try{
            //var_dump($this->prepare);
            if ($this->transaction && count($objeto) == 0) {
                $this->conexion->commit();
                $this->transaction = false;
            }elseif($this->prepare != '') {
                //var_dump($objeto);

                $noEsArray = false;
                $resArray = [];
                foreach($objeto as $clave => $valor){

                    if (is_array($valor)) {
                        $contNumero = 0;
                        foreach($valor as $v){
                            //var_dump($v);
                            $res = $this->prepare->execute([$clave =>$v]);
                            $f = $this->prepare->fetchAll();
                            $res2 = $this->prepare->rowCount();

                            if (count($f) === 0) 
                                $contNumero += $res2;
                            else{
                                if (is_array($f) && count($f) > 0) 
                                    array_push($resArray, $f);
                            }
                        }

                        if ($contNumero > 0)
                            return $contNumero;
                        else
                            return $this->EnviarArray($resArray);
                    }else{
                        $noEsArray = true;
                        break;
                    }
                }
                //si no array
                if ($noEsArray) {
                    $res = $this->prepare->execute($objeto);
                    if ($res) {
                        $res = $this->prepare->fetchAll();
                        $res2 = $this->prepare->rowCount();
                        $res3 = $this->EnviarArray($res);

                        if (count($res3) === 0) 
                            return $res2;
                        else
                            return $res3;
                    }else
                        throw new Exception('Error al ejecutar la sentencia'); 
                }
            }else
                throw new Exception('Hay un error cuando llama al método ejecutar');
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function DevolverCambios()
    {   
        try{
            if ($this->transaction){
                $this->conexion->rollback();
                $this->transaction = false;
            }else
                throw new Exception('Error al ejecutar el método DevolverCambios primero se debe ejecutar el método Transaccion');
        }catch(Exception $e){
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
        
    }
    //funciones para una transaccion y preparaciones
}
