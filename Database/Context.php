<?php
namespace Shake\Database;

use Nette,
	Nette\Object;


/**
 * Shake\Database\Context
 * Enhanced Nette\Database\Context with lightweight ORM features and more.
 *
 * @package Shake
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 */
class Context extends Object
{
	/** @var Nette\Database\Context */
	private $context;
	
	/** @var IOrmFactory */
	private $factory;
	
	/** @var int $transactionDepth  Depth for nested transactions */
	private $transactionDepth = 0;
	


	/**
	 * @param Nette\Database\Context
	 * @param IOrmFactory
	 */
	public function __construct(Nette\Database\Context $context, IOrmFactory $factory)
	{
		$this->context = $context;
		$this->factory = $factory;
	}



	/********************* ORM *********************/



	/**
	 * @param string
	 * @return Nette\Database\Table\IRowContainer
	 */
	public function table($table)
	{
		$table = $this->context->table($table);

		return $this->factory->createSelection($table);
	}



	/**
	 * @param string
	 * @param array|NULL
	 * @return mixed
	 */
	public function __call($name, $args = array())
	{
		return call_user_func_array(array($this->context, $name), $args);
	}



	/********************* Transactions *********************/



	/**
	 * @return bool
	 */
	public function beginTransaction()
	{
		$this->transactionDepth++;

		if ($this->transactionDepth == 1) {
			return $this->context->beginTransaction();
		} else {
			return TRUE;
		}
	}



	/**
	 * @return bool
	 */
	public function commit()
	{
		$this->transactionDepth--;

		if ($this->transactionDepth == 0) {
			return $this->context->commit();
		} else {
			return TRUE;
		}
	}



	/**
	 * @return bool
	 */
	public function rollBack()
	{
		$this->transactionDepth = 0;

		return $this->context->rollBack();
	}

}