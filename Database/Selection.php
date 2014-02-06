<?php
namespace Shake\Database;

use Nette,
	Nette\Object,
	Nette\Database\Table\IRowContainer;


/**
 * Shake\Database\Selection
 * Enhanced Nette\Database\Table\Selection with lightweight ORM features.
 *
 * @package Shake
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
class Selection extends Object implements \Iterator, IRowContainer, \ArrayAccess, \Countable
{
	/** @var Nette\Database\Table\Selection */
	private $selection;
	
	/** @var IOrmFactory */
	private $factory;
	


	/**
	 * @param Nette\Database\Table\Selection
	 * @param IOrmFactory
	 */
	public function __construct(Nette\Database\Table\Selection $selection, IOrmFactory $factory)
	{
		$this->selection = $selection;
		$this->factory = $factory;
	}



	/********************* ORM *********************/



	/**
	 * @param string
	 * @return Nette\Database\Table\IRow|FALSE
	 */
	public function get($key)
	{
		$result = $this->selection->get($key);

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createRow($result);
		} else {
			return $result;
		}
	}



	/**
	 * @return Nette\Database\Table\IRow|FALSE
	 */
	public function fetch()
	{
		$result = $this->selection->fetch();

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createRow($result);
		} else {
			return $result;
		}
	}



	/**
	 * @return Nette\Database\Table\IRow[]
	 */
	public function fetchAll()
	{
		$rows = $this->selection->fetchAll();

		$fetchAll = array();
		foreach ($rows as $row) {
			$fetchAll[] = $this->factory->createRow($result);
		}

		return $fetchAll;
	}



	/**
	 * @param string|NULL
	 * @param mixed|NULL
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL)
	{
		return $this->selection->fetchPairs($key, $value);
	}



	/**
	 * @param string
	 * @return self
	 */
	public function select($columns)
	{
		call_user_func_array(array($this->selection, 'select'), func_get_args());

		return $this;
	}



	/**
	 * @param mixed
	 * @return self
	 */
	public function wherePrimary($key)
	{
		$this->selection->wherePrimary($key);

		return $this;
	}



	/**
	 * @param string
	 * @param mixed
	 * @return self
	 */
	public function where($condition, $parameters = array())
	{
		call_user_func_array(array($this->selection, 'where'), func_get_args());

		return $this;
	}



	/**
	 * @param string
	 * @return self
	 */
	public function order($columns)
	{
		call_user_func_array(array($this->selection, 'order'), func_get_args());

		return $this;
	}



	/**
	 * @param int
	 * @param int|NULL
	 * @return self
	 */
	public function limit($limit, $offset = NULL)
	{
		$this->selection->limit($limit, $offset);

		return $this;
	}



	/**
	 * @return 
	 */
	public function page($page, $itemsPerPage, & $numOfPages = NULL)
	{
		$this->selection->page($page, $itemsPerPage, $numOfPages);

		return $this;
	}



	/**
	 * @param string
	 * @return self
	 */
	public function group($columns)
	{
		call_user_func_array(array($this->selection, 'group'), func_get_args());

		return $this;
	}



	/**
	 * @param string
	 * @return self
	 */
	public function having($having)
	{
		call_user_func_array(array($this->selection, 'having'), func_get_args());

		return $this;
	}



	/**
	 * @param array|\Traversable|Nette\Database\Table\Selection
	 * @return Nette\Database\Table\IRow|bool|int
	 */
	public function insert($data)
	{
		$result = $this->selection->insert($data);

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createRow($result);
		} else {
			return $result;
		}
	}



	/**
	 * @param string
	 * @param string
	 * @param string
	 * @return Nette\Database\Table\IRowContainer|array
	 */
	public function getReferencedTable($table, $column, $checkPrimaryKey)
	{
		$result = $this->selection->getReferencedTable($table, $column, $checkPrimaryKey);

		if ($result instanceof Nette\Database\Table\IRowContainer) {
			return $this->factory->createSelection($result);
		} else {
			return $result;
		}
	}



	/**
	 * @param string
	 * @param array|NULL
	 * @return mixed
	 */
	public function __call($name, $args = array())
	{
		return call_user_func_array(array($this->selection, $name), $args);
	}



	/********************* interface Countable *********************/



	/**
	 * @param  string|NULL
	 * @return int
	 */
	public function count($column = NULL)
	{
		return $this->selection->count();
	}



	/********************* interface Iterator *********************/



	/**
	 * @return void
	 */
	public function rewind()
	{
		$this->selection->rewind();
	}



	/**
	 * @return Nette\Database\Table\IRow|FALSE
	 */
	public function current()
	{
		$result = $this->selection->current();

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createRow($result);
		} else {
			return $result;
		}
	}



	/**
	 * @return string
	 */
	public function key()
	{
		return $this->selection->key();
	}



	/**
	 * @return void
	 */
	public function next()
	{
		$this->selection->next();
	}



	/**
	 * @return bool
	 */
	public function valid()
	{
		return $this->selection->valid();
	}



	/********************* interface ArayAccess *********************/



	/**
	 * @param string
	 * @param Nette\Database\Table\IRow
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->selection->offsetSet($key, $value);
	}



	/**
	 * @param string
	 * @return Nette\Database\Table\IRow|NULL
	 */
	public function offsetGet($key)
	{
		$result = $this->selection->offsetGet($key);

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createRow($result);
		} else {
			return $result;
		}
	}



	/**
	 * @param string
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->selection->offsetExists($key);
	}



	/**
	 * @param string
	 * @return void
	 */
	public function offsetUnset($key)
	{
		$this->selection->offsetUnset($key);
	}

}