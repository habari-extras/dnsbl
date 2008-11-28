<?php
/**
 * DNSBL
 * 
 *
 * @package dnsbl
 * @version $Id$
 * @author ayunyan <ayu@commun.jp>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link http://ayu.commun.jp/habari-dnsbl
 */
class Dnsbl extends Plugin
{
	/**
	 * plugin information
	 *
	 * @access public
	 * @retrun array
	 */
	public function info()
	{
		return array(
			'name' => 'DNSBL',
			'version' => '0.1-alpha',
			'url' => 'http://ayu.commun.jp/habari-dnsbl',
			'author' => 'ayunyan',
			'authorurl' => 'http://ayu.commun.jp/',
			'license' => 'Apache License 2.0',
			'description' => '',
			'guid' => '86fcddaa-ab4e-11dd-9e8d-001b210f913f'
			);
	}

	/**
	 * setting priority
	 *
	 * @access public
	 * @return array
	 */
	public function set_priorities()
	{
		return array(
			'action_comment_insert_before' => 1
			);
	}

	/**
	 * action: plugin_activation
	 *
	 * @access public
	 * @param string $file
	 * @return void
	 */
	public function action_plugin_activation($file)
	{
		if (Plugins::id_from_file($file) != Plugins::id_from_file(__FILE__)) return;
		Options::set('dnsbl__ipbl', "dnsbl.spam-champuru.livedoor.com");
		Options::set('dnsbl__urlbl', "bsb.spamlookup.net");

	}

	/**
	 * action: update_check
	 *
	 * @access public
	 * @return void
	 */
	public function action_update_check()
	{
		Update::add($this->info->name, $this->info->guid, $this->info->version);
	}

	/**
	 * action: plugin_ui
	 *
	 * @access public
	 * @param string $plugin_id
	 * @param string $action
	 * @return void
	 */
	public function action_plugin_ui($plugin_id, $action)
	{
		if ($plugin_id != $this->plugin_id()) return;
		if ($action == _t('Configure')) {
			$form = new FormUI(strtolower(get_class($this)));
			$form->append('textarea', 'ipbl', 'dnsbl__ipbl', _t('IP Blacklists: ', 'dnsbl'));
			$form->append('submit', 'save', _t('Save'));
			$form->out();
		}
	}

	/**
	 * filter: plugin_config
	 *
	 * @access public
	 * @param array $actions
	 * @param string $plugin_id
	 * @return array
	 */
	public function filter_plugin_config($actions, $plugin_id)
	{
		if ($plugin_id == $this->plugin_id()) {
			$actions[]= _t('Configure');
		}
		return $actions;
	}

	/**
	 * action: comment_insert_before
	 *
	 * @access public
	 * @param object $comment
	 * @return void
	 */
	public function action_comment_insert_before($comment)
	{
		$ipaddrs = explode('.', $comment->ip, 4);
		$reverse_ipaddr = join('.', array_reverse($ipaddrs));

		$ipbls = Options::get('dnsbl__ipbl');
		$ipbls = str_replace("\r", '', $ipbls);
		$ipbls = explode("\n", trim($ipbls));

		$checks = array();
		@reset($ipbls);
		while (list(, $ipbl) = @each($ipbls)) {
			$addr = gethostbyname($reverse_ipaddr . '.' . $ipbl);
			if ($addr == '127.0.0.2') {
				$checks[] = sprintf(_t('Flagged by DNSBL (%s)', 'dnsbl'), $ipbl);
			}
		}

		if (count($checks) > 0) {
			$comment->status = 'spam';
			if (isset($comment->info->spamcheck)) {
				$comment->info->spamcheck = array_unique(array_merge($comment->info->spamcheck, $checks));
			} else {
				$comment->info->spamcheck = $checks;
			}
		}
	}
}
?>