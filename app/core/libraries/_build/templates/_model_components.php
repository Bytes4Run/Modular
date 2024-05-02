private {type} ${prop};
// Constructor
public function __construct(int $id = null) {
    parent::__construct();
    $this->table = "{name}";
    $this->_setTable($this->table);
    $this->error = null;
    if (!is_null($id) && $id > 0) {
        $this->_init_Model($id);
    }
}
// Getters and Setters
/**
 * Function to get any value propperty from the Model
 * 
 * @param string $prop
 * @param return mixed
 */
public function __get(string $name):mixed {
    $result = null;
    if (property_exists($this, $name)) {
        if (in_array($name, ['id'])) {
            $result = intval($this->$name);
        } else {
            $result = $this->$name;
        }
    }
    return $result;
}
/**
 * Function to set any value propperty from the Model
 *
 * @param string $name
 * @param mixed $value
 * @return void
 * @throws Exception
*/
public function __set(string $name, $value): void {
    if (property_exists($this, $name)) {
        if ($name == 'created_at' || $name == 'updated_at') {
            if (!is_null($value) && !empty($value)) {
                $this->$name = new DateTime($value);
            } else {
                $this->$name = new DateTime();
            }
        } else {
            $this->$name = $value;
        }
    }
}
// Get error and set error
/** 
 * Function to set any error occurring on the Model
 * 
 * @param array $error
 * @return void
 */
private function __setError(array $error): void {
    if (!is_null($this->error) && !empty($this->error)) {
        $this->error[] = $error;
    } else {
        $this->error = $error;
    }
}
/** 
 * Function to get the error from the Model
 * 
 * @return null|array
 * @throws Exception
 */
public static function getError (): ?array {
    return self::$error;
}
// Add register
/** 
 * Function to add records to the Model table database
 * 
 * @param null|array $data
 * @return null|${name}Model
 * @throws Exception
 */
public function _add_ (?array $data): null|{name}Model {
    if (!is_null($data) && !empty($sata)) {
        foreach($data as $key => $item) {
            $this->__set($key,$item);
        }
    }
    return $this->createModel();
}
// Get register
/** 
 * Function to get records from the Model table database
 * 
 * @param string|array $fields Fields to get from the database
 * @param string|array $where Conditions to be satisfied
 * @return null|array|{name}Model
 * @throws Exception
 */
public function _get_ (string|array $fields = '*', string|array $where = []): null|array|{name}Model {
    return $this->getModel($fields,$where);
}
// Edit register
/** 
 * Function to edit records from the Model table database
 * 
 * @param null|int $id Field identifiying the record from the database
 * @param array $data Data to be updated in the record
 * @return bool
 * @throws Exception
 */
public function _edit_ (?int $id = null, array $data = []): bool {
    if (!is_null($id) && $id > 0) {
        $this->__set('id',$id);
    }
    if (!empty($data)) {
        foreach($data as $key => $item) {
            $this->__set($key,$item);
        }
    }
    return $this->updateModel();
}
// delete register
/** 
 * Function to delete records from the Model table database
 * 
 * @param null|int $id Field to delete from the database
 * @return bool
 * @throws Exception
 */
public function _remove_ (?int $id = null): bool {
    if (!is_null($id) && $id > 0) {
        $this->__set('id',$id);
    }
    return $this->deleteModel();
}
// private functions
/** 
 * Function to initialize the Model
 * 
 * @param int $id
 * @return void
 * @throws Exception
 */
private function _init_Model (int $id): void {
    $this->id = $id;
    $this->getModel();
}
// Create model
/** 
 * Function to create a new Model
 * 
 * @return null|{name}Model
 * @throws Exception
 */
private function createModel (): null|{name}Model {
    $result = null;
    $query = ['fields' => [{fields}], 'values' => [{values}]];
    try {
        $result = $this->insert($query);
        if (empty($result['data'])) {
            $error = Context::getError();
            throw new Exception($error['message'],$error['code']);
        } else {
            $this->__set($result['data']);
        }
    } catch (Throwable $e) {
        $this->__setError(['message' => $e->getMessage(),'code'=>$e->getCode()]);
        return null;
    }
    return $this;
}
// Select model
/** 
 * Function to select a new record from the Model
 * 
 * @return null|array|{name}Model
 * @throws Exception
 */
private function createModel (): null|{name}Model {
    $result = null;
    $query = ['fields' => [{fields}], 'values' => [{values}]];
    try {
        $result = $this->insert($query);
        if (empty($result['data'])) {
            $error = Context::getError();
            throw new Exception($error['message'],$error['code']);
        } else {
            $this->__set($result['data']);
        }
    } catch (Throwable $e) {
        $this->__setError(['message' => $e->getMessage(),'code'=>$e->getCode()]);
        return null;
    }
    return $this;
}