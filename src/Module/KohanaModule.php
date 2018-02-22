<?php
namespace Codeception\Module;

use Codeception\TestInterface;
use Codeception\Lib\Connector\KohanaConnector;
use Codeception\Lib\Framework;
use Codeception\Lib\Interfaces\ActiveRecord;
use Codeception\Lib\Interfaces\PartedModule;
use Codeception\Lib\ModuleContainer;

/**
 *
 * This module allows you to run functional tests for Kohana 3.3.
 * It should **not** be used for acceptance tests.
 *
 * ## Status
 *
 * * Author: **Þói Juhasz**
 * * Stability: **alpha**
 *
 * ## Example
 *
 *     modules:
 *         enabled:
 *             - Kohana
 *

 */
class KohanaModule extends Framework implements ActiveRecord, PartedModule {

	/**
	 * @var  array
	 */
	public $config = [];

	/**
	 * Constructor.
	 *
	 * @param  ModuleContainer  $moduleContainer
	 * @param  array|NULL  $config
	 */
	public function __construct(ModuleContainer $moduleContainer, $config = NULL)
	{


		parent::__construct($moduleContainer);
	}

	/**
	 * @return  array
	 */
	public function _parts()
	{
		return ['orm'];
	}

	/**
	 * Initialize hook.
	 */
	public function _initialize()
	{
	}

	/**
	 * Before hook.
	 *
	 * @param \Codeception\TestInterface $test
	 */
	public function _before(TestInterface $test)
	{
		$this->client = new KohanaConnector;
		$this->client->setIndex('../public/index.php');
	}

	/**
	 * After hook.
	 *
	 * @param \Codeception\TestInterface $test
	 */
	public function _after(TestInterface $test)
	{
		$_SESSION = [];
		$_GET = [];
		$_POST = [];
		$_COOKIE = [];
		parent::_after($test);
	}

	/**
	 * Print out to the console
	 *
	 * @param var $var
	 */
	public function console_print($var)
	{
		fwrite(STDERR, print_r($var, TRUE));
	}

    public function seeHeader($header, $value = NULL)
    {
        $headers = $this->getActiveClient()->getInternalResponse()->getHeaders();

        $this->assertArrayHasKey($header, $headers);

        if ($value !== NULL)
        {
        	$this->assertEquals($headers[$header], $value);
        }
    }

	public function haveRecord($model, $attributes = [])
	{
		// TODO: Implement haveRecord() method.

	}

	public function seeRecord($model, $attributes = [])
	{
		// TODO: Implement seeRecord() method.
	}

	public function dontSeeRecord($model, $attributes = [])
	{
		// TODO: Implement dontSeeRecord() method.
	}

	public function grabRecord($model, $attributes = [])
	{
		// TODO: Implement grabRecord() method.
	}

	protected function getActiveClient()
	{

		$method = new \ReflectionMethod($this, 'getRunningClient');
		$method->setAccessible(TRUE);

		return $method->invoke($this);

	}
}
