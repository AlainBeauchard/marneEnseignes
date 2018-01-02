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
        $filtre = new Application_Form_FiltreClient();
        $filtre->getElement('type_filtre')->setValue('factures');
        $filtre->getElement('valide')->setValue('0');
        $this->view->filtre = $filtre;

        $db_devis = new Application_Model_Devis();
        $select = $db_devis->select()->where('valide = 0')->order('date DESC');
        $devis = $db_devis->fetchAll($select);

        $paginator = Zend_Paginator::factory($devis);
        $paginator->setItemCountPerPage(50);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;
    }

    public function ajouterAction()
    {
        $form = new Application_Form_Devis();

        $form->getElement('dateCreation')->setValue(date('d/m/Y'));

        $this->view->form = $form;
    }

    public function editerAction()
    {
        $form = new Application_Form_Devis();


        $id_devis = $this->_getParam('id');

        $db_devis = new Application_Model_Devis();
        $devis = $db_devis->find($id_devis)->current();
        $this->view->devis = $devis;

        $db_client = new Application_Model_Clients();
        $client = $db_client->find($devis->id_client)->current();

        $form->getElement('codeClient')->setValue($client->ref);
        $form->getElement('refDossier')->setValue($devis->ref);
        $form->getElement('intitule')->setValue($devis->titre);
        $form->getElement('dateCreation')->setValue(date('d/m/Y', strtotime($devis->date)));

        $this->view->form = $form;
    }

    public function ficheAction()
    {

    }

    public function deleteAction()
    {

    }
}

