<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $components = array('DebugKit.Toolbar', 'Session', 'Auth', 'RequestHandler');
     public $helpers = array('Html', 'Form', 'Session');

    public function beforeFilter() {
        //
        //$this->response->disableCache();
        $this->Auth->authenticate = array(
            AuthComponent::ALL => array(
                'userModel' => 'User',
                'fields' => array(
                    'username' => 'username',
                ),
                'scope' => array(
                    //define o escorpo do usuario, se ele estar ou apto a logar no sistema
                    'User.status' => 1,
                ),
            ),
            'Form',
        );
        //Setado como controller, utiliza o metodo isAuthorized para saber quando o usuario tem acesso permitido
        $this->Auth->authorize = 'controller';

        //define o controller e a action para login
        $this->Auth->loginAction = array(
            'plugin' => null,
            'controller' => 'users',
            'action' => 'login',
        );

        //define o controller e a action apos o logout do usuario
        $this->Auth->logoutRedirect = array(
            'plugin' => null,
            'controller' => 'users',
            'action' => 'login',
        );

        //define o controller e a action para o login do usuario
        $this->Auth->loginRedirect = array(
            'plugin' => null,
            'controller' => 'home',
            'action' => 'index'
        );

        $this->Auth->authError = __('Acesso negado');

        //$this->Auth->allow('login');
        $this->Auth->allowedActions = array('display');  //allowedActions
    }
    //metodo para controle de acesso
    public function isAuthorized($user) {
        //Somente o admin tem acesso a /admin/controller/action admin_add
        if (isset($user['role_id']) && $user['role_id'] == 1) { //!empty($this->request->params['admin']  $user['role_id'] == 1
            return true; //$user['role_id'] == 1
        }
        return false;  //!empty($user)
    }

}
