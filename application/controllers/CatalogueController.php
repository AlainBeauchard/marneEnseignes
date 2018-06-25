<?php

class CatalogueController extends Zend_Controller_Action
{
    /**
     * @throws Zend_Config_Exception
     * @throws Zend_Controller_Response_Exception
     * @throws Zend_Exception
     * @throws Zend_Navigation_Exception
     */
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

    /**
     * @throws Zend_Form_Exception
     * @throws Zend_Paginator_Exception
     * @throws Zend_Session_Exception
     */
    public function indexAction()
    {
        $session = new Zend_Session_Namespace('filtreCatalogue');

        $db_produit = new Application_Model_Catalogue();
        $select = $db_produit->select();

        $filtreCatalogue = new Application_Form_FiltreCatalogue();

        $params = $this->_getAllParams();

        if ( isset($params['reference']) ) {
            $_SESSION['paramsCalalogue'] = $this->_getAllParams();
        }

        $session->filtres = $_SESSION['paramsCalalogue'];

        if($session->filtres != null){
            $filtreCatalogue->populate($session->filtres);
        }

        $bfiltre = false;
        if(isset($session->filtres['reference']) && strlen(trim($session->filtres['reference']))){
            $bfiltre = true;
            $select->where('reference like ?', '%' . $session->filtres['reference']. '%');
        }
        if(isset($session->filtres['codeMe']) && strlen(trim($session->filtres['codeMe']))){
            $bfiltre = true;
            $select->where('code_me like ?', '%' . $session->filtres['codeMe']. '%');
        }
        if(isset($session->filtres['produit']) && strlen(trim($session->filtres['produit']))){
            $bfiltre = true;
            $select->where('designation like ?', '%' . $session->filtres['produit'] . '%');
        }
        if(isset($session->filtres['type']) && strlen(trim($session->filtres['type']))){
            $bfiltre = true;
            $select->where('type like ?', '%' . $session->filtres['type'] . '%');
        }
        if(isset($session->filtres['epaisseur']) && strlen(trim($session->filtres['epaisseur']))){
            $bfiltre = true;
            $select->where('epaisseur like ?', '%' . $session->filtres['epaisseur'] . '%');
        }
        if(isset($session->filtres['format']) && strlen(trim($session->filtres['format']))){
            $bfiltre = true;
            $select->where('format like ?', '%' . $session->filtres['format'] . '%');
        }
        if(isset($session->filtres['surface_totale']) && strlen(trim($session->filtres['surface_totale']))){
            $bfiltre = true;
            $select->where('surface_totale like ?', '%' . $session->filtres['surface_totale'] . '%');
        }
        if(isset($session->filtres['fournisseur']) && strlen(trim($session->filtres['fournisseur']))){
            $bfiltre = true;
            $select->where('fournisseur like ?', '%' . $session->filtres['fournisseur'] . '%');
        }
        $select->order('code_me');

        $this->view->filtreCatalogue = $filtreCatalogue;
        if ( $bfiltre ) {
            $produits = $db_produit->fetchAll($select);
        } else {
            $produits = [];
        }


        $paginator = Zend_Paginator::factory($produits);
        $paginator->setItemCountPerPage(200);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;

        $this->view->catalogues = $produits;

        $this->view->origin = 'catalogue';
        $this->view->nom_id_pc = 'id_produit_pc';
        $this->view->nom_name_pc = 'id_produit';
    }

    /**
     * @throws Zend_Db_Table_Exception
     * @throws Zend_Form_Exception
     */
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

    /**
     * @throws Zend_Form_Exception
     */
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

    /**
     * @throws Zend_Db_Table_Exception
     */
    public function ficheAction()
    {
        $id = $this->_getParam('id');
        
        $db_catalogue = new Application_Model_Catalogue();
        $catalogue = $db_catalogue->find($id)->current();

        $this->view->produit = $catalogue;
    }

    /**
     *
     */
    public function deleteAction()
    {
        $db_catalogue = new Application_Model_Catalogue();
        $db_catalogue->delete('id_produit = ' . $this->_getParam('id'));
        
        $this->_redirect('/catalogue/');
    }
}

