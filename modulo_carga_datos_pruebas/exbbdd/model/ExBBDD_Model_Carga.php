<?php
/**
* Modelo para el acceso a la base de datos y funciones CRUD
* Autor: ELivar Largo
* Sitio Web: wwww.ecodeup.com
*/
class ExBBDD_Model_Carga
{
	//atributos
    public $nif;
	public $fecha_interaccion;
	public $tipo_interaccion;
    public $time_stamp;
    
	private $DB;
	private $DBpro;
    private $DBsiebelx;
    
	private $datos_nif;
    private $datos;
    private $tabla;
    private $ambito;
    private $fecha_devo;
 
	public function __construct()
	{   
        global $DB;
        global $DBpro;
        global $DBsiebelx;        
        $this->DB = $DB;
        $this->DBpro = $DBpro;
        $this->DBsiebelx = $DBsiebelx;
    }

    public function get_datos_tabla($tabla, $fecha_devo, $ambito)
    {
        $this->tabla = strtoupper($tabla);
        $this->fecha_devo = date("Ymd", strtotime($fecha_devo));
        $this->ambito = ($ambito == 'PERSONAL') ? 'P_':'';

        if($this->tabla == 'CLIENTES'
            || $this->tabla == 'CUENTAS'
            || $this->tabla == 'FACT_FACTURA'
            || $this->tabla == $this->ambito.'CARTERA'
            || $this->tabla == $this->ambito.'PARI'
            || $this->tabla == $this->ambito.'DEVOLUCIONES'
        ) {
            $sql = $this->sql_por_cust_code();
        } elseif($this->tabla == $this->ambito.'PAGOS' || $this->tabla == $this->ambito.'PAGOS_NOPARI'){
            $sql = $this->sql_pagos_pagos_nopari();
        } elseif($this->tabla == $this->ambito.'ESTADOS_PARI'){
            $sql = $this->sql_estados_pari();
        } elseif($this->tabla == 'INTERACCIONES_TIER'){
            if($this->get_interacciones()) {
                return true;
            } else {
                return false;
            }
        } else {
            echo 'la tabla no esta registrada <br>';
            die();
        }

        echo 'Consulta que se está procesando <br><br>' . $sql . '<br><br><br>';
        // die();

        echo 'Haciendo la consulta <br><br>';
        $rs = $this->DBpro->Execute($sql);
        
        if ($rs) {
            $i = 0;
            $this->datos = '';

            echo 'Creando fichero ' . $this->tabla . '.sql <br><br>';

            $ruta = 'files/' . $this->tabla . '.sql';
            $file = fopen($ruta, "w");

            while(!$rs->EOF) {
                
                // if($i > 20){break;}

                $this->datos['field'] = $rs->fields;
                $this->datos['tipo'] = $rs->_fieldobjects;

                $line = $this->create_insert();

                fwrite($file, $line . PHP_EOL);

                $rs->MoveNext();
                $i++;
            }
            $rs->close();
            fwrite($file, 'COMMIT;' . PHP_EOL);
            fclose($file);

            echo 'Número de registros procesados ' . $i . '<br>';
            return true;
        } 

        return false;

    }

    public function sql_por_cust_code()
    { 
        //AND D.CUST_CODE = '1.22076909'
        $sql = "SELECT * FROM " . $this->tabla ." T
                WHERE T.CUST_CODE IN (
                    SELECT D.CUST_CODE FROM " . $this->ambito ."DEVOLUCIONES D 
                    WHERE D.FECHA_DEVOLUCION = " . $this->fecha_devo . "
                    GROUP BY D.CUST_CODE
                ) ";

        if($this->tabla != 'CLIENTES' && $this->tabla != 'CUENTAS'){
            $sql .= "AND TO_DATE(T.FECHA_FACTURA,'YYYYMMDD') 
                    BETWEEN TO_DATE(" . $this->fecha_devo . ",'YYYYMMDD')-425 
                    AND TO_DATE(" . $this->fecha_devo . ",'YYYYMMDD')-1";
        }
        
        return $sql;
    }

    public function sql_devoluciones()
    {
        $sql = "SELECT * FROM " . $this->tabla . " D 
                WHERE D.FECHA_DEVOLUCION = " . $this->fecha_devo . " 
                AND TO_DATE(D.FECHA_FACTURA,'YYYYMMDD') 
                BETWEEN SYSDATE-425 AND TO_DATE(" . $this->fecha_devo . ",'YYYYMMDD')";

        return $sql;
    }

    public function sql_pagos_pagos_nopari()
    {   
        //AND D.CUST_CODE = '1.22076909'
        $sql = "SELECT T.* FROM " . $this->tabla . " T 
                LEFT JOIN " . $this->ambito ."DEVOLUCIONES D ON T.NUM_FACTURA = D.NUM_FACTURA
                WHERE D.CUST_CODE IN (
                            SELECT DD.CUST_CODE FROM " . $this->ambito ."DEVOLUCIONES DD 
                            WHERE DD.FECHA_DEVOLUCION = " . $this->fecha_devo ."
                        )
                AND TO_DATE(D.FECHA_FACTURA,'YYYYMMDD') 
                BETWEEN TO_DATE(" . $this->fecha_devo . ",'YYYYMMDD')-425 
                AND TO_DATE(" . $this->fecha_devo . ",'YYYYMMDD')-1";

        return $sql;
    }

