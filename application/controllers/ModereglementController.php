<?php

class ModereglementController extends Zend_Controller_Action
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
        $response->insert('sidebar', $this->view->render('sidebarmodeReglement.phtml'));
    }

    /**
     * @throws Zend_Form_Exception
     * @throws Zend_Paginator_Exception
     * @throws Zend_Session_Exception
     */
    public function indexAction()
    {
        $session = new Zend_Session_Namespace('filtreModeReglement');

        $db_mr = new Application_Model_ModeReglement();
        $select = $db_mr->select();

        $filtreMr = new Application_Form_FiltreModeReglement();

        $params = $this->_getAllParams();
        if($session->filtres != null){
            $filtreMr->populate($session->filtres);
        }

        if(isset($params['libelle_reglement']) && strlen(trim($params['libelle_reglement']))){
            $select->where('libelle_reglement like ?', '%' . $params['libelle_reglement']. '%');
        }
        $select->order('libelle_reglement');

        $this->view->filtreMr = $filtreMr;
        $mr = $db_mr->fetchAll($select);


        $paginator = Zend_Paginator::factory($mr);
        $paginator->setItemCountPerPage(200);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;

        $this->view->mr = $mr;
    }

    /**
     * @throws Zend_Db_Table_Exception
     * @throws Zend_Form_Exception
     */
    public function editerAction()
    {
        $form = new Application_Form_ModeReglement();
        $this->view->form = $form;

        $form->getElement('Ajouter')->setLabel('Modifier le règlement');


        $this->view->id = $this->_getParam('id');

        $db_catalogue = new Application_Model_ModeReglement();
        $catalogue = $db_catalogue->find($this->_getParam('id'))->current();
        $form->populate($catalogue->toArray());

        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
            foreach($this->_request->getPost() as $key => $value){
                if(strlen($value)){
                    $datas[$key] = $value;
                }
            }
            if($db_catalogue->update($datas, array('id = ?' => $this->_getParam('id')))){
                $this->_redirect('/modereglement/editer/id/' . $this->_getParam('id'));
            }

        }
    }

    /**
     * @throws Zend_Form_Exception
     */
    public function ajouterAction()
    {
        $form = new Application_Form_ModeReglement();
        $this->view->form = $form;
        
        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
            $db_catalogue = new Application_Model_ModeReglement();
            $row = $db_catalogue->createRow($this->_request->getPost());
            if($id = $row->save()){
                $this->_redirect('/modereglement/editer/id/' . $id);
            }
        }
    }

    /**
     * @throws Zend_Db_Table_Exception
     */
    public function ficheAction()
    {
        $id = $this->_getParam('id');
        
        $db_catalogue = new Application_Model_ModeReglement();
        $catalogue = $db_catalogue->find($id)->current();

        $this->view->produit = $catalogue;
    }

    /**
     *
     */
    public function deleteAction()
    {
        $db_catalogue = new Application_Model_ModeReglement();
        $db_catalogue->delete('id = ' . $this->_getParam('id'));
        
        $this->_redirect('/modereglement/');
    }
}

