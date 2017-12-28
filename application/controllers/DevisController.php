<?php

class DevisController extends Zend_Controller_Action
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

        $response = $this->getResponse();
        $response->insert('sidebar', $this->view->render('sidebardevis.phtml'));
    }

    public function indexAction()
    {

    }

    public function ajouterAction()
    {
        $form = new Application_Form_Devis();
        $this->view->form = $form;


    }

    public function ficheAction()
    {

    }

    public function deleteAction()
    {

    }
}