    public function sql_estados_pari()
    {   
        //AND D.CUST_CODE = '1.22076909'
        $sql = "SELECT T.* FROM " . $this->tabla . " T 
                LEFT JOIN " . $this->ambito ."PARI P ON T.NUM_FACTURA = P.NUM_FACTURA
                WHERE P.CUST_CODE IN (
                            SELECT DD.CUST_CODE FROM " . $this->ambito ."DEVOLUCIONES DD 
                            WHERE DD.FECHA_DEVOLUCION = " . $this->fecha_devo ."
                        )
                AND TO_DATE(P.FECHA_FACTURA,'YYYYMMDD') 
                BETWEEN TO_DATE(" . $this->fecha_devo . ",'YYYYMMDD')-425 
                AND TO_DATE(" . $this->fecha_devo . ",'YYYYMMDD')-1";

        return $sql;
    }

    public function create_insert()
    {
        $array_key = '';
        $array_value = '';

        $sql = "INSERT INTO $this->tabla ";

        foreach ($this->datos["field"] as $key => $value) {

            $array_key .= $key . ',';

            if($value == null) {
                $array_value .= 'null,';
            } else {
                foreach ($this->datos["tipo"] as $val) {
                    if($val->name == $key) {
                        if($val->type == 'VARCHAR2'){
                            $array_value .= "'" . $value . "',";
                        } else {
                            $array_value .= $value . ',';
                        }
                    }
                }
            }
        }

        $sql .= "(" . substr($array_key, 0, -1) . ") ";
        $sql .= "VALUES(" . substr($array_value, 0, -1) . ");";
        // echo $sql .'<br>';
        return $sql;
    }

    public function get_nif_devoluciones() 
    {
        $sql = "SELECT C.NIF FROM CLIENTES C
        INNER JOIN " . $this->ambito ."DEVOLUCIONES D ON C.CUST_CODE = D.CUST_CODE
        WHERE D.FECHA_DEVOLUCION = " . $this->fecha_devo;

        $rs = $this->DBpro->Execute($sql);

        if ($rs) {
            while(!$rs->EOF) {
                $this->datos_nif[] = $rs->fields['NIF'];
                $rs->MoveNext();
            }
            $rs->close();
            return true;
        } 
        return false;
    }

    public function get_interacciones()
    {
        if($this->get_nif_devoluciones()){
            $i = 0;
            
            $ruta = 'files/' . $this->tabla . '.sql';
            $file = fopen($ruta, "w");

            foreach($this->datos_nif as $nif){
                // if($i > 20){break;}

                $sql = "SELECT B.LOC NIF, A.CREATED FECHA_INTERACCION, A.X_ATTRIB_03 TIPO_INTERACCION
                        FROM  S_EVT_ACT A, S_ORG_EXT B 
                        WHERE 1=1
                        AND B.ROW_ID  = A.TARGET_OU_ID
                        AND LOC IS NOT NULL
                        AND A.CREATED >= to_date(to_char(sysdate-420, 'dd/mm/yyyy')||' 00:00:00', 'DD/MM/YYYY HH24:MI:SS')
                        AND A.CREATED <= to_date(to_char(sysdate-60, 'dd/mm/yyyy')||' 23:59:59', 'DD/MM/YYYY HH24:MI:SS')
                        AND A.OWNER_login<> 'SADMIN'
                        AND A.X_ATTRIB_03 is not NULL
                        AND B.LOC = '$nif'";
                
                $rs = $this->DBsiebelx->Execute($sql);
    
                if ($rs) {
                    while(!$rs->EOF) {
                        $this->nif = $rs->fields['NIF'];
                        $this->fecha_interaccion = date("Ymd",strtotime($rs->fields['FECHA_INTERACCION']));
                        $this->tipo_interaccion = $rs->fields['TIPO_INTERACCION'];
    
                        $line = $this->set_Interacciones();
    
                        fwrite($file, $line . PHP_EOL);
    
                        $rs->MoveNext();
                    }
                    $rs->close();
                } else {
                    fclose($file);
                    return false;
                }
                
                $i++;
            }

            fwrite($file, 'COMMIT;' . PHP_EOL);
            fclose($file);
            echo 'Número de registros procesados ' . $i . '<br>';

            return true;
        } else {
            return false;
        }

    }

    public function set_Interacciones() {
        
        $sql = "INSERT INTO " . $this->tabla ." (NIF,FECHA_INTERACCION,TIPO_INTERACCION,TIME_STAMP)
                VALUES ('". $this->nif . "',". $this->fecha_interaccion . ",'" . $this->tipo_interaccion . "'," . date("YmdHi") . ")";

        $buscar=array(chr(13).chr(10), "\r\n", "\n", "\r", "   ");
        $reemplazar=array("", "", "", "", "");
        $cadena=str_ireplace($buscar,$reemplazar,$sql);
        
        return $cadena . ';';
    }

}