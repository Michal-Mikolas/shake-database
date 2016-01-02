<?php 
namespace Shake\Database;

use Nette,
	Nette\DI\CompilerExtension,
	Nette\Database\Connection,
	Nette\PhpGenerator;


/**
 * Database\Extension
 *
 * @author  Michal Mikoláš <nanuqcz@gmail.com>
 * @package Shake
 */
class Extension extends CompilerExtension
{
	/** @var array */
	private $defaults = array(
		'database' => '@database.default',
		'factory' => array(
			'class' => 'Shake\Database\Orm\ClassMapFactory',
			'arguments' => array("App\\Model\\*Entity", "App\\Model\\*Table"),
		),
	);


	/**
	 * @return void
	 */
	public function loadConfiguration()
	{
		// 1) Load config
		$this->config = $this->getConfig($this->defaults);

		// 2) Add @shake.database.context service
		// Entity & Table factory
		$this->containerBuilder->addDefinition( $this->prefix('factory') )
			->setClass($this->config['factory']['class'], $this->config['factory']['arguments'])
			->setInject(TRUE);

		// Database\ORM context
		$this->containerBuilder->addDefinition( $this->prefix('context') )
			->setClass('Shake\\Database\\Orm\\Context', array(
				$this->config['database'] . '.context',
				'@' . $this->prefix('factory')
			))
			->setInject(TRUE);
	}

}