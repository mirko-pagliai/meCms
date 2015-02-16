<?php
/**
 * EmailComponent
 *
 * This file is part of MeCms.
 *
 * MeCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeCms.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeCms\Controller\Component
 * @see			http://api.cakephp.org/2.6/class-CakeEmail.html CakePHP Api
 */

App::uses('Component', 'Controller');
App::uses('CakeEmail', 'Network/Email');

/**
 * Sends emails. This component is a wrapper for the `CakeEmail` class.
 */
class EmailComponent extends Component {
	/**
	 * Controller
	 * @var object
	 */
	protected $controller;
	
	/**
	 * `CakeEmail` instance.
	 * @var object 
	 */
	protected $cakeEmail;
	
	/**
	 * Method that is called automatically when the method doesn't exist.
	 * 
	 * This is a wrapper for the `CakeEmail` class.
	 * @param string $method Method to invoke
	 * @param array $params Array of params for the method
	 * @uses $cakeEmail
	 */
	public function __call($method, $params) {
		return call_user_func_array(array($this->cakeEmail, $method), $params);
	}

	/**
	 * Construct.
	 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
	 * @param array $settings Array of configuration settings.
	 * @uses $cakeEmail
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		
		//Initializes the `CakeEmail` class.
		$this->cakeEmail = new CakeEmail();
	}
	
	/**
	 * Configures `CakeEmail` with some default settings.
	 * @uses $cakeEmail
	 * @uses $controller
	 */
	protected function autoConfig() {
		$this->cakeEmail->config('default');
		$this->cakeEmail->from($from = array($this->controller->config['email'] => $this->controller->config['title']));
		$this->cakeEmail->sender($from);
		$this->cakeEmail->template('default', 'MeCms.default');
		$this->cakeEmail->emailFormat('html');
		$this->cakeEmail->helpers(array('Html' => array('className' => 'MeTools.MeHtml')));
		$this->cakeEmail->viewVars(array(
			'config'		=> $this->controller->config,
			'site_address'	=> Router::url('/', TRUE)
		));
	}

	/**
	 * Is called after the controller's beforeFilter method but before the controller executes the current action handler.
	 * @param Controller $controller
	 * @uses $controller
	 */
	public function startup(Controller $controller) {
		$this->controller = $controller;
		
		//Configures `CakeEmail` with some default settings
		$this->autoConfig();
	}
}