<?php
namespace Shake\Database;

use Nette, 
	Nette\Object,
	Nette\ObjectMixin;


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
	
	/** @var IOrmFactory */
	private $factory;
	


	/**
	 * @param Nette\Database\Table\ActiveRow
	 * @param IOrmFactory
	 */
	public function __construct(Nette\Database\Table\ActiveRow $row, IOrmFactory $factory)
	{
		$this->row = $row;
		$this->factory = $factory;
	}



	/********************* ORM *********************/



	/**
	 * @param  string
	 * @param  string
	 * @return IRow|NULL
	 */
	public function ref($key, $throughColumn = NULL)
	{
		$result = $this->row->ref($key, $throughColumn);

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
		$selection = $this->row->related($key, $throughColumn);
		
		return $this->factory->createSelection($selection);
	}



	/**
	 * @param string
	 * @param array|NULL
	 * @return mixed
	 */
	public function __call($name, $args = array())
	{
		return call_user_func_array(array($this->row, $name), $args);
	}



	/********************* interface IRow *********************/



	/**
	 * @param Nette\Database\Table\Selection
	 * @return void
	 */
	public function setTable(Nette\Database\Table\Selection $selection)
	{
		$this->row->setTable($selection);
	}



	/**
	 * @return Nette\Database\Table\IRowContainer
	 */
	public function getTable()
	{
		return $this->row->getTable();
	}



	/**
	 * @param bool
	 * @return mixed
	 */
	public function getPrimary($need = TRUE)
	{
		return $this->row->getPrimary($need);
	}



	/**
	 * @param bool
	 * @return string
	 */
	public function getSignature($need = TRUE)
	{
		return $this->row->getSignature($need);
	}



	/********************* interface IteratorAggregate ****************d*g**/


	public function getIterator()
	{
		return $this->row->getIterator();
	}



	/********************* interface ArayAccess & magic accessors *********************/



	/**
	 * @param string
	 * @param Nette\Database\Table\IRow
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->row->offsetSet($key, $value);
	}



	/**
	 * @param string
	 * @return Nette\Database\Table\IRow|NULL
	 */
	public function offsetGet($key)
	{
		$result = $this->row->offsetGet($key);

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
		return $this->row->offsetExists($key);
	}



	/**
	 * @param string
	 * @return void
	 */
	public function offsetUnset($key)
	{
		$this->row->offsetUnset($key);
	}



	/**
	 * @param string
	 * @param mixed
	 * @return void
	 */
	public function __set($key, $value)
	{
		return $this->row->__set($key, $value);
	}



	/**
	 * @param string
	 * @return mixed
	 */
	public function &__get($key)
	{
		if ($this->__isset($key) || !ObjectMixin::has($this, $key)) {
			$result = $this->row->__get($key);
		} else {
			return ObjectMixin::get($this, $key);
		}

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
		return $this->row->__isset($key);
	}



	/**
	 * @param string
	 * @return void
	 */
	public function __unset($key)
	{
		return $this->row->__unset($key);
	}

}