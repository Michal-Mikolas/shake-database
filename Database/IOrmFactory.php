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
interface IOrmFactory
{

	/**
	 * @param Nette\Database\Table\Selection
	 * @return Nette\Database\Table\IRowContainer
	 */
	public function createSelection(Nette\Database\Table\Selection $selection);



	/**
	 * @param Nette\Database\Table\ActiveRow
	 * @return Nette\Database\Table\IRow
	 */
	public function createRow(Nette\Database\Table\ActiveRow $row);

}