<?php

class FactureController extends Zend_Controller_Action
{
    /**
     * @throws Zend_Config_Exception
     * @throws Zend_Controller_Response_Exception
     * @throws Zend_Exception
     * @throws Zend_Navigation_Exception
     * @throws \Exception
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
        $response->insert('sidebar', $this->view->render('sidebarfacture.phtml'));
    }

    /**
     * @throws Zend_Paginator_Exception
     */
    public function indexAction()
    {
        $filtre = new Application_Form_FiltreClient();
        $filtre->getElement('type_filtre')->setValue('factures');
        $filtre->getElement('valide')->setValue('0');
        $this->view->filtre = $filtre;

        $db_devis = new Application_Model_Devis();
        $select = $db_devis->select()->where('facture = 1 ')->where('valide = 0')->order('date DESC');
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

    }

    /**
     * @throws Zend_Db_Table_Exception
     * @throws Zend_Form_Exception
     * @throws \Exception
     */
    public function editerAction()
    {
        $form = new Application_Form_Devis();

        $id_devis = $this->_getParam('id');

        $db_devis = new Application_Model_Devis();
        $db_items = new Application_Model_ItemDevis();

        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
            $this->sauveFormDevis($db_devis, $db_items, false);
        }

        $devis = $db_devis->find($id_devis)->current();
        $this->view->devis = $devis;

        $tab = ['Adhesif', 'Deplacement', 'Faconnage', 'ForfaitPrestation', 'Fourniture',
                'Prestation', 'Produit', 'SousTraitance', 'Pose', 'FraisTechnique'
               ];

        foreach($tab as $item) {
            $valItem = 'items'.$item;
            $select = $db_items->select()->where('id_devis = ?', $devis->id)
                ->where(' typeligne = ? ', strtolower($item));
            $this->view->$valItem = $db_items->fetchAll($select);
        }

        $db_client = new Application_Model_Clients();
        $client = $db_client->find($devis->id_client)->current();

        $form->getElement('codeClient')->setValue($client->ref);
        $form->getElement('nomClient')->setValue($client->contact_nom);
        $form->getElement('refDossier')->setValue($devis->ref);
        $form->getElement('delai')->setValue($devis->delai);
        $form->getElement('reglement')->setValue($devis->reglement);
        $form->getElement('intitule')->setValue($devis->titre);
        $form->getElement('dateCreation')->setValue(date('d/m/Y', strtotime($devis->date)));

        $this->view->form = $form;

        $formRedaction = new Application_Form_WriteDevis();
        $formRedaction->getElement('redactionDevis')->setValue($devis->redaction);
        $this->view->formRedaction = $formRedaction;

        $dbReglement = new Application_Model_PresetReglement();
        $this->view->listeReglement = $dbReglement->fetchAll($dbReglement->select()->where('1=1'));

        $db_modeles = new Application_Model_Modeles();
        $this->view->modeles = $db_modeles->fetchAll();
        $this->view->id_devis = $id_devis;
    }

    /**
     * @throws Zend_Db_Table_Exception
     */
    public function ficheAction()
    {
        $db_devis = new Application_Model_Devis();
        $db_items = new Application_Model_ItemDevis();

        $id_devis = $this->_getParam('id');

        $devis = $db_devis->find($id_devis)->current();
        $this->view->devis = $devis;

        $tab = ['Adhesif', 'Deplacement', 'Faconnage', 'ForfaitPrestation', 'Fourniture',
            'Prestation', 'Produit', 'SousTraitance', 'Pose', 'FraisTechnique'
        ];

        foreach($tab as $item) {
            $valItem = 'items'.$item;
            $select = $db_items->select()->where('id_devis = ?', $devis->id)
                ->where(' typeligne = ? ', strtolower($item));
            $this->view->$valItem = $db_items->fetchAll($select);
        }

        $db_client = new Application_Model_Clients();
        $this->view->client = $db_client->find($devis->id_client)->current();

        $formRedaction = new Application_Form_WriteFacture();
        $formRedaction->getElement('redactionFacture')->setValue($devis->redaction_facture);
        $this->view->formRedactionFacture = $formRedaction;

        $db_modeles = new Application_Model_Modeles();
        $this->view->modeles = $db_modeles->fetchAll();
        $this->view->id_devis = $id_devis;
    }

    public function deleteAction()
    {
        $db_devis = new Application_Model_Devis();
        $db_items = new Application_Model_ItemDevis();

        $idDevis = $this->_getParam('id');
        $db_items->delete("id_devis = ".$idDevis);
        $db_devis->delete("id_devis = ".$idDevis);

        $this->_redirect('/devis/');
    }

    public function histoAction()
    {
        // TODO faire l'historique des factures
    }
}
