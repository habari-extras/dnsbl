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
			$ui= new FormUI(strtolower(get_class($this)));
			$ui->append('submit', 'save', _t( 'Save' ));
			$ui->out();
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
	}
}
?>