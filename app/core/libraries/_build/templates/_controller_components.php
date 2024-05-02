/** 
 * Function to create a new {name} in the database
 * 
 * @param array $data
 * @return array
 * @throws Exception
 */
public function create(array $data): array {
    if (empty($data)) {
        return $this->view("{name}/create");
    } else {
        $result = [];
        try {
            $result = $this->model->_add_($data);
            if (is_null($result)) {
                $error = {name}Model::getError();
                throw new Exception($error['message'],$error['code']);
            }
        } catch (Throwable $e) {
            $result = ['error' => $e->getMessage()];
        }
    }
    return $result;
}
/** 
 * Function to get a {name} from the database
 * 
 * @param array $data
 * @return array
 * @throws Exception
 */
public function read(array $data): array {
    $result = [];
    try {
        $result = $this->model->_get_($fields,$where);
        if (is_null($result)) {
            $error = {name}Model::getError();
            throw new Exception($error['message'],$error['code']);
        }
    } catch (Throwable $e) {
        $result = ['error' => $e->getMessage()];
    }
    return $result;
}
/**
 * Function to update a {name} in the database
 * 
 * @param array $data
 * @return array
 * @throws Exception
 */
public function update(array $data): array {
    $result = [];
    try {
        $result = $this->model->_update_($data);
        if (is_null($result)) {
            $error = {name}Model::getError();
            throw new Exception($error['message'],$error['code']);
        }
    } catch (Throwable $e) {
        $result = ['error' => $e->getMessage()];
    }
    return $result;
}
/**
 * Function to delete a {name} from the database
 * 
 * @param array $data
 * @return array
 * @throws Exception
 */
public function delete(array $data): array {
    $result = [];
    try {
        $result = $this->model->_delete_($data);
        if (is_null($result)) {
            $error = {name}Model::getError();
            throw new Exception($error['message'],$error['code']);
        }
    } catch (Throwable $e) {
        $result = ['error' => $e->getMessage()];
    }
    return $result;
}