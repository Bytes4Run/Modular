/**
 * Class {name}
 * @author {author}
 */
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