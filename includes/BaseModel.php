<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 04.04.16
 * Time: 22:30
 */

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

const DT_MYSQL_FORMAT = 'Y-m-d H:i:s';

class BaseModel extends Model
{
    private $_fieldTypes = ['serializable', 'bool', 'int', 'float', 'datetime', 'string'];
    protected $_serializable = [];
    protected $_bool = [];
    protected $_int = [];
    protected $_float = [];
    protected $_datetime = [];
    protected $_string = [];

    /** @var string */
    public $createdAt;
    /** @var string */
    public $updatedAt;

    public function toArray($columns = null): array
    {
        // your breakpoint here
        return parent::toArray($columns);
    }

    public function columnMap()
    {
        $result = [
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
        ];
        foreach ($this->_fieldTypes as $fieldType) {
            $fieldNames = "_$fieldType";
            foreach ($this->$fieldNames as $fieldName) {
                $result[$fieldName] = $this->_snakeToCamel($fieldName);
            }
        }
        return $result;
    }

    public function beforeValidation()
    {
        $this->_serialize();
    }

    public function beforeCreate()
    {
        $this->createdAt = date(DT_MYSQL_FORMAT);
    }

    public function beforeUpdate()
    {
        $this->updatedAt = date(DT_MYSQL_FORMAT);
    }

    public function beforeSave()
    {
        // your breakpoint here
    }

    public function prepareSave()
    {
        $this->updatedAt = date(DT_MYSQL_FORMAT);
    }

    public function onValidationFails()
    {
        // your breakpoint here
    }

    public function validation()
    {
        // your breakpoint here
    }

    public function afterFetch()
    {
        $this->_deserialize();
    }

    public function afterSave()
    {
        $this->_deserialize();
    }

    public function delete(): bool
    {
        return parent::delete();
    }

    public static function find($parameters = null): ResultsetInterface
    {
        $parameters = static::addCacheParameters($parameters, 'find');

        return parent::find($parameters);
    }

    public static function findFirst($parameters = null): ?ModelInterface
    {
        $parameters = static::addCacheParameters($parameters, 'findFirst');

        return parent::findFirst($parameters);
    }

    protected static function addCacheParameters($parameters = null, $method = '')
    {
        /*
        if (null !== $parameters) {
            if (true !== is_array($parameters)) {
                $parameters = [$parameters];
            }

            if (true !== isset($parameters['cache'])) {
                $parameters['cache'] = [
                    'key' => static::generateCacheKey($parameters, $method),
                    'lifetime' => 300,
                ];
            }
        }
*/
        return $parameters;
    }

    protected static function generateCacheKey(array $parameters, string $method)
    {
        $uniqueKey = explode('\\', get_called_class());
        $uniqueKey[] = $method;
        foreach ($parameters as $key => $value) {
            if (true === is_scalar($value)) {
                $uniqueKey[] = (ctype_alnum("$key") ? $key : bin2hex($key)) .
                    '_' . (ctype_alnum("$value") ? $value : bin2hex($value));
            } elseif (true === is_array($value)) {
                $uniqueKey[] = sprintf(
                    '%sI%sI',
                    $key,
                    static::generateCacheKey($value, $method)
                );
            }
        }
        $maxKeyLength = 100;
        $key = join('_', $uniqueKey);
        if (strlen($key) > $maxKeyLength) {
            $key = substr($key, 0, $maxKeyLength - 32) . md5($key);
        }
        return $key;
    }

    private function _snakeToCamel($snake)
    {
        return lcfirst(str_replace('_', '', ucwords($snake, '_')));
    }

    private function _serialize()
    {
        $this->_datetime[] = 'created_at';
        $this->_datetime[] = 'updated_at';
        foreach ($this->_serializable as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            $this->$fieldName = json_encode($this->$fieldName);
        }
        foreach ($this->_bool as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            $this->$fieldName = (int) $this->$fieldName;
        }
        foreach ($this->_int as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            if (is_null($this->$fieldName)) continue;
            $this->$fieldName = (int) $this->$fieldName;
        }
        foreach ($this->_float as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            if (is_null($this->$fieldName)) continue;
            $this->$fieldName = (float) $this->$fieldName;
        }
        foreach ($this->_datetime as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            if (is_null($this->$fieldName)) continue;
            $this->$fieldName = date(DT_MYSQL_FORMAT, strtotime($this->$fieldName));
        }
    }

    private function _deserialize()
    {
        $this->_datetime[] = 'created_at';
        $this->_datetime[] = 'updated_at';
        foreach ($this->_serializable as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            $this->$fieldName = is_null($this->$fieldName) ? null : json_decode($this->$fieldName);
        }
        foreach ($this->_bool as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            $this->$fieldName = (bool) $this->$fieldName;
        }
        foreach ($this->_int as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            if (is_null($this->$fieldName)) continue;
            $this->$fieldName = (int) $this->$fieldName;
        }
        foreach ($this->_float as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            if (is_null($this->$fieldName)) continue;
            $this->$fieldName = (float) $this->$fieldName;
        }
        foreach ($this->_datetime as $fieldName) {
            $fieldName = $this->_snakeToCamel($fieldName);
            $this->$fieldName = $this->$fieldName === '0000-00-00 00:00:00' ? null : date(DT_FORMAT, strtotime($this->$fieldName));
        }
    }

    /**
     * @param int $id
     * @return Model
     * @throws Exception
     */
    public static function getById($id)
    {
        $item = static::findFirst($id);
        if (!$item) {
            throw new \NotFoundException('Item is not found');
        }
        return $item;
    }

    /**
     * @param int $userId
     * @return Model
     */
    public static function getByUserId($userId)
    {
        return static::findFirst(['userId = ?0', 'bind' => $userId]);
    }

    /**
     * @param int $userId
     * @return Model[]
     */
    public static function getFilteredByUserId($userId)
    {
        $items = [];
        $all = static::find(['userId' => $userId]);
        foreach ($all as $item) {
            $items[] = $item;
        }
        return $items;
    }

    /**
     * @return Model[]
     */
    public static function getAll()
    {
        $items = [];
        $all = static::find();
        foreach ($all as $item) {
            $items[] = $item;
        }
        return $items;
    }

    /**
     * @param array $filter
     * @return Model[]
     */
    public static function getFilteredAll(array $filter = [])
    {
        $items = [];
        $all = static::find($filter);
        foreach ($all as $item) {
            $items[] = $item;
        }
        return $items;
    }
}
