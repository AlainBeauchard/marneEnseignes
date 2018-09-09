<?php

class FacturationController extends Zend_Controller_Action
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
		$response->insert('sidebar', $this->view->render('sidebarfacturation.phtml'));

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

    public function creerdevisAction()
    {
		$formItemDevis = new Application_Form_Itemsdevis();
		$this->view->formItemDevis = $formItemDevis;

		$form = new Application_Form_Creerdevis();
		$this->view->form = $form;

		$formRedaction = new Application_Form_WriteDevis();
		$this->view->formRedaction = $formRedaction;


		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			$formDatas = $this->_request->getPost();
			$db_devis = new Application_Model_Devis();
			$devis = $db_devis->createRow($formDatas);
			list($j,$m,$a) = explode('/', $formDatas[date]);
			$devis->date = $a.'-'.$m.'-'.$j;
			$date_devis = $a.'-'.$m.'-'.$j;
// 			list($j,$m,$a) = explode('/', $formDatas[delai]);
			$devis->delai = $formDatas[delai];

			$validite = new DateTime($date_devis);
			date_add($validite, date_interval_create_from_date_string('1 month'));
			$devis->date_validite = date_format($validite, 'Y-m-d');

			$id_devis = $devis->save();
			
			$this->_redirect('/facturation/editer/id/' . $id_devis);

/*
			$this->view->id_devis = $id_devis;
			$formItemDevis->getElement('id_devis')->setValue($id_devis);
*/
		}

    }

    public function creerfactureAction()
    {
		$formItemDevis = new Application_Form_Itemsdevis();
		$this->view->formItemDevis = $formItemDevis;

		$form = new Application_Form_Creerdevis();
		$form->getElement('creerdevis')->setLabel('Créer facture');
		$this->view->form = $form;

		$formRedaction = new Application_Form_WriteDevis();
		$this->view->formRedaction = $formRedaction;


		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			$formDatas = $this->_request->getPost();
			$db_devis = new Application_Model_Devis();
			$devis = $db_devis->createRow($formDatas);
			list($j,$m,$a) = explode('/', $formDatas[date]);
			$devis->date = $a.'-'.$m.'-'.$j;
			$date_devis = $a.'-'.$m.'-'.$j;
			list($j,$m,$a) = explode('/', $formDatas[delai]);
			$devis->delai = $a.'-'.$m.'-'.$j;

			$validite = new DateTime($date_devis);
			date_add($validite, date_interval_create_from_date_string('1 month'));
			$devis->date_validite = date_format($validite, 'Y-m-d');

			$id_devis = $devis->save();

			$this->view->id_devis = $id_devis;
			$formItemDevis->getElement('id_devis')->setValue($id_devis);
		}

    }

    public function facturesAction()
    {
		$db_devis = new Application_Model_Devis();

		$select = $db_devis->select()->where('valide = 1 AND facture = 1 and paye = 0')->order('date');
		$factures = $db_devis->fetchAll($select);

		$paginator = Zend_Paginator::factory($factures);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

		$this->view->paginator = $paginator;
    }

    public function devisAction()
    {
    }

    public function historiqueAction()
    {
		$filtre = new Application_Form_FiltreClient();
		$filtre->getElement('type_filtre')->setValue('factures');
		$filtre->getElement('valide')->setValue('1');
		$this->view->filtre = $filtre;

		if($this->_getParam('f') == 'devis'){
			$db_devis = new Application_Model_Devis();
			$select = $db_devis->select()->where('valide = 1')->order('date_validite');
			$devis = $db_devis->fetchAll($select);

			$paginator = Zend_Paginator::factory($devis);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			Zend_Paginator::setDefaultScrollingStyle('Sliding');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
		}else{
			$db_devis = new Application_Model_Devis();
			$select = $db_devis->select()->where('paye = 1')->order('date_validite');
			$devis = $db_devis->fetchAll($select);

			$paginator = Zend_Paginator::factory($devis);
			$paginator->setCurrentPageNumber($this->_getParam('page'));
			Zend_Paginator::setDefaultScrollingStyle('Sliding');
			Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
		}

		$this->view->paginator = $paginator;
		$this->view->item = $this->_getParam('f');

    }

    public function editerAction()
    {
		$id_devis = $this->_getParam('id');
		
		$calc = new Application_Form_Calc();
		$this->view->calc = $calc;

		$db_devis = new Application_Model_Devis();
		$devis = $db_devis->find($id_devis)->current();
		$this->view->devis = $devis;
		
		$db_catalogue = new Application_Model_Produits();
		$select = $db_catalogue->select()->order('designation');
		$catalogues = $db_catalogue->fetchAll();
		
		$this->view->catalogues = $catalogues;

		$form = new Application_Form_Creerdevis();
		$form->populate($devis->toArray());
		$this->view->form = $form;
		
		$filtreForm = new Application_Form_FiltreCatalogue();
		$this->view->filtreForm = $filtreForm;

		$formRedaction = new Application_Form_WriteDevis();
		$this->view->formRedaction = $formRedaction;
		
		$formNomModele = new Application_Form_NomModele();
		$this->view->formNomModele = $formNomModele;
		
		$db_modeles = new Application_Model_Modeles();
		$this->view->modeles = $db_modeles->fetchAll();

		if($devis->redaction !== NULL){
			$formRedaction->getElement('redactionDevis')->setValue($devis->redaction);
		}

		$formItemDevis = new Application_Form_Itemsdevis();
		$formItemDevis->getElement('id_devis')->setValue($id_devis);
		$this->view->formItemDevis = $formItemDevis;

		$db_client = new Application_Model_Clients();
		$client = $db_client->find($devis->id_client)->current();

		$form->getElement('client')->setValue($client->societe);
		$form->getElement('date')->setValue(date('d/m/Y', strtotime($devis->date)));
		$form->getElement('creerdevis')->setLabel('Modifier Devis');

		$db_items = new Application_Model_ItemDevis();
		$select = $db_items->select()->where('id_devis = ?', $devis->id);
		$items = $db_items->fetchAll($select);

		$this->view->items = $items;

		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			$formDatas = $this->_request->getPost();
			
			$devis->id_client = $formDatas[id_client];

			list($j,$m,$a) = explode('/', $formDatas[date]);
			$devis->date = $a.'-'.$m.'-'.$j;
			$date_devis = $a.'-'.$m.'-'.$j;

			$devis->delai = $formDatas[delai];

			$validite = new DateTime($date_devis);
			date_add($validite, date_interval_create_from_date_string('1 month'));
			$devis->date_validite = date_format($validite, 'Y-m-d');

			$devis->titre = $formDatas[titre];
			$devis->save();
		}

    }

    public function ficheAction()
    {
		$id_devis = $this->_getParam('id');

		$db_devis = new Application_Model_Devis();
		$devis = $db_devis->find($id_devis)->current();

		$this->view->devis = $devis;

		$formRedaction = new Application_Form_WriteDevis();
		$this->view->formRedaction = $formRedaction;

		$formRedactionFacture = new Application_Form_WriteFacture();
		$this->view->formRedactionFacture = $formRedactionFacture;

		$formFacture = new Application_Form_Facture();
		$formFacture->populate($devis->toArray());
		$formFacture->getElement('date_paiement')->setValue(date('d/m/Y', strtotime($devis->date_paiement)));
		$formFacture->getElement('date_facture')->setValue(date('d/m/Y', strtotime($devis->date_facture)));
		$this->view->formFacture = $formFacture;
		
		$formNomModele = new Application_Form_NomModele();
		$this->view->formNomModele = $formNomModele;
		
		$db_modeles = new Application_Model_Modeles();
		$this->view->modeles = $db_modeles->fetchAll();

		if($this->_request->isPost() && $formFacture->isValid($this->_request->getPost())){
			$formDatas = $this->_request->getPost();

			list($j, $m, $a) = explode('/', $formDatas[date_paiement]);
			$devis->date_paiement = $a.'-'.$m.'-'.$j;
			$devis->acompte = $formDatas[acompte];
			$devis->remise = $formDatas[remise];
			$devis->reglement = $formDatas[reglement];
			$devis->save();
		}

		$db_itemsDevis = new Application_Model_ItemDevis();
		$select = $db_itemsDevis->select()->where('id_devis = ?', $id_devis);
		$items = $db_itemsDevis->fetchAll($select);

		$this->view->items = $items;

		$db_client = new Application_Model_Clients();
		$client = $db_client->find($devis->id_client)->current();

		$this->view->client = $client;

    }

    public function printAction()
    {
		$this->_helper->layout->setLayout("print");
		$id = $this->_getParam('id');
		$modele = $this->getParam('modele');

		$db_devis = new Application_Model_Devis();
		$rows = $db_devis->find($id)->current();
		
		$this->view->devis = $rows;

		$db_client = new Application_Model_Clients();
		$client = $db_client->find($rows->id_client)->current();

		$this->view->client = $client;

        $db_itemsDevis = new Application_Model_ItemDevis();
        $select = $db_itemsDevis->select()->where('id_devis = ?', $id)->where('typeligne = ?', 'itemredaction');
        $articles = $db_itemsDevis->fetchAll($select);

        if($modele == 'devis'){
			$this->view->devis = $rows;
			$this->view->articles = $articles;
			$this->_helper->viewRenderer('printdevis');
		}else{
			$this->view->facture = $rows;
			$this->_helper->viewRenderer('printfacture');
		}

    }

    public function accepterAction()
    {
		$id = $this->_getParam('id');

		$db_devis = new Application_Model_Devis();
		$devis = $db_devis->find($id)->current();

		$devis->valide = 1;
		$devis->save();

		$this->_redirect('/facturation/fiche/id/' . $id);
    }

    public function facturerAction()
    {
		$id = $this->_getParam('id');

		$db_devis = new Application_Model_Devis();
		$devis = $db_devis->find($id)->current();

		$date = new Zend_Date();

		$devis->date_facture = $date->get('yyyy-MM-dd');
		$devis->date_paiement = date('Y-m-d', strtotime('+ 1 month'));
		$devis->facture = 1;
		$devis->save();

		$this->_redirect('/facturation/fiche/id/' . $id);
    }

    public function devisacceptesAction()
    {
        $filtre = new Application_Form_FiltreClient();
		$filtre->getElement('type_filtre')->setValue('factures');
		$filtre->getElement('valide')->setValue('0');
		$this->view->filtre = $filtre;

		$db_devis = new Application_Model_Devis();
		$select = $db_devis->select()->where('valide = 1')->where('facture = 0')->order('date DESC');
		$devis = $db_devis->fetchAll($select);

		$paginator = Zend_Paginator::factory($devis);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

		$this->view->paginator = $paginator;
    }

    public function cgvAction()
    {
        $this->_helper->layout->setLayout("print");
    }

    public function historiquefacturesAction()
    {
        // action body
    }
}






