<?php

class DevisController extends Zend_Controller_Action
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
        $response->insert('sidebar', $this->view->render('sidebardevis.phtml'));
    }

    /**
     * @throws Zend_Paginator_Exception
     */
    public function indexAction()
    {
        $db_devis = new Application_Model_Devis();
        $select = $db_devis->select()->where('facture = 0 ')->where('valide = 0')->order('date DESC');
        $devis = $db_devis->fetchAll($select);

        $this->indexPrivateAction($devis);
    }

    /**
     * @throws Zend_Paginator_Exception
     */
    private function indexPrivateAction($devis)
    {
        $filtre = new Application_Form_FiltreClient();
        $filtre->getElement('type_filtre')->setValue('factures');
        $filtre->getElement('valide')->setValue('0');
        $this->view->filtre = $filtre;

        $paginator = Zend_Paginator::factory($devis);
        $paginator->setItemCountPerPage(50);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;
    }

    /**
     * @throws Exception
     * @throws Zend_Form_Exception
     */
    public function ajouterAction()
    {
        $form = new Application_Form_Devis();

        $db_devis = new Application_Model_Devis();
        $db_items = new Application_Model_ItemDevis();

        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
            $this->sauveFormDevis($db_devis, $db_items, true);
        }

        $form->getElement('dateCreation')->setValue(date('d/m/Y'));
        $form->getElement('refDossier')->setValue($this->getRefDossierMax());
        $this->view->form = $form;

        $tab = ['Adhesif', 'Deplacement', 'Faconnage', 'ForfaitPrestation', 'Fourniture',
            'Prestation', 'Produit', 'SousTraitance', 'Pose', 'FraisTechnique'
        ];

        foreach($tab as $item) {
            $valItem = 'items'.$item;
            $this->view->$valItem = [];
        }

        $this->view->montantRemise = '';

        //$db_catalogue = new Application_Model_Produits();
        //$select = $db_catalogue->select()->order('designation');
        //$catalogues = $db_catalogue->fetchAll($select);
        $this->view->catalogues = []; //$catalogues;

        $filtreForm = new Application_Form_FiltreCatalogue();
        $this->view->filtreForm = $filtreForm;


        $formRedaction = new Application_Form_WriteDevis();
        $this->view->formRedaction = $formRedaction;

        $dbReglement = new Application_Model_PresetReglement();
        $this->view->listeReglement = $dbReglement->fetchAll($dbReglement->select()->where('1=1'));

        $db_modeles = new Application_Model_Modeles();
        $this->view->modeles = $db_modeles->fetchAll();
        $this->view->id_devis = -1;
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

        $this->view->montantRemise = $devis->remise;

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
        $form->getElement('nomClient')->setValue($client->societe);
        $form->getElement('refDossier')->setValue($devis->ref);
        $form->getElement('delai')->setValue($devis->delai);
        $form->getElement('reglement')->setValue($devis->reglement);
        $form->getElement('intitule')->setValue($devis->titre);
        $form->getElement('dateCreation')->setValue(date('d/m/Y', strtotime($devis->date)));

        $this->view->form = $form;

        //$db_catalogue = new Application_Model_Produits();
        //$select = $db_catalogue->select()->order('designation');
        //$catalogues = $db_catalogue->fetchAll($select);
        $this->view->catalogues = [];//$catalogues;

        $filtreForm = new Application_Form_FiltreCatalogue();
        $this->view->filtreForm = $filtreForm;


        $formRedaction = new Application_Form_WriteDevis();
        $formRedaction->getElement('redactionDevis')->setValue($devis->redaction);
        $this->view->formRedaction = $formRedaction;

        $formNomModele = new Application_Form_NomModele();
        $this->view->formNomModele = $formNomModele;

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
        $db_devis->delete("id = ".$idDevis);

        $this->_redirect('/devis/');
    }

    /**
     * @param Application_Model_Devis $db_devis
     * @param Application_Model_ItemDevis $db_items
     * @param bool $bAjout
     * @throws \Exception
     */
    public function sauveFormDevis($db_devis, $db_items, $bAjout)
    {
        $idDevis = $this->_getParam('id');
        foreach($this->_request->getPost() as $key => $value){
            if(strlen($value)){
                $datas[$key] = $value;
            }
        }

        $data = [];
        $data['id'] = $idDevis;
        $data['id_client'] = $datas['idClient'];
        $data['date_validite'] = $datas[''];
        $data['delai'] = $datas['delai'];
        $data['titre'] = $datas['intitule'];
        $data['redaction'] = $datas['redactionDevis'];
        $data['valide'] = $datas['valide'];
        $data['date_validation'] = $datas['date_validation'];
        $data['facture'] = $datas['facture'];
        $data['date_facture'] = $datas['date_facture'];
        $data['paye'] = $datas['paye'];
        $data['redaction_facture'] = $datas['redaction_facture'];
        $data['date_paiement'] = $datas['date_paiement'];
        $data['acompte'] = $datas['acompte'];
        $data['remise'] = $datas['remise'];
        $data['ref'] = $datas['refDossier'];
        $data['reglement'] = $datas['reglement'];
        $data['jsonEntete'] = $datas['jsonEntete'];

        if ($bAjout) {
            $data['date'] = date('Y-m-d');
            $row = $db_devis->createRow($data);
            $idDevis = $row->save();
        } else {
            $db_devis->update($data, array('id = ?' => $idDevis));
        }

        //on supprime les items avec le devis
        $db_items->delete("id_devis = ".$idDevis);

        $tab = ['Adhesif', 'Deplacement', 'Faconnage', 'ForfaitPrestation', 'Fourniture',
            'Prestation', 'Produit', 'SousTraitance', 'Pose', 'FraisTechnique'
        ];

        foreach ($tab as $item) {
            if (isset($_REQUEST['json_' . $item])) {
                foreach ($_REQUEST['json_' . $item] as $ligne) {

                    if( $ligne != '' ) {
                        $row = $db_items->createRow(['id_devis' => $idDevis]);

                        $row->id_item = 0;
                        $row->remise = 0;
                        $row->pht = 0;
                        $row->typeligne = strtolower($item);
                        $row->json = strtolower($ligne);

                        $row->save();
                    }
                }
            }
        }

        $this->_redirect('/devis/editer/id/' . $idDevis);
    }

    public function getRefDossierMax()
    {
        // TODO aller chercher le max du num dossier pour l'année !
        $max = 1;
        $retour = date('y') . '-'.str_pad ((string)$max,4, "0", STR_PAD_LEFT);

        return $retour;
    }

    public function histoAction()
    {
        $db_devis = new Application_Model_Devis();
        $select = $db_devis->select()
            ->where('valide = 1')
            ->order('date DESC');
        $devis = $db_devis->fetchAll($select);

        $this->indexPrivateAction($devis);
    }

    /**
     * @throws Zend_Paginator_Exception
     */
    public function acceptAction()
    {
        $db_devis = new Application_Model_Devis();
        $select = $db_devis->select()
            ->where('facture = 0 ')
            ->where('valide = 1')
            ->order('date DESC');
        $devis = $db_devis->fetchAll($select);

        $this->indexPrivateAction($devis);
    }
}
