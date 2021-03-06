<?php
/**
 *
 * Advertisement management. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\ads\location\type;

class below_header extends base
{
	/**
	 * {@inheritDoc}
	 */
	public function get_id()
	{
		return 'below_header';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_category()
	{
		return self::CAT_TOP_OF_PAGE;
	}
}
