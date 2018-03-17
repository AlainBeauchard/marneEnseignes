<?php

class UtilisateurController extends Zend_Controller_Action
{

    public function init()
    {
	    
	  	$auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity() || !$auth->getIdentity()->login) {
            $this->_redirect('/login');
        }    
        
        $acl = Zend_Registry::get('acl');
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'frontend');
        $this->view->navigation(new Zend_Navigation($config))->setAcl($acl)->setRole($auth->getIdentity()->status);
	    
	    if($auth->getIdentity()->status == 'admin'){
        	$response = $this->getResponse();
        	$response->insert('sidebar', $this->view->render('sidebaruser.phtml'));	
        }
        
    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        
        $db_users = new Application_Model_Users();
        $user = $db_users->find($auth->getIdentity()->id_user)->current();
        
        $formUsers = new Application_Form_Users();
        $formUsers->populate($user->toArray());
        $this->view->formUsers = $formUsers;
        
        if($this->_request->isPost() && $formUsers->isValid($this->_request->getPost())){
	        
	        $formDatas = $this->_request->getPost();
	        
	        $user->nom = $formDatas[nom];
	        $user->login = $formDatas[login];
	        $user->mdp = $formDatas[mdp];
	        
	        if($auth->getIdentity()->status == 'admin'){
		       $user->status = $formDatas[status]; 
	        }
	        
	        $user->save();
	    } 
        
    }

    public function ajoutAction()
    {
        $auth = Zend_Auth::getInstance();
        
        $db_users = new Application_Model_Users();
        
        $formUsers = new Application_Form_Users();
        $this->view->formUsers = $formUsers;
        
        if($this->_request->isPost() && $formUsers->isValid($this->_request->getPost())){
	        
	        $formDatas = $this->_request->getPost();
			$row = $db_users->createRow($formDatas);
	        
	        $row->save();
	    } 
        

    }

    public function deleteAction()
    {
		$db_users = new Application_Model_Users();
		$db_users->delete('id_user = ' . $this->_getParam('id'));
        
        $this->_redirect('/utilisateur/liste');
    }

    public function editerAction()
    {
	    $id_user = $this->_getParam('id');
        $db_users = new Application_Model_Users();
        $user = $db_users->find($id_user)->current();
        
        $formUsers = new Application_Form_Users();
        $formUsers->populate($user->toArray());
        $this->view->formUsers = $formUsers;
        
        if($this->_request->isPost() && $formUsers->isValid($this->_request->getPost())){
	        
	        $formDatas = $this->_request->getPost();
	        
	        $user->nom = $formDatas[nom];
	        $user->login = $formDatas[login];
	        $user->mdp = $formDatas[mdp];
		    $user->status = $formDatas[status];
		    $user->color = $formDatas['color'];
		    if ( strpos($user->color,'#') == false ) {
                $user->color = '#'.$user->color;
            }

	        $user->save();
	    } 
    }

    public function listeAction()
    {
        $db_users = new Application_Model_Users();
        $select = $db_users->select()->order('nom');
        
        $users = $db_users->fetchAll($select);
        $this->view->users = $users;
    }


}









