<?php
namespace app\lib\base;


class Model
{
    /**
     * @var Database
     */
    protected $_dbh = null;
    public $_table = "";
    protected $_primaryKey = 'id';

    public $timestamps = false;

    public function __construct()
    {
        // starts the connection to the database
        $this->_dbh = Database::openConnection();

        $this->init();
    }

    public function init()
    {

    }

    /**
     * @return static
     */
    public static function model()
    {
        $className = get_called_class();

        return new $className();
    }

    /**
     * @return Database
     */
    public function getDb()
    {
        if ($this->_dbh == null) {
            $this->_dbh = Database::openConnection();
        }
        return $this->_dbh;
    }

    /**
     * Sets the database table the model is using
     * @param string $table the table the model is using
     */
    protected function _setTable($table)
    {
        $this->_table = $table;
    }

    public function fetchOne($id)
    {
        $sql = 'select * from ' . $this->_table;
        $sql .= ' where id = ?';

        $statement = $this->_dbh->prepare($sql);
        $statement->execute(array($id));

        return $statement->fetchObject(get_class($this));
    }

    /**
     * Get one row
     * @param string $customWhere
     * @return mixed
     */
    public function one($customWhere = '', $asArray = false)
    {
        $sql = 'select * from ' . $this->_table. ' ' . $customWhere;

        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        if ($asArray) {
            return  $this->_dbh->fetchAssociative();
        }
        return $statement->fetchObject(get_class($this));
    }

    /**
     * @param string $customWhere
     * @return array
     */
    public function fetchAll($customWhere = '', $asArray = false)
    {
        $this->_dbh->getAll($this->_table . " $customWhere");
        if ($asArray) {
            return  $this->_dbh->fetchAllAssociative();
        }
        return $this->_dbh->getStatement()->fetchAll(\PDO::FETCH_CLASS, get_class($this));
    }

    /**
     * @param string $customWhere
     * @return mixed
     */
    public static function all($customWhere = '')
    {
        $className = get_called_class();
        $model = new $className();
        return $model->fetchAll($customWhere);
    }

    /**
     * Saves the current data to the database. If an key named "id" is given,
     * an update will be issued.
     * @param array $data the data to save
     * @return int the id the data was saved under
     */
    public function save($data = array())
    {
        $sql = '';

        $values = array();

        if (!$this->isNewModel()) {

            $sql = 'update ' . $this->_table . ' set ';
            if ($this->timestamps) {
                $data['updated_at'] = time();
            }

            $first = true;
            foreach ($data as $key => $value) {
                if ($key != 'id') {
                    $sql .= ($first == false ? ',' : '') . ' ' . $key . ' = ?';

                    $values[] = $value;

                    $first = false;
                }
            }

            // adds the id as well
            $values[] = $this->{$this->_primaryKey};

            $sql .= ' where '.$this->_primaryKey.' = ?';// . $data['id'];

            $statement = $this->_dbh->prepare($sql);
           // $this->map($data);

            return $statement->execute($values);
        } else {
            if ($this->timestamps) {
                $data['created_at'] = $data['updated_at'] = time();
            }
            $keys = array_keys($data);

            $sql = 'insert into ' . $this->_table . '(';
            $sql .= implode(',', $keys);
            $sql .= ')';
            $sql .= ' values (';

            $dataValues = array_values($data);
            $first = true;
            foreach ($dataValues as $value) {
                $sql .= ($first == false ? ',?' : '?');

                $values[] = $value;

                $first = false;
            }

            $sql .= ')';

            $statement = $this->_dbh->prepare($sql);

            if ($statement->execute($values)) {

                $this->map($data);
                return $this->_dbh->lastInsertedId();
            }
        }

        return false;
    }

    /**
     * Check model is created
     * @return bool
     */
    public function isNewModel()
    {
        if (key_exists($this->_primaryKey, $this) && $this->{$this->_primaryKey}) {
            return false;
        }

        return true;
    }

    public function map($data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Deletes a single entry
     * @param int $id the id of the entry to delete
     * @return boolean true if all went well, else false.
     */
    public function delete($id)
    {
        $statement = $this->_dbh->prepare("delete from " . $this->_table . " where id = ?");
        return $statement->execute(array($id));
    }

    /**
     * Count rows
     * @param string $where
     * @return int
     */
    public function count($where = '')
    {
        $this->_dbh->prepare('SELECT COUNT(*) AS count FROM '.$this->_table.' '.$where);
        $this->_dbh->execute();
        return (int)$this->_dbh->fetchAssociative()["count"];
    }
}
