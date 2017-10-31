<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout("login");
    }

    public function indexAction()
    {
        $form = new Application_Form_Login();

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();

            if (strlen($formData[login])) {

                $auth = Zend_Auth::getInstance();
                $db = Zend_Registry::get('db');
                $dbAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'login', 'mdp');
                $dbAdapter->setCredential($formData['mdp'])->setIdentity($formData['login']);
                $result = $auth->authenticate($dbAdapter);

                if ($result->isValid()) {

                    $data = $dbAdapter->getResultRowObject(null, 'pass');
                    $auth->getStorage()->write($data);

                    $this->_redirect('/');
                } else {
                    $this->view->message = 'Echec de connexion';
                }
            } else {
                $this->view->message = 'Les champs sont vides.';
            }
        }

        $this->view->form = $form;

    }

    public function decoAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();

        $this->_redirect('/');
    }


}



