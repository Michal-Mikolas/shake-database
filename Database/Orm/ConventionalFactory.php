<?php
namespace Shake\Database\Orm;

use Nette;
use Shake\Utils\Strings;


/**
 * Shake\Database\Orm\ConventionalFactory
 * Factory for ORM objects creation. 
 *
 * @package Shake
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
class ConventionalFactory implements IFactory
{
	/** @var string */
	private $entityClassMap;

	/** @var string */
	private $tableClassMap;



	public function __construct($entityClassMap = '*Entity', $tableClassMap = '*Table')
	{
		$this->entityClassMap = $entityClassMap;
		$this->tableClassMap = $tableClassMap;
	}



	/**
	 * @param Nette\Database\Table\Selection
	 * @return Shake\Database\Orm\Table
	 */
	public function createTable(Nette\Database\Table\Selection $selection)
	{
		// 1) Get selection class name
		$className = Strings::toPascalCase( $selection->getName() );
		$className = $this->expand($this->tableClassMap, $className);

		if (!class_exists($className))
			$className = 'Shake\\Database\\Orm\\Table';

		// 2) Create & check
		$class = new $className($selection, $this);
		
		if (!($class instanceof \Shake\Database\Orm\Table))
			throw new Nette\InvalidStateException("Class '$className' not inherits Shake\\Database\\Orm\\Table class.");

		return $class;
	}



	/**
	 * @param Nette\Database\Table\ActiveRow
	 * @return Shake\Database\Orm\Entity
	 */
	public function createEntity(Nette\Database\Table\ActiveRow $row)
	{
		// 1) Get selection class name
		$className = Strings::toPascalCase( $row->getTable()->getName() );
		$className = $this->expand($this->entityClassMap, $className);

		if (!class_exists($className))
			$className = 'Shake\\Database\\Orm\\Entity';

		// 2) Create & check
		$class = new $className();
		$class->setRow($row);
		$class->setFactory($this);
		
		if (!($class instanceof \Shake\Database\Orm\Entity))
			throw new Nette\InvalidStateException("Class '$className' not inherits Shake\\Database\\Orm\\Entity class.");

		return $class;
	}



	/**
	 * @param string  for example 'Model\*Entity'
	 * @param string  for example 'article'
	 * @return string  for example 'Model\ArticleEntity'
	 */
	private function expand($map, $name)
	{
		$name = ucfirst($name);
		$name = str_replace('*', $name, $map);

		return $name;
	}

}