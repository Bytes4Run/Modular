<?php
/**
 * Context 
 * @description This class is the base class for all models
 * @author Jorge Echeverria <jecheverria@bytes4run.com> 
 * @category Class 
 * @package B4R\Kernel\classes\Context
 * @version 1.3.0 rev.1 
 * @date 2023-05-01 - 2024-03-06
 * @time 17:50:00
 * @copyright (c) 2023 Bytes4Run 
*/ 
 
declare(strict_types=1);

namespace B4R\Kernel\classes;

use B4R\Kernel\classes\Connection;
use DeepCopy\Exception\PropertyException;

class Context {
    private ?string $base_name;
    private ?string $table_name;
    private ?array $error;
    private string|array $fields;
    private string|array $conditions;
    private string $query;
    /**
     * Function constructor
     * @param string $base_name Base name of the model
     * @param string $table_name Table name of the model
     * @return void
     */
    public function __construct(?string $base_name = null, ?string $table_name = null) {
        $this->base_name = "default";
        $this->table_name = null;
        $this->error = null;
        $this->fields = "*";
        $this->conditions = "";
        $this->query = "";
        if ($base_name) {
            $this->base_name = $base_name;
        }
        if ($table_name) {
            $this->table_name = $table_name;
        }
    }
    /**
     * Function to get the error
     * @return array|null
     */
    public function getError(): ?array
    {
        return $this->error;
    }
    /**
     * Function to get data from the database using the parameters given by the user
     * This can resolve an array of data or null if there is no data or error
     * @param array $query Array of parameters with the following
     * Structure:
     * 
     * $query = [
     *    "fields" => ["table"=>["field1","field2","field3"]],
     *    "joins" => ["type" => ['table1'=>"field",'table2 => "field"],...],
     *    "conditions" => [["type" => ['field1', 'value1']],['type2' => ["field2",'value2']],...,"separator" => "AND"],
     * ];
     * 
     * Where: 
     * 
     * "fields": Is an array of tables and fields to get, where the table is the key of an array of fields, This can be a table name as a key for a string of fields separated by commas
     * "joins": Is an array of tables and fields to join, hwere the key is the type of join and the value is an array of tables and fields to join 
     * "conditions": Is an array of conditions to filter the data, where the type is the type of condition, the first value is the field and the second value is the value to compare, the separator is the separator of the conditions
     * The type of conditions can be:
     * 
     * *'COMPARE':* To compare values. This will be: ' = ? ' on the query statement.
     * 
     * *'SIMILAR':* To compare a value amoung. This will be: ' LIKE CONCAT('%', ?, '%') ' on the query statement.
     * 
     * *'START_WITH':* To compare values that start with. This will be: ' LIKE CONCAT(?, '%') ' on the query statement.
     * 
     * *'END_WITH':* To compare values that end with. Representado en la query como: ' LIKE CONCAT('%', ?) '
     * 
     * *'RANGE':* To compare values between two values. Representado en la query como: ' BETWEEN ? AND ? '
     * 
     * *'NEGATIVE':* To compare values that are not iquals. Representado en la query como: ' != ? '
     * 
     * *'LESS_THAN':* To compare values less than field value. Representado en la query como: ' < ? '
     * 
     * *'MORE_THAN':* To compare values more than field value. Representado en la query como: ' > ? '
     * 
     * *'LESS_EQ_TO':* To compare values less than or equal to a field value. Representado en la query como: ' <= ? '
     * 
     * *'MORE_EQ_TO':* To compare valous more than or equal to. Representado en la query como: ' >= ? '
     * 
     * *'NOT_IN':* To values not include in an array. 
     * Representado en la query como: ' NOT IN (?) ' o ' NOT IN (?,?,...) ' dependiendo de la cantidad de valores en el arreglo.
     * 
     * *'IS_IN':* To values include in an array.
     * Representado en la query como: ' IN (?) ' o ' IN (?,?,...) ' dependiendo de la cantidad de valores en el arreglo.
     * 
     * "separator" Have to be an array of separator for each condition, like: [Y,O] for AND y OR respectibly.
     * 
     * @param int $limit Limit of rows to get
     * @param int $offset Offset of rows to get
     * @param string $sorting Order of the rows to get. Accepted: ASC or DESC
     * @param string $sortingby Order by of the rows to get. Accepted: A field name
     * @param string $grouping Group by of the rows to get. Accepted: A field name
     * @return array|null
     * @link https://www.bytes4run.com/projects/context/select
     */
    protected function select (
        array $query, 
        int $limit = 0, 
        int $offset = 0, 
        string $sorting = "ASC", 
        string $sortingby = "", 
        string $grouping): ?array 
    {
        if (!empty($tableName)) {
            $this->table_name = $tableName;
        }
        if (empty($query)) {
            $this->error = ["code" => 400, "message" => "The query is empty"];
            return null;
        } else {
            return $this->getDbData($query, $limit, $offset, $sorting, $sortingby, $grouping);
        }
    }
    /**
     * Function to insert data into the database using the parameters given by the user
     *
     * @param array $query Name of the table to be affected
     * @param string $tableName Array of date to be inserted in the table. This contains the fields and values to be process
     * * *'fields':* This has to be an array of all fields to be affected by the insertion.
     * *'values':* This will be an array of values to be inserted in the data table.
     * @return array|null
     */
    protected function insert (array $query, string $tableName): ?array {
        if (!empty($tableName)) {
            $this->table_name = $tableName;
        }
        if (!empty($query)) {
            return $this->setDbData('insert',$query['fields'],$query['values']);
        } else {
            $this->error = ["code" => 400, "message" => "The query is empty"];
            return null;
        }
    }
    /**
     * Function to update data into the database using the parameters given by the user
     *
     * @param array $query
     * 
     * *'fields':* Is an array of fields to update, where the key is the field name and the value is the value to update
     * *'values':* Is an array of values to update, where the key is the field name and the value is the value to update
     * *'conditions':* Is an array of conditions to filter the data, where the type is the type of condition, the first value is the field and the second value is the value to compare, the separator is the separator of the conditions
     * 
     * @example
     * $query = [
     *      ...
     *      "conditions" => [["type" => ['field1', 'value1']],['type2' => ["field2",'value2']],...,"separator" => "AND"]
     * ];
     *
     *  The type of conditions can be:
     * 
     * * *'COMPARE':* To compare values. This will be: ' = ? ' on the query statement.
     * 
     * *'SIMILAR':* To compare a value amoung. This will be: ' LIKE CONCAT('%', ?, '%') ' on the query statement.
     * 
     * *'START_WITH':* To compare values that start with. This will be: ' LIKE CONCAT(?, '%') ' on the query statement.
     * 
     * *'END_WITH':* To compare values that end with. Representado en la query como: ' LIKE CONCAT('%', ?) '
     * 
     * *'RANGE':* To compare values between two values. Representado en la query como: ' BETWEEN ? AND ? '
     * 
     * *'NEGATIVE':* To compare values that are not iquals. Representado en la query como: ' != ? '
     * 
     * *'LESS_THAN':* To compare values less than field value. Representado en la query como: ' < ? '
     * 
     * *'MORE_THAN':* To compare values more than field value. Representado en la query como: ' > ? '
     * 
     * *'LESS_EQ_TO':* To compare values less than or equal to a field value. Representado en la query como: ' <= ? '
     * 
     * *'MORE_EQ_TO':* To compare valous more than or equal to. Representado en la query como: ' >= ? '
     * 
     * *'NOT_IN':* To values not include in an array. 
     * Representado en la query como: ' NOT IN (?) ' o ' NOT IN (?,?,...) ' dependiendo de la cantidad de valores en el arreglo.
     * 
     * *'IS_IN':* To values include in an array.
     * Representado en la query como: ' IN (?) ' o ' IN (?,?,...) ' dependiendo de la cantidad de valores en el arreglo.
     * 
     * "separator" Have to be an array of separator for each condition, like: [Y,O] for AND y OR respectibly.
     * 
     * @param string $tableName
     * @return array|null
     */
    protected function update (array $query, array|string $parameters, string $tableName = ''): ?array {
        if (!empty($tableName)) {
            $this->table_name = $tableName;
        }
        if (empty($parameters)) {
            $this->error = ['code'=> 500, 'message'=> "The parameters are empty, this will cause a no good ending."];
            return null;
        }
        if (empty($query)) {
            $this->error = ["code" => 400, "message" => "The query is empty"];
            return null;
        }
        return $this->setDbData('update',$query['fields'],$query['values'],$parameters);
    }
    /**
     * Function to delete data from the database using the parameters given by the user
     *
     * @param array $query Name of the table to be affected.
     * @param array|string $parameters Array of conditions to filter the data, where the type is the type of condition, 
     * the first value is the field and the second value is the value to compare, the separator is the separator of the conditions
     * Condition to be satisfied to delete the record.
     * "conditions": Is an array of conditions to filter the data, where the type is the type of condition, the first
     * value is the field and the second value is the value to compare, the separator is the separator of the conditions
     * @example
     *       $query = [
     *       ...
     *       "conditions" => [["type" => ['field1', 'value1']],['type2' => ["field2",'value2']],...,"separator" => "AND"]
     *       ];
     *
     *   The type of conditions can be:
     *
     *   *'COMPARE':* To compare values. This will be: ' = ? ' on the query statement.
     *
     *   *'SIMILAR':* To compare a value among. This will be: ' LIKE CONCAT('%', ?, '%') ' on the query statement.
     *
     *   *'START_WITH':* To compare values that start with. This will be: ' LIKE CONCAT(?, '%') ' on the query statement.
     *
     *   *'END_WITH':* To compare values that end with. This will be: ' LIKE CONCAT('%', ?) '
     *
     *   *'RANGE':* To compare values between two values. This will be: ' BETWEEN ? AND ? '
     *
     *   *'NEGATIVE':* To compare values that are not equals. This will be: ' != ? '
     *
     *   *'LESS_THAN':* To compare values less than field value. This will be: ' < ? '
     *
     *   *'MORE_THAN':* To compare values more than field value. This will be: ' > ? '
     *
     *   *'LESS_EQ_TO':* To compare values less than or equal to a field value. This will be: ' <= ? '
     *
     *   *'MORE_EQ_TO':* To compare values more than or equal to. This will be: ' >= ? '
     *
     *   *'NOT_IN':* To values not include in an array.
     *   This will be: ' NOT IN (?) ' o ' NOT IN (?,?,...) ' depending on array length.
     *
     *   *'IS_IN':* To values include in an array.
     *   This will be: ' IN (?) ' o ' IN (?,?,...) ' depending on the array length.
     *
     *   "separator" Have to be an array of separator for each condition, like: [Y,O] for AND y OR respectively.
     * 
     * @param string $tableName
     * @return bool
     */
    protected function delete (array $query, string $tableName = ""): bool {
        if (!empty($tableName)) {
            $this->table_name = $tableName;
        }
        if (empty($query)) {
            $this->error = ["code" => 400, "message" => "The query is empty"];
            return null;
        }
        return $this->setDbData($query);
    }    
    /**
     * Función que devuelve el cálculo de registros en la tabla sugerida, respetando la condición dada.
     *
     * @param string $tableName Name of the table affected
     * @param string $function Name of the function to be use in the calculation process.
     * This could be:
     *
     * *"COUNT" :* Count and returns the total of records in a table.
     * *"MAX" :* Returns the maximum
     * *"MIN" :* Returns the minimum
     * @param string $sorting Field to use in the sorting process
     * @param array|string $parameters Parameters to be satisfied to return any output
     * 
     * "conditions": Is an array of conditions to filter the data, where the type is the type of condition, the first
     * value is the field and the second value is the value to compare, the separator is the separator of the conditions
     * The type of conditions can be:
*
     *  *'COMPARE':* To compare values. This will be: ' = ? ' on the query statement.
     *
     *  *'SIMILAR':* To compare a value among. This will be: ' LIKE CONCAT('%', ?, '%') ' on the query statement.
     *
     *  *'START_WITH':* To compare values that start with. This will be: ' LIKE CONCAT(?, '%') ' on the query statement.
     *
     *  *'END_WITH':* To compare values that end with. This will be: ' LIKE CONCAT('%', ?) '
     *
     *  *'RANGE':* To compare values between two values. This will be: ' BETWEEN ? AND ? '
     *
     *  *'NEGATIVE':* To compare values that are not equals. This will be: ' != ? '
     *
     *  *'LESS_THAN':* To compare values less than field value. This will be: ' < ? '
     *
     *  *'MORE_THAN':* To compare values more than field value. This will be: ' > ? '
     *
     *  *'LESS_EQ_TO':* To compare values less than or equal to a field value. This will be: ' <= ? '
     *
     *  *'MORE_EQ_TO':* To compare valous more than or equal to. This will be: ' >= ? '
     *
     *  *'NOT_IN':* To values not include in an array.
     *  This will be: ' NOT IN (?) ' o ' NOT IN (?,?,...) ' depending on array length.
     *
     *  *'IS_IN':* To values include in an array.
     *  This will be: ' IN (?) ' o ' IN (?,?,...) ' depending on the array length.
     *
     *  "separator" Have to be an array of separator for each condition, like: [Y,O] for AND y OR respectively.
     *
     * @link /docs/develop/queryStringCondition
     * @return array|null
     */
    protected function calculate(
        string $function = 'count', 
        string $field = 'id', 
        array $parameters = null): array
    {
        if (!empty($tableName)) {
            $this->table_name = $tableName;
        }
        return $this->getDBDataFunction($function, $field, $parameters);
    }

