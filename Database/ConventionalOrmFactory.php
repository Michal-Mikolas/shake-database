<?php
namespace Shake\Database;

use Nette;


/**
 * Shake\Database\IOrmFactory
 * Factory for ORM objects creation. 
 *
 * @package Shake
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
class ConventionalOrmFactory implements IOrmFactory
{
	/** @var string */
	private $rowClassMap;

	/** @var string */
	private $selectionClassMap;



	public function __construct($rowClassMap = '*Entity', $selectionClassMap = '*Collection')
	{
		$this->rowClassMap = $rowClassMap;
		$this->selectionClassMap = $selectionClassMap;
	}



	/**
	 * @param Nette\Database\Table\Selection
	 * @return Nette\Database\Table\IRowContainer
	 */
	public function createSelection(Nette\Database\Table\Selection $selection)
	{
		// 1) Get selection class name
		$tableName = $selection->getName();
		$className = $this->expand($this->selectionClassMap, $tableName);

		if (!class_exists($className))
			$className = '\Shake\Database\Selection';

		// 2) Create & check
		$class = new $className($selection, $this);
		
		if (!($class instanceof Nette\Database\Table\IRowContainer))
			throw new Nette\InvalidStateException("Class '$className' not implements Nette\Database\Table\IRowContainer interface.");

		return $class;
	}



	/**
	 * @param Nette\Database\Table\ActiveRow
	 * @return Nette\Database\Table\IRow
	 */
	public function createRow(Nette\Database\Table\ActiveRow $row)
	{
		// 1) Get selection class name
		$tableName = $row->getTable()->getName();
		$className = $this->expand($this->rowClassMap, $tableName);

		if (!class_exists($className))
			$className = '\Shake\Database\ActiveRow';

		// 2) Create & check
		$class = new $className($row, $this);
		
		if (!($class instanceof Nette\Database\Table\IRow))
			throw new Nette\InvalidStateException("Class '$className' not implements Nette\Database\Table\IRow interface.");

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