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

        $response = $this->getResponse();
        $response->insert('sidebar', $this->view->render('sidebarcatalogue.phtml'));
    }

    public function indexAction()
    {
        $session = new Zend_Session_Namespace('filtreCatalogue');

        $db_produit = new Application_Model_Catalogue();
        $select = $db_produit->select();

        $filtreCatalogue = new Application_Form_FiltreCatalogue();

        if($this->_request->isPost() && $filtreCatalogue->isValid($this->_request->getPost())){
            
            //$formDatas = $this->_request->getPost();
            $params = $this->_request->getPost();
            $session->filtres = $params;
        }

        if($session->filtres != null){
            $filtreCatalogue->populate($session->filtres);
        }

        //$filtreCatalogue->getElement('type_filtre')->setValue('catalogue');
        
        $this->view->filtreCatalogue = $filtreCatalogue;

        if(strlen(trim($session->filtres['codeMe']))){
            $select->where('code_me like ?', '%'.$session->filtres['codeMe'].'%');
        }
        if(strlen(trim($session->filtres['reference']))){
            $select->where('reference like ?', '%' . $session->filtres['reference'] . '%');
        }
        if(strlen(trim($session->filtres['produit']))){
            $select->where('designation like ?', '%' . $session->filtres['produit'] . '%');
        }
        if(strlen(trim($session->filtres['fournisseur']))){
            $select->where('fournisseur like ?', '%' . $session->filtres['fournisseur'] . '%');
        }

        $select->order('code_me');

        $produits = $db_produit->fetchAll($select);

        $this->view->produits = $produits;
    }

    public function editerAction()
    {
        $form = new Application_Form_Catalogue();
        $this->view->form = $form;

        $form->getElement('Ajouter')->setLabel('Modifier le projet');


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
        $form = new Application_Form_Catalogue();
        $this->view->form = $form;
        
        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
            $db_catalogue = new Application_Model_Catalogue();
            $row = $db_catalogue->createRow($this->_request->getPost());
            if($id = $row->save()){
                $this->_redirect('/catalogue/editer/id/' . $id);
            }
        }
    }

    public function ficheAction()
    {
        $id = $this->_getParam('id');
        
        $db_catalogue = new Application_Model_Catalogue();
        $catalogue = $db_catalogue->find($id)->current();
        
        $this->view->catalogue = $catalogue;
    }

    public function deleteAction()
    {
        $db_catalogue = new Application_Model_Catalogue();
        $db_catalogue->delete('id_produit = ' . $this->_getParam('id'));
        
        $this->_redirect('/catalogue/');
    }
}

