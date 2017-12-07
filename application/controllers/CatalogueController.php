<?php

class CatalogueController extends Zend_Controller_Action
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

    }

    public function indexAction()
    {
        $db_produit = new Application_Model_Catalogue();
        $select = $db_produit->select();

        $produits = $db_produit->fetchAll($select);

        $this->view->produits = $produits;
    }

    public function editerAction()
    {
        $form = new Application_Form_Catalogue();
        $this->view->form = $form;

        $this->view->id_produit = $this->_getParam('id');

        $db_catalogue = new Application_Model_Catalogue();
        $catalogue = $db_catalogue->find($this->_getParam('id'))->current();
        $form->populate($catalogue->toArray());

        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
            foreach($this->_request->getPost() as $key => $value){
                if(strlen($value)){
                    $datas[$key] = $value;
                }
            }
            if($db_catalogue->update($datas, array('id_produit = ?' => $this->_getParam('id')))){
                $this->_redirect('/catalogue/editer/id/' . $this->_getParam('id'));
            }

        }
    }

    public function ajouterAction()
    {
        $this->view->form = 'a Faire';
    }

    public function ficheAction()
    {
        $this->view->form = 'a Faire';
    }

    public function deleteAction()
    {
        $this->view->form = 'a Faire';
    }
}