    /**
     * Function to set the database to be used.
     * @author Jorge Echeverria <jecheverria@bytes4run>
     * @param string|null $db Name of the database.
     * @return void
     */
    protected function _setDB(?string $db)
    {
        if (!empty($db) && !is_null($db)) {
            $this->base_name = $db;
        } else {
            $this->error = ['status' => 500, 'message' => "A database name is need it.", 'data' => array()];
        }
    }

    /**
     * Function to set the table to be used.
     *
     * @param string|null $name Name of the table in the database to be used
     * @return void
     */
    protected function _setTable(?string $name)
    {
        if (!empty($name) && !is_null($name)) {
            $this->table_name = $name;
        } else {
            $this->error = ['status' => 500,'message' => "A table name is need it.", 'data' => array()];
        }
    }

    /**
     * Función que establece los campos a servir de filtro en la busqueda o campos a devolver.
     * @return string $field campos.
     * @return Context
     */
    protected function find (string|array $field = 'all'): Context
    {
        $this->fields = $field;
        return $this;
    }

    /**
     * Funcion que establece la condición a ser cumplida para la busqueda.
     *
     * @param string|array $condition
     * @return Context
     */
    protected function where (string|array $condition):Context {
        $this->conditions = $condition;
        return $this;
    }

    /**
     * Función que establece el orden de los registros a ser devueltos.
     *
     * @param string $order [ASC,DESC]
     * @param string $orderby Campo por el cual se ordenarán los registros.
     * @return Context
     */
    protected function sorting (string $order = 'ASC', string $orderby = 'id'): Context
    {
        $this->order = $order;
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Funcion que devuelve los registros solicitados.
     * @param array|null $values
     * @return array|bool
     */
    protected function get (array|null $values = null):array|bool {
        return $this->_getDbData($values);
    }

    /**
     * funcion que realiza la inserción de datos.
     *
     * @param array|null $data
     * @return array|bool
     */
    protected function save (array|null $data=null):array|bool {
        return (!is_null($data)) ? $this->_setDBData('insert', $data) : $this->_setDBData('insert');
    }

    /**
     * Función que establece el separador de una consulta.
     *
     * Puede usarse los siguientes simbolos:
     * "<";"lt";"LessThan" Menor que, para referenciar que se buscaran los resultados menores a $value (a partir de propiedad)
     * ">";"mt";"MoreThan" Mayor que, para referenciar que se buscaran los resultados mayores a $value (a partir de propiedad)
     * "=";"eq";"Equal" Igaul a, para referenciar que se buscan los valores iguales a $value (a partir de propiedad)
     * "!";"!=";"neq";"NOT";"NotEq";"Distint";"Diferent" No igual (Diferente,Distinto), para referenciar que se buscan los valores no iguales a $value
     * "<=";"lte";"LessThanEq" Menor o igual que, para hacer referencia que se buscan los valores menores o iguales a $value
     * ">=";"mte";"MoreThanEq" Mayor o igual que, para hacer referencia que se buscan los valores mayores o iguales a $value
     * "*.*";"btw";"Between" En medio, para hacer referencia que se buscan los valores que contengan en medio $value
     * "*.";"sw";"StartWith" Inicia con, para hacer referencia que se buscan los valores que inicien con $value
     * ".*";"ew";"EndWith" Termina con, para hacer referencia que se buscan los valores que terminen con $value
     * @param string $simbol
     * @param mixed $value
     * @return void
     */
    protected function oparator(string $simbol)
    {
        if (!empty($simbol) && $simbol != '') {
            switch ($simbol) {
                case "<":
                case "lt":
                case "LessThan":
                    $this->oparator = "<";
                    break;
                case ">":
                case "mt":
                case "MoreThan":
                    $this->oparator = ">";
                    break;
                case "=":
                case "eq":
                case "Equal":
                    $this->oparator = "=";
                    break;
                case "!":
                case "!=":
                case "neq":
                case "NOT":
                case "NotEq":
                    $this->oparator = "!=";
                    break;
                case "Distint":
                case "distint":
                case "DISTINT":
                case "Diferent":
                case "diferent":
                case "DIFERENT":
                    $this->oparator = "<>";
                    break;
                case "<=":
                case "lte":
                case "LessThanEq":
                    $this->oparator = "<=";
                    break;
                case ">=":
                case "mte":
                case "MoreThanEq":
                    $this->oparator = ">=";
                    break;
                case "*.*":
                case "btw":
                case "Between":
                    $this->oparator = "BETWEEN";
                    break;
                case "*.":
                case "sw":
                case "StartWith":
                    $this->oparator = "LIKE CONCAT(?, '%')";
                    break;
                case ".*":
                case "ew":
                case "EndWith":
                    $this->oparator = "LIKE CONCAT('%', ?)";
                    break;
                default:
                    $this->oparator = "=";
                    break;
            }
        } else {
            $this->oparator = "=";
        }
    }

    /**
     * Función que asigna el separador de condiciones en una consulta.
     *
     * Puede usarse los siguientes simbolos:
     * "Y";"AND";"And";"and" Para hacer referencia a que se deben cumplir todas las condiciones.
     * "O";"OR";"Or";"or" Para hacer referencia a que se debe cumplir al menos una de las condiciones.
     * @param string $simbol
     * @return void
     */
    protected function separator(string $simbol)
    {
        if (!empty($simbol) && $simbol != '') {
            switch ($simbol) {
                case "Y":
                case "AND":
                case "And":
                case "and":
                    $this->separator = "AND";
                    break;
                case "O":
                case "OR":
                case "Or":
                case "or":
                    $this->separator = "OR";
                    break;
                default:
                    $this->separator = "AND";
                    break;
            }
        } else {
            $this->separator = "AND";
        }
    }

    /**
     * Function to get data from the database using the parameters given by the user
     * This can resolve an array of data or null if there is no data or error
     * @param array $query Array of parameters with the following
     * @param integer $limit
     * @param integer $offset
     * @param string $order
     * @param string $orderby
     * @param string $groupby
     * @return array|null
     */
    private function getDbData(
        array $query, 
        int $limit, 
        int $offset, 
        string $order, 
        string $orderby, 
        string $groupby): ?array 
    { 
        $this->query = "SELECT ";
        $this->fields = $query["fields"];
        if (is_array($this->fields)) {
            foreach ($this->fields as $table => $fields) {
                if (is_array($fields)) {
                    $this->query .= implode(",",$fields).",";
                } else {
                    $this->query .= $fields.",";
                }
            }
        } else {
            $this->query .= $this->fields;
        }
        $this->query = rtrim($this->query,",");
        $this->query .= " FROM ".$this->table_name;
        if (isset($query["joins"])) {
            foreach ($query["joins"] as $type => $tables) {
                $this->query .= " ".$type." ";
                foreach ($tables as $table => $field) {
                    $this->query .= $table." ON ".$field;
                }
            }
        }
        if (isset($query["conditions"])) {
            $this->query .= " WHERE ";
            $this->conditions = $query["conditions"];
            $separator = $query["conditions"]["separator"];
            foreach ($this->conditions as $condition) {
                foreach ($condition as $type => $values) {
                    if (is_array($values)) {
                        $this->query .= $values[0]." ".$type." ? ".$separator;
                    } else {
                        $this->query .= $values." ".$type." ? ".$separator;
                    }
                }
            }
            $this->query = rtrim($this->query,$separator);
        }
        if ($orderby) {
            $this->query .= " ORDER BY ".$orderby." ".$order;
        }
        if ($groupby) {
            $this->query .= " GROUP BY ".$groupby;
        }
        if ($limit) {
            $this->query .= " LIMIT ".$limit;
        }
        if ($offset) {
            $this->query .= " OFFSET ".$offset;
        }
        $connection = new Connection($this->base_name);
        $result = $connection->query($this->query);
        if ($result) {
            return $result;
        } else {
            $this->error = $connection->getError();
            return null;
        }
    }

    /**
     * Function to set data into the database using the parameters given by the user
     *
     * @param string $type
     * @param array $fields
     * @param array $values
     * @param array $parameters
     * @return array|null
     */
    private function setDbData(
        string $type = "insert", 
        array $fields = [], 
        array $values = [], 
        array $parameters = []): ?array
    {
        $this->query = "INSERT INTO ".$this->table_name." (";
        $this->query .= implode(",",$fields).") VALUES (";
        foreach ($values as $index => $value) {
            $this->query .= ($index < count($values) - 1) ? "?,":"?";
            array_push($queryArray,$value);
        }
        $this->query = rtrim($this->query,",");
        $this->query .= ")";
        if ($type == "update") {
            $this->query = "UPDATE ".$this->table_name." SET ";
            foreach ($fields as $index => $field) {
                $this->query .= ($index < count($field) - 1) ? "?,":"?";
                array_push($queryArray,$value);
            }
            $this->query = rtrim($this->query,",");
            if (!empty($parameters)) {
            $this->query .= " WHERE ";
            $conditions = $this->getConditions($parameters);
            $this->query .= $conditions['cadena'];
            $queryArray = array_merge($queryArray,$conditions['valores']);
        }
        }
        if ($type == "delete") {
            $this->query = "DELETE FROM ".$this->table_name;
            if (!empty($parameters)) {
                $this->query .= " WHERE ";
                $conditions = $this->getConditions($parameters);
                $this->query .= $conditions['cadena'];
                $queryArray = array_merge($queryArray,$conditions['valores']);
            }
        }
        $this->query .= ";";
        $connection = new Connection($this->base_name);
        $result = $connection->query($this->query);
        if ($result) {
            return $result;
        } else {
            $this->error = $connection->getError();
            return null;
        }
    }
    /**
     * Function to get the conditions to be used in the query
     * @param array $parameters
     * @return array
     */
    private function getConditions(array $conditions): array {
        $cadena = "";
        $valores = [];
        if (isset($arreglo['condition']) && !empty($arreglo['condition'])) {
            foreach ($arreglo['condition'] as $indice => $cond) {
                if ($indice > 0) {
                    $separador = ($arreglo['separator'][($indice - 1)]) ?? null;
                    if (isset($separador) && !is_null($separador)) {
                        switch ($separador) {
                            case "Y":
                                $string .= " AND ";
                                break;
                            case "O":
                                $string .= " OR ";
                                break;
                        }
                    }
                }
                $string .= '`' . $cond['table'] . '`.`' . $cond['field'] . '`';
                switch ($cond['type']) {
                    case 'COMPARATIVE':
                        $string .= ' = ? ';
                        break;
                    case 'SIMILAR':
                        $string .= " LIKE CONCAT('%', ?, '%') ";
                        break;
                    case 'START_WITH':
                        $string .= " LIKE CONCAT(?, '%') ";
                        break;
                    case 'END_WITH':
                        $string .= " LIKE CONCAT('%', ?) ";
                        break;
                    case 'RANGE':
                        $string .= ' BETWEEN ? AND ? ';
                        break;
                    case 'NEGATIVE':
                        $string .= ' != ? ';
                        break;
                    case 'LESS_THAN':
                        $string .= ' < ? ';
                        break;
                    case 'MORE_THAN':
                        $string .= ' > ? ';
                        break;
                    case 'LESS_EQ_TO':
                        $string .= ' <= ? ';
                        break;
                    case 'MORE_EQ_TO':
                        $string .= ' >= ? ';
                        break;
                    case 'NOT_IN';
                        $string .= ' NOT IN (';
                        for ($ind = 0; $ind < count($cond['value']); $ind++) {
                            $string .= (($ind + 1) < count($cond['value'])) ? '?,' : '?';
                        }
                        $string .= ')';
                        break;
                    case 'IS_IN';
                        $string .= ' IN (';
                        for ($ind = 0; $ind < count($cond['value']); $ind++) {
                            $string .= (($ind + 1) < count($cond['value'])) ? '?,' : '?';
                        }
                        $string .= ')';
                        break;
                }
                if ($cond['type'] != 'RANGE' && $cond['type'] != 'NOT_IN') {
                    array_push($values, $cond['value']);
                } else {
                    foreach ($cond['value'] as $item) {
                        array_push($values, $item);
                    }
                }
            }
        }
        return ['cadena' => $string, 'valores' => $values];
    }

    /**
     * Función que realiza la insercion o modificacion de datos en la base de datos.
     * @param string $type Tipo de consulta a realizar.
     * @param array|null $data Arreglo de datos a ser utilizados en la consulta.
     * @return array
     */
    private function _setDbData(string $type = "insert", array | null $data = null): array
    {
        if (!is_null($data)) {
            $this->fields = (isset($data['fields'])) ? $data['fields'] : [];
            $this->conditions = (isset($data['conditions'])) ? $data['conditions'] : [];
            $this->order = (isset($data['order'])) ? $data['order'] : 'ASC';
            $this->orderby = (isset($data['orderby'])) ? $data['orderby'] : 'id';
        }
        return $this->setDBData($type, $this->fields, $this->values, $this->params);
    }

    /**
     * Función que devuelve los datos de la base de datos.
     * @param string $type Tipo de consulta a realizar.
     * @param array|null $data parametros a ser utilizados en la consulta.
     * @return array
     */
    private function _getDbData(array | null $data = null): array
    {
        if (!is_null($data)) {
            $this->fields = (isset($data['fields'])) ? $data['fields'] : [];
            $this->conditions = (isset($data['conditions'])) ? $data['conditions'] : [];
            $this->order = (isset($data['order'])) ? $data['order'] : 'ASC';
            $this->orderby = (isset($data['orderby'])) ? $data['orderby'] : 'id';
        }
        return $this->getDBData($this->fields, $this->limit, $this->order, $this->orderby);
    }

}