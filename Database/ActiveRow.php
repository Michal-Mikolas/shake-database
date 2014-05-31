<?php
namespace Shake\Database;

use Nette, 
	Nette\Object,
	Nette\Utils\ObjectMixin,
	Nette\InvalidStateException,
	Nette\MemberAccessException;


/**
 * Shake\Database\ActiveRow
 * Enhanced Nette\Database\Table\ActiveRow with lightweight ORM features.
 *
 * @package Shake
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
class ActiveRow extends Object implements \IteratorAggregate, Nette\Database\Table\IRow
{
	/** @var Nette\Database\Table\ActiveRow */
	private $row;

	/** @var array */
	private $data = array();
	
	/** @var IOrmFactory */
	private $factory;
	


	/**
	 * @param Nette\Database\Table\ActiveRow
	 * @param IOrmFactory
	 */
	public function __construct(Nette\Database\Table\ActiveRow $row = NULL, IOrmFactory $factory)
	{
		$this->setRow($row);

		$this->factory = $factory;
	}



	public function setRow(Nette\Database\Table\ActiveRow $row = NULL)
	{
		$this->row = $row;
	}



	public function getRow()
	{
		if ($this->row instanceof Nette\Database\Table\ActiveRow) {
			return $this->row;

		} else {
			throw new InvalidStateException("Cant use this until '\$row' is set.");
		}
	}



	/********************* ORM *********************/



	/**
	 * @param  string
	 * @param  string
	 * @return IRow|NULL
	 */
	public function ref($key, $throughColumn = NULL)
	{
		$result = $this->getRow()->ref($key, $throughColumn);

		if ($result instanceof Nette\Database\Table\IRow) {
			return $this->factory->createRow($result);
		} else {
			return $result;
		}
	}



	/**
	 * @param  string
	 * @param  string
	 * @return GroupedSelection
	 */
	public function related($key, $throughColumn = NULL)
	{
		$selection = $this->getRow()->related($key, $throughColumn);
		
		return $this->factory->createSelection($selection);
	}



	/**
	 * @param string
	 * @param array|NULL
	 * @return mixed
	 */
	public function __call($name, $args = array())
	{
		return call_user_func_array(array($this->getRow(), $name), $args);
	}



	/********************* interface IRow *********************/



	/**
	 * @param Nette\Database\Table\Selection
	 * @return void
	 */
	public function setTable(Nette\Database\Table\Selection $selection)
	{
		$this->getRow()->setTable($selection);
	}



	/**
	 * @return Nette\Database\Table\IRowContainer
	 */
	public function getTable()
	{
		return $this->getRow()->getTable();
	}



	/**
	 * @param bool
	 * @return mixed
	 */
	public function getPrimary($need = TRUE)
	{
		return $this->getRow()->getPrimary($need);
	}



	/**
	 * @param bool
	 * @return string
	 */
	public function getSignature($need = TRUE)
	{
		return $this->getRow()->getSignature($need);
	}



	/********************* interface IteratorAggregate ****************d*g**/



	public function getIterator()
	{
		return $this->getRow()->getIterator();
	}



	/********************* interface ArayAccess & magic accessors *********************/



	/**
	 * @param string
	 * @param string
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->__set($key, $value);
	}



	/**
	 * @param string
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->__get($key);
	}



	/**
	 * @param string
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->__isset($key);
	}



	/**
	 * @param string
	 * @return void
	 */
	public function offsetUnset($key)
	{
		$this->__unset($key);
	}



	/**
	 * @param string
	 * @param mixed
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}



	/**
	 * @param string
	 * @return mixed
	 */
	public function &__get($key)
	{
		// Get data
		if (isset($this->data[$key])) {
			$result = $this->data[$key];

		} elseif (ObjectMixin::has($this, $key) || !isset($this->row)) {
			return ObjectMixin::get($this, $key);

		} else {
			$result = $this->row->__get($key);
		}

		// Create entity
		if ($result instanceof Nette\Database\Table\IRow) {
			$row = $this->factory->createRow($result);
			return $row;
		} else {
			return $result;
		}
	}



	/**
	 * @param string
	 * @return bool
	 */
	public function __isset($key)
	{
		return isset($this->data[$key]) 
			|| ObjectMixin::has($this, $key) 
			|| (isset($this->row) && $this->row->__isset($key));
	}



	/**
	 * @param string
	 * @return void
	 */
	public function __unset($key)
	{
		if (isset($this->data[$key])) {
			unset($this->data[$key]);

		} elseif (ObjectMixin::has($this, $key)) {
			throw new InvalidStateException("Can't unset '$key' property method.");

		} elseif (!isset($this->row)) {
			throw new MemberAccessException("Can't unset undeclared property '$key'.");

		} else {
			$this->row->__unset($key);
		}
	}

}