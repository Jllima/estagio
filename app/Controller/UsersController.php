<?php

App::uses('AppController', 'Controller');

/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator');

    /**
     * index method
     *
     * @return void
     */
    //reescreve o metodo beforeFilter permitindo acesso a metodo add

    public function isAuthorized($user) {
        if (!parent::isAuthorized($user)) {
            if (in_array($this->action, array('view','edit'))) {//$this->action === 'add'
                //representa os parametros pssados dentro de um url
                $userId = $this->request->params['pass'][0];
                $userSession = $this->Auth->user('id');
                return $this->User->isOwnedBy($userId, $user['id']);
            }
        } elseif (parent::isAuthorized($user)) { //$user['role_id'] == 1
            return true;
        } else {
            return false;
        }
    }

    public function beforeFilter() {
        //chama o metodo beforeFilter da classe pai
        parent::beforeFilter();
        $this->Auth->allow('logout');
    }

    public function login() {
        $this->layout = 'login';
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {

                $this->Session->setFlash('Logado com sucesso', 'flash/flash_succes');
                $this->redirect($this->Auth->loginRedirect);
            } else {
                $this->Session->setFlash('Login ou senha incorretos', 'flash/flash_danger', array(), 'auth');
                $this->redirect($this->Auth->loginAction);
            }
        }
    }

    public function admin_logout() {
        //$this->Session->setFlash(__('Deslogado com sucesso.'), 'default', array('class' => 'success'));
        $this->redirect($this->Auth->logout());
    }

    public function logout() {

        $this->Session->setFlash('Deslogado', 'flash/flash_succes', array(), 'logout');
        $this->redirect($this->Auth->logout());
    }

    public function admin_index() {
        
    }

    public function listar() {
        $this->User->recursive = -1;
        $users = $this->User->find('all');
        //debug($users);
        $this->set(compact('users'));
    }

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->Paginator->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null) {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
        $this->set('user', $this->User->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {
            //debug($this->request->data);exit;
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash('Usuario adicionado com sucesso', 'flash/flash_succes');
                return $this->redirect(array('action' => 'view', $this->User->id));
            } else {
                $this->Session->setFlash('Usuario não foi salvo', 'flash/flash_danger');
            }
        }
        $roles = $this->User->Role->find('list');
        $this->set(compact('roles'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null) {

        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is(array('post', 'put'))) {
            //debug($this->request->data);exit;
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash('Perfil editado com sucesso', 'flash/flash_succes');
                return $this->redirect(array('action' => 'view', $this->User->id));
            } else {
                $this->Session->setFlash('Edição falhou', 'flash/flash_danger');
            }
        } else {
            $options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
            $this->request->data = $this->User->find('first', $options);
            //debug($this->request->data);
        }
        $roles = $this->User->Role->find('list');
        $this->set(compact('roles'));
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->User->delete()) {
            $this->Session->setFlash(__('The user has been deleted.'));
        } else {
            $this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

}
