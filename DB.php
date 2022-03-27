<?php 
/**
 * 
 * @version   v2.0.0
 * @author    Edwin Velasquez Jimnez
 * @link      lion_3214@hotmail.com
 * @copyright LionTumbler 26/03/2022
 * @method    class db()
 * 
 */
include 'constan.php';
class db
{
    //version | nuevas funcionalidades | arreglo de errores
    private $version = 'v2.0.0';
    //conexion
    private $conexion;
    private $transaction = 0;
    private $prepare = '';
    private $nombre_db;
    //consulta
    private $formatoSelect = 'SELECT %1$s FROM %2$s';
    private $formatoDelete = 'DELETE FROM %1$s';
    private $formatoUpdate = 'UPDATE `%1$s` SET %2$s';
    private $formatoInsert = 'INSERT INTO `%1$s` (%2$s) VALUES (%3$s)';
    //donde opciones
    private $formatoWhere3 = ' WHERE %3$s';
    private $formatoWhere2 = ' WHERE %2$s';

    public function __construct($nombre_db = NOMBRE_DB, $usuario_db = USER_DB, $contrasena_db = CONTRASENA_DB, $host = HOST, $type_db = TYPE_DB)
	{
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => 0,
        ];
        $this->nombre_db = $nombre_db;

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
        }catch(PDOException $e){ 
            //throw new PDOException($e->getMessage(), (int)$e->getCode());
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
	}

    //limpiar todas las variables
    public function __destruct(){ 
        $this->conexion = null;
        $this->transaction = null;
        $this->prepare = null;
        $this->nombre_db = null;
        $this->formatoSelect = null;
        $this->formatoDelete = null;
        $this->formatoUpdate = null;
        $this->formatoInsert = null;
        $this->formatoWhere3 = null;
        $this->formatoWhere2 = null;
        //echo 'destroid';
    } 

    private function enviarArray($a)
    {   
        //var_dump($a);
        if(count($a) === 1)
            return $a[0];
        else
            return $a;
    }

    private function armarArray($objeto, $query)
    {
        $array = [];
        //echo $query;
        $reflect = new ReflectionClass($objeto);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PRIVATE);
        foreach ($props as $prop) {
            //print $prop->getName()."<br>";
            $clave = $prop->getName();
            $pos = strpos($query, ':'.$clave);
            //si encuentra la palabra
            if ($pos !== false) {
                $method = 'get'.ucfirst($clave);
                $valor = $objeto->$method();
                if (is_array($valor)) {
                    $cont = 0;
                    foreach($valor as $v){
                        $array[$cont][] = [":$clave" => $v];
                        $cont++;
                    }
                }else{
                    if (isset($valor) && $valor != '') 
                        $array[0][] = [":$clave" => $valor];
                    else
                        throw new Exception('Error al preparar el array, porque alguno de los campos suministrados está vacío '.$clave);
                }
            }
        }

        return $array;
    }

    private function is_boolean($valor)
    {
        return ($valor == true || $valor == 1 || $valor == false || $valor == 0);
    }

    //funcion para las transacciones
    private function rollBack()
    {
        $this->conexion->rollBack();
        $this->transaction = 0;
    }

    private function destruct($fp, $cadena)
    {
        fwrite($fp, "\n");
        fwrite($fp, "\t".'public function __destruct(){'."\n");
        fwrite($fp, $cadena);
        fwrite($fp, "\t".'}'."\n");
    }

    //este metodo se estaba usando pero no era necesario un constructor
    private function constructor($fp, $id)
    {
        fwrite($fp, "\n");
        fwrite($fp, "\t".'public function __construct($'.$id.' = 0){'."\n");
        fwrite($fp, "\t\t".'$this->'.$id.' = $'.$id.";\n");
        fwrite($fp, "\t".'}'."\n");
    }

    private function createGetFrom($fp)
    {
        fwrite($fp, "\n");
        fwrite($fp, "\t".'public function getFrom(){'."\n");
        fwrite($fp, "\t\t".'return $this->from;'."\n");
        fwrite($fp, "\t".'}'."\n");
    }

    private function getCCadena($campo)
    {
        $cadena = "\n";
        $cadena .= "\t".'public function get'.ucfirst($campo).'(){'."\n";
        $cadena .= "\t\t".'return $this->'.$campo.';'."\n";
        $cadena .= "\t".'}'."\n";
        return $cadena;
    }

    private function setCCadena($campo)
    {
        $cadena = "\n";
        $cadena .= "\t".'public function set'.ucfirst($campo).'($'.$campo.'){'."\n";
        $cadena .= "\t\t".'$this->'.$campo.' = $'.$campo.";\n";
        $cadena .= "\t".'}'."\n";
        return $cadena;
    }

    private function createIndexBlocking($carpeta)
    {
        $fp = fopen ($carpeta.'/index.php', 'w');
        fwrite($fp, '<script>'."\n");
        fwrite($fp, "\t".'location.href="../";'."\n");
        fwrite($fp, '</script>');
        fclose($fp);
    }

    private function comentarioPredeterminado($fp, $class)
    {
        fwrite($fp, '/** '."\n");
        fwrite($fp, ' * '."\n");
        fwrite($fp, ' * @version   '."$this->version\n");
        fwrite($fp, ' * @author    Edwin Velasquez Jimenez<lion_3214@hotmail.com>'."\n");
        fwrite($fp, ' * @link      https://github.com/liontumbler/conexionDb'."\n");
        fwrite($fp, ' * @copyright LionTumbler '.date('d/m/Y')."\n"); 
        fwrite($fp, ' * @method    class '.$class."() objeto creado con la librería conexionDb\n");
        fwrite($fp, ' * '."\n");
        fwrite($fp, ' */'."\n");
    }

    private function generateStringInsert($objeto, $prepare = 0)
    {   
        try {
            $cadena = '';
            $cadenaValores = '';
            $array = array();
            $reflect = new ReflectionClass($objeto);
            $props   = $reflect->getProperties(ReflectionProperty::IS_PRIVATE);

            foreach ($props as $prop) {

                $clave = $prop->getName();
                if ($clave != 'from') {
                    $method = 'get'.ucfirst($clave);
                    $valor = $objeto->$method();
                    $typeCadena = 'type'.ucfirst($clave);
                    $type = $objeto->$typeCadena;

                    if(isset($valor)){
                        
                        //consultas preparadas
                        if($prepare) 
                            $cadena .= ':'.$clave.', ';
                        //consulta normal
                        else{
                            $cadena .= $clave.', ';
                            $cadenaValores .= '"'.$valor.'", ';
                        }

                        //valida el tipo de dato
                        if($type == 'boolean'){
                            if(!$this->is_boolean($valor))
                                throw new Exception('No se puede proseguir porque el campo ('.$clave.') es un valor ('.$valor.') y debe ser un boolean');
                        }elseif ($type == 'integer'){
                            if(!is_numeric($valor))
                                throw new Exception('No se puede proseguir porque el campo ('.$clave.') es un valor ('.$valor.') y debe ser un integer');
                        }
                    }
                }
            }

            if($cadena != '')
                $cadena = substr($cadena, 0, -2);
            
            if($cadenaValores != '')
                $cadenaValores = substr($cadenaValores, 0, -2);

            $array['variables'] = $cadena;
            $array['values'] = $cadenaValores;

            if($array['values'] == '')
                return $cadena;
            else
                return $array;
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();

            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    private function generateStringUpdate($objeto, $prepare = 0)
    {
        try {
            $cadena = '';
            $reflect = new ReflectionClass($objeto);
            $props   = $reflect->getProperties(ReflectionProperty::IS_PRIVATE);

            $id = '';
            foreach ($props as $prop) {
                
                $clave = $prop->getName();

                //busca que campo es el id
                $posicion_coincidencia = strpos(strtolower($clave), 'id');                
                if ($posicion_coincidencia !== false && $id == '')
                    $id = $clave;

                //mira que sea diferente a id y al from
                if ($clave != 'from' && $clave != $id) {
                    $method = 'get'.ucfirst($clave);
                    $valor = $objeto->$method();
                    $typeCadena = 'type'.ucfirst($clave);
                    $type = $objeto->$typeCadena;

                    if(isset($valor)){
                        //consultas preparadas
                        if($prepare) 
                            $cadena .= '`'.$clave.'` = :'.$clave.', ';
                        //consulta normal
                        else
                            $cadena .= $clave.' = "'.$valor.'", ';

                        //valida el tipo de dato
                        if($type == 'boolean'){
                            if(!$this->is_boolean($valor))
                                throw new Exception('No se puede proseguir porque el campo ('.$clave.') es un valor ('.$valor.') y debe ser un boolean');
                        }elseif ($type == 'integer'){
                            if(!is_numeric($valor))
                                throw new Exception('No se puede proseguir porque el campo ('.$clave.') es un valor ('.$valor.') y debe ser un integer');
                        }
                    }
                }
            }

            if($cadena != '')
                $cadena = substr($cadena, 0, -2);

            return $cadena;
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    //incluye la clase y la instancia automaticamente para solo acceder a los metodos del mismo
    public function includeModelo($modelo, $dir)
    {
        $raizProyecto = explode("/", $_SERVER['DOCUMENT_ROOT']);
        $raizActual = explode("/", $dir);

        $cuantoRetrocedo = abs((count($raizProyecto)-count($raizActual))) -1;
        $retroceso = '';
        for ($i=0; $i < ($cuantoRetrocedo); $i++) { 
            $retroceso .= '../';
        }
        $retroceso .= 'modelosDB';
        
        $file = $retroceso.'/'.$modelo.'.php';
        if (file_exists($file)) {
            include $file;
            $class = ucfirst($modelo);

            return new $class();
        }
        else {
            echo 'el modelo no se a creado, se crea o refresca';
            echo '<br>';
            $this->createORepaintModels($retroceso);

            return $this->includeModelo($modelo, $dir);
        }
    }

    //crea la carpeta en la raiz del proyecto los modelos para manejar la DB
    public function createORepaintModels($carpeta = 'modelosDB')
    {
        //prefijo de como se extrae el dato en Mysql
        $prefijo = 'Tables_in_'.$this->nombre_db;

        //si no existe la carpeta modelosDB la crea
        if (!file_exists($carpeta))
            //devuelve true o false mkdir si la pudo o no crear
            mkdir($carpeta, 0777, 1);

        # listar todas las tablas de esa DB
        $tables = $this->conexion->query("SHOW FULL TABLES FROM $this->nombre_db", PDO::FETCH_NAMED);
        foreach($tables as $table) {

            $tabla = $table[$prefijo];
            $class = ucfirst(strtolower($tabla));

            //crear archivo
            $fp = fopen ($carpeta.'/'.strtolower($tabla).'.php', 'w');
            fwrite($fp, "<?php \n");
            $this->comentarioPredeterminado($fp, $class);
            fwrite($fp, 'class '.$class."{\n");
            fwrite($fp, "\n");
            fwrite($fp, "\t".'private $from = "'.$tabla.'";'."\n");

            #listar campos de tabla
            $campos = $this->conexion->query("SHOW COLUMNS FROM $tabla", PDO::FETCH_NAMED);
            $id = '';
            $arrMethodField = array();
            $arrTypeField = array();
            $cadenaDestructField = '';
            foreach($campos as $campo) {

                //escribe el archivo
                fwrite($fp, "\t".'private $'.$campo['Field'].';'."\n");

                //crea variable publica con el tipo de la varible
                $type = 'string';
                $esEntero = strpos(strtolower($campo['Type']), 'int'); 
                $esBoolean = strpos(strtolower($campo['Type']), 'tinyint(1)');                
                if ($esBoolean !== false) 
                    $type = 'boolean';
                elseif ($esEntero !== false)
                    $type = 'integer';
                $arrTypeField[] = "\t".'public $type'.ucfirst($campo['Field']).' = "'.$type.'";'."\n";

                //guarda formato de cadena set en un array para despues ser insertado al archivo
                $cadena = $this->setCCadena($campo['Field']);
                //guarda formato de cadena set en un array para despues ser insertado al archivo
                $cadena .= $this->getCCadena($campo['Field']);
                $arrMethodField[] = $cadena;

                $cadenaDestructField .= "\t\t".'$this->'.$campo['Field'].' = NULL;'."\n";
                $cadenaDestructField .= "\t\t".'$this->type'.ucfirst($campo['Field']).' = NULL;'."\n";

                //busca que campo es el id
                $posicion_coincidencia = strpos(strtolower($campo['Field']), 'id');                
                if ($posicion_coincidencia !== false && $id == '')
                    $id = $campo['Field'];
            }

            fwrite($fp, "\n");
            for ($i=0; $i < count($arrTypeField); $i++) { 
                fwrite($fp, $arrTypeField[$i]);
            }

            //metodo constructor
            //$this->constructor($fp, $id);

            //metodo destructor
            $this->destruct($fp, $cadenaDestructField);

            //crea funcion getFrom
            $this->createGetFrom($fp);
            
            for ($i=0; $i < count($arrMethodField); $i++) { 
                fwrite($fp, $arrMethodField[$i]);
            }

            fwrite($fp, '}'."\n");
            fwrite($fp, '?>');
            fclose($fp);

            //crear archivo para que no se pueda acceder desde el navegador a esa carpeta
            $this->createIndexBlocking($carpeta);
        }
    }

	public function select($from, $where = '', $campos = '*')
    {
        try{
            $array = [];
            $query = '';
            if ($where != '') {
                $formato = $this->formatoSelect.$this->formatoWhere3;
                $query = sprintf($formato, $campos, $from, $where);
            }else{
                $formato = $this->formatoSelect;
                $query = sprintf($formato, $campos, $from);
            }
            //var_dump($query);
            $res = $this->conexion->query($query, PDO::FETCH_NAMED);

            foreach($res as $row) {
                array_push($array, $row);
            }
            
            return $this->enviarArray($array);
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function selectPre($objeto, $where = '', $campos = '*')
    {   
        try{
            //var_dump($objeto);
            if (!method_exists($objeto, 'getFrom')) 
                throw new Exception('No se ha declarado el método getFrom en la clase '.get_class($objeto)); 

            $from = $objeto->getFrom();
            $query = '';
            if ($where != '') {
                $formato = $this->formatoSelect.$this->formatoWhere3;
                $query = sprintf($formato, $campos, $from, $where);
            }else{
                $formato = $this->formatoSelect;
                $query = sprintf($formato, $campos, $from);
            }
            //var_dump($query);
            $this->prepare = $this->conexion->prepare($query);

            return $this->armarArray($objeto, $query);
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function delete($from, $where = '')
    {
        try{
            $query = '';
            if ($where != '') {
                $formato = $this->formatoDelete.$this->formatoWhere2;
                $query = sprintf($formato, $from, $where);
            }else{
                $formato = $this->formatoDelete;
                $query = sprintf($formato, $from);
            }
            //var_dump($query);
            $res = $this->conexion->query($query, PDO::FETCH_BOUND);

            return $res->rowCount();
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function deletePre($objeto, $where = '')
    {
        try{
            //var_dump($objeto);
            if (!method_exists($objeto, 'getFrom')) 
                throw new Exception('No se ha declarado el método getFrom en la clase '.get_class($objeto));
            
            $from = $objeto->getFrom();
            $query = '';
            if ($where != '') {
                $formato = $this->formatoDelete.$this->formatoWhere2;
                $query = sprintf($formato, $from, $where);
            }else{
                $formato = $this->formatoDelete;
                $query = sprintf($formato, $from);
            }
            //var_dump($query);
            $this->prepare = $this->conexion->prepare($query);

            return $this->armarArray($objeto, $query);
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
        
    }

    public function insert($objeto)
    {
        try{
            if (!method_exists($objeto, 'getFrom')) 
                throw new Exception('No se ha declarado el método getFrom en la clase '.get_class($objeto));

            $array = $this->generateStringInsert($objeto);
            $query = sprintf($this->formatoInsert, $objeto->getFrom(), $array['variables'], $array['values']);

            //var_dump($query);
            $res = $this->conexion->query($query, PDO::FETCH_BOUND);

            return $res->rowCount();
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function insertPre($objeto)
    {
        try{
            //var_dump($objeto);
            if (!method_exists($objeto, 'getFrom')) 
                throw new Exception('No se ha declarado el método getFrom en la clase '.get_class($objeto));

            //arma una cadena con los valores llenos en el objeto
            $values = $this->generateStringInsert($objeto, 1);

            //parte la cadena para armar una cadena nueva
            $variables = explode(",", $values);
            $cadenaVariables = '';
            foreach($variables as $key => $v){ 
                $v = str_replace(':', '', $v);
                $v = str_replace(' ', '', $v);

                $cadenaVariables .= '`'.$v.'`,';
            }

            if($cadenaVariables != '')
                $cadenaVariables = substr($cadenaVariables, 0, -1);

            $query = sprintf($this->formatoInsert, $objeto->getFrom(), $cadenaVariables, $values);

            //var_dump($query);
            $this->prepare = $this->conexion->prepare($query);

            return $this->armarArray($objeto, $query);
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function update($objeto, $where)
    {
        try{
            if (!method_exists($objeto, 'getFrom')) 
                throw new Exception('No se ha declarado el método getFrom en la clase '.get_class($objeto));

            $set = $this->generateStringUpdate($objeto);
            $formato = $this->formatoUpdate.$this->formatoWhere3;
            $query = sprintf($formato, $objeto->getFrom(), $set, $where);

            //var_dump($query);
            $res = $this->conexion->query($query, PDO::FETCH_BOUND);

            return $res->rowCount();
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function UpdatePre($objeto, $where)
    {
        try{
            //var_dump($objeto);
            if (!method_exists($objeto, 'getFrom')) 
                throw new Exception('No se ha declarado el método getFrom en la clase '.get_class($objeto));

            $set = $this->generateStringUpdate($objeto, 1);
            $formato = $this->formatoUpdate.$this->formatoWhere3;
            $query = sprintf($formato, $objeto->getFrom(), $set, $where);

            //var_dump($query);
            $this->prepare = $this->conexion->prepare($query);

            return $this->armarArray($objeto, $query);
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    public function Ejecutar($objeto = [])
    {
        try{
            //var_dump($this->prepare);
            if($this->prepare != '') {
                //var_dump($objeto);
                $noEsArray = 0;
                $resArray = [];
                
                $contNumero = 0;
                foreach($objeto as $clave => $valor){

                    if (is_array($valor)) {
                        $vArr = [];
                        foreach($valor as $v){
                            $vArr = array_merge($vArr, $v);
                        }

                        $res = $this->prepare->execute($vArr);
                        $f = $this->prepare->fetchAll();
                        $res2 = $this->prepare->rowCount();

                        if (count($f) === 0) 
                            $contNumero += $res2;
                        else if (is_array($f) && count($f) > 0)
                            array_push($resArray, $f);
                    }else{
                        $noEsArray = 1;
                        break;
                    }
                }

                //si no array
                if ($noEsArray) {
                    $res = $this->prepare->execute($objeto);
                    if ($res) {
                        $res = $this->prepare->fetchAll();
                        $res2 = $this->prepare->rowCount();
                        $res3 = $this->enviarArray($res);
                        if (count($res3) === 0) 
                            return $res2;
                        else
                            return $res3;
                    }else
                        throw new Exception('Error al ejecutar la sentencia'); 

                }else{
                    if (count($resArray) === 0)
                        return $contNumero;
                    else
                        return $this->enviarArray($resArray);
                }

            }else
                throw new Exception('Hay un error cuando llama al método ejecutar()');
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    //si no puede hacer una query o lo que quiera hacer puede retornar la conexion para hacer lo que le plasca ya que devuelve un objeto PDO ya conectado a DB
    public function getConn()
    {
        try {
            return $this->conexion;
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }

    //funciones para una transaccion
    public function readyTransaction()
    {
        if(!$this->transaction){
            $this->conexion->beginTransaction();
            $this->transaction = 1;
        }else
            throw new Exception('Ya está el método iniciado, tiene que llamar el método endTransaction() para terminar la transacción');
    }

    //funciones para una transaccion
    public function endTransaction()
    {
        try {
            if ($this->transaction) {
                if($this->conexion->commit()){
                    $this->transaction = 0;
                }else
                    throw new Exception('ocurrio un error al momento de commitear la transaccion');
            }else
                throw new Exception('Debe iniciar primero el método readyTransaction() para terminar la transacción');
        }catch(Exception $e){
            if($this->transaction)
                $this->rollBack();
            
            echo 'Excepción capturada: ', $e->getMessage(), "<br>";
        }
    }
}
?>