<?php


namespace Ouzo\Tools\Model\Template;

use Ouzo\Config;
use Ouzo\Db;
use Ouzo\Tools\Model\Template\Dialect\Dialect;
use Ouzo\Utilities\Arrays;

class Generator
{

    private $_tableName;
    private $_className;
    private $_adapter;

    function __construct($tableName, $className = null)
    {
        $this->_tableName = $tableName;
        $this->_className = $className;
        $this->_adapter = $this->dialectAdapter();
    }

    private function _thisNamespace()
    {
        $thisReflection = new \ReflectionClass($this);
        return $thisReflection->getNamespaceName();
    }

    private function _objectShortClassName($object)
    {
        $objectReflection = new \ReflectionClass($object);
        return $objectReflection->getShortName();
    }

    /**
     * @return Dialect
     */
    public function dialectAdapter()
    {
        $dbDialectFullClassPath = Config::getValue('sql_dialect');
        $dbDialectObject = new $dbDialectFullClassPath();
        $dbDialectShortName = $this->_objectShortClassName($dbDialectObject);
        $selfClassPath = $this->_thisNamespace($this);
        $generatorDialect = "$selfClassPath\\Dialect\\$dbDialectShortName";
        if (!class_exists($generatorDialect)) {
            throw new GeneratorException("Model generator for '$dbDialectShortName' does not exists.");
        }
        return new $generatorDialect($this->_tableName);
    }

    public function getTemplateClassName()
    {
        return '';
    }

    public function classTemplate()
    {
        $classStub = new ClassStub();
        $classStub->addPlaceholderReplacement('sequence', $this->_adapter->sequence())
            ->addPlaceholderReplacement('primary', $this->_adapter->primaryKey())
            ->addPlaceholderReplacement('table', $this->_adapter->tableName())
            ->addPlaceholderReplacement('class', $this->getTemplateClassName());
        Arrays::map($this->_adapter->columns(), array($classStub, 'addColumn'));
        return $classStub->contents();
    }

}

class GeneratorException extends \Exception
{
}