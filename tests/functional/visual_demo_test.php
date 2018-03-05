<?php
/**
 *
 * Advertisement management. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\ads\tests\functional;

/**
 * @group functional
 */
class visual_demo_test extends functional_base
{
	/**
	 * {@inheritDoc}
	 */
	public function setUp()
	{
		parent::setUp();

		$this->add_lang_ext('phpbb/ads', array(
			'common',
		));
	}

	public function test_visual_demo()
	{
		$crawler = self::request('GET', 'phpbbads-visual-demo/enable?hash=' . $this->mock_link_hash('visual_demo') . "&sid={$this->sid}");

		// We should be on index page now. Visual demo disable prompt should be displayed.
		$this->assertContains($this->lang('DISABLE_VISUAL_DEMO'), $crawler->filter('.rules')->html());

		// Above header demo should be displayed
		$this->assertContains($this->lang('AD_ABOVE_HEADER'), $crawler->filter('body')->children()->first()->text());

		// Click "disable visual demo" link
		$disable_link = $crawler->filter('.rules a')->first()->link();
		$crawler = self::$client->click($disable_link);

		// Above header demo should not be displayed
		$this->assertNotContains($this->lang('AD_ABOVE_HEADER'), $crawler->filter('body')->children()->first()->text());
	}

	public function test_visual_demo_access()
	{
		$this->logout();

		$crawler = self::request('GET', 'phpbbads-visual-demo/enable?hash=' . $this->mock_link_hash('visual_demo') . "&sid={$this->sid}", array(), false);
		$this->assert_response_html(403);
		$this->assertContains($this->lang('NO_AUTH_OPERATION'), $crawler->filter('body')->text());
	}

	/**
	 * Create a link hash for the user 'admin'
	 *
	 * @param string  $link_name The name of the link
	 * @return string the hash
	 */
	protected function mock_link_hash($link_name)
	{
		$this->get_db();

		$sql = "SELECT user_form_salt
			FROM phpbb_users
			WHERE username = 'admin'";
		$result = $this->db->sql_query($sql);
		$user_form_salt = $this->db->sql_fetchfield('user_form_salt');
		$this->db->sql_freeresult($result);

		return substr(sha1($user_form_salt . $link_name), 0, 8);
	}
}
