<?php
/**
 *
 * Advertisement management. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\ads\tests\event;

class main_listener_base extends \phpbb_database_test_case
{
	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\config\db_text */
	protected $config_text;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\template\template */
	protected $template;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\user */
	protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var string ads_table */
	protected $ads_table;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\ads\ad\manager */
	protected $manager;

	/** @var \phpbb\ads\location\manager */
	protected $location_manager;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\controller\helper */
	protected $controller_helper;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\request\request */
	protected $request;

	/** @var \PHPUnit\Framework\MockObject\MockObject|\phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $ad_locations_table;

	/** @var string */
	protected $ad_group_table;

	/** @var array */
	protected $locations;

	/**
	* {@inheritDoc}
	*/
	protected static function setup_extensions()
	{
		return array('phpbb/ads');
	}

	/**
	* {@inheritDoc}
	*/
	public function getDataSet()
	{
		return $this->createXMLDataSet(__DIR__ . '/../fixtures/ad.xml');
	}

	/**
	* {@inheritDoc}
	*/
	protected function setUp(): void
	{
		parent::setUp();

		global $user, $phpbb_path_helper, $phpbb_root_path, $phpEx, $phpbb_dispatcher;

		$phpbb_path_helper = $this->getMockBuilder('\phpbb\path_helper')
			->disableOriginalConstructor()
			->getMock();
		$phpbb_dispatcher = new \phpbb_mock_event_dispatcher();
		$lang_loader = new \phpbb\language\language_file_loader($phpbb_root_path, $phpEx);
		$this->language = new \phpbb\language\language($lang_loader);
		$user = new \phpbb\user($this->language, '\phpbb\datetime');
		$request = $this->getMockBuilder('\phpbb\request\request')
			->disableOriginalConstructor()
			->getMock();
		$config = new \phpbb\config\config(array());
		$template = $this->getMockBuilder('\phpbb\template\template')
			->disableOriginalConstructor()
			->getMock();
		$this->ads_table = 'phpbb_ads';
		$this->ad_locations_table = 'phpbb_ad_locations';
		$this->ad_group_table = 'phpbb_ad_group';
		// Location types
		$this->locations = array(
			'above_footer',
			'above_header',
			'after_footer_navbar',
			'after_header_navbar',
			'after_first_post',
			'after_not_first_post',
			'after_posts',
			'after_profile',
			'after_quickreply',
			'before_posts',
			'before_profile',
			'before_quickreply',
			'below_footer',
			'below_header',
			'pop_up',
			'slide_up',
		);
		$location_types = array();
		foreach ($this->locations as $type)
		{
			$class = "\\phpbb\\ads\\location\\type\\$type";
			if ($type === 'pop_up')
			{
				$location_types['phpbb.ads.location.type.' . $type] = new $class($user, $this->language, $request, $config, $template);
			}
			else
			{
				$location_types['phpbb.ads.location.type.' . $type] = new $class($user, $this->language);
			}
		}

		// Load/Mock classes required by the listener class
		$this->template = $this->getMockBuilder('\phpbb\template\template')
			->disableOriginalConstructor()
			->getMock();
		$this->user = $user;
		$this->config = new \phpbb\config\config(array('phpbb_ads_adblocker_message' => '0'));
		$this->manager = new \phpbb\ads\ad\manager($this->new_dbal(), $this->config, $this->ads_table, $this->ad_locations_table, $this->ad_group_table);
		$this->location_manager = new \phpbb\ads\location\manager($location_types);
		$this->controller_helper = $this->controller_helper = $this->getMockBuilder('\phpbb\controller\helper')
			->disableOriginalConstructor()
			->getMock();
		$this->request = $request;
		$this->cache = $this->getMockBuilder('\phpbb\cache\driver\dummy')
			->disableOriginalConstructor()
			->getMock();
		$this->php_ext = $phpEx;
	}

	/**
	* Get the event listener
	*
	* @return \phpbb\ads\event\main_listener
	*/
	protected function get_listener()
	{
		return new \phpbb\ads\event\main_listener(
			$this->language,
			$this->template,
			$this->user,
			$this->config,
			$this->manager,
			$this->location_manager,
			$this->controller_helper,
			$this->request,
			$this->cache,
			$this->php_ext
		);
	}
}
