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
     * @throws Zend_Session_Exception
     */
    public function indexAction()
    {
        $db_devis = new Application_Model_Devis();
        $select = $db_devis->select()->where('facture = 0 ')->where('valide = 0')->order('date DESC');

        $params = $this->_getAllParams();
        if(isset($params['ref']) && strlen(trim($params['ref']))){
            $select->where('ref = ?', $params['ref']);
        }
        if(isset($params['num_devis']) && strlen(trim($params['num_devis']))){
            $select->where('num_devis like ?', '%' . $params['num_devis'] . '%');
        }
        if(isset($params['titre']) && strlen(trim($params['titre']))){
            $select->where('titre like ?', '%' . $params['titre'] . '%');
        }

        // La recherche se fait sur le nom du client alors que dans la base on a id_client !
        if(isset($params['client']) && strlen(trim($params['client']))){
            $db_client = new Application_Model_Clients();
            $subSelect = $db_client->select()->where('societe like ?',$params['client'].'%' );

            $listeClient = $db_client->fetchAll($subSelect);
            $str = '-1';
            foreach($listeClient as $client) {
                $str .= ','.$client->id_client;
            }

            $select->where('id_client in ( '.$str.' ) ' , '');
        }

        if(isset($params['annee']) && isset($params['mois']) && (int) $params['annee'] && (int) $params['mois']){
            $select->where('date like ?', $params['annee'] . '-' . $params['mois'] . '-%');
        }

        // recherche dans les types de devis
        if (isset($params['devis_type']) && $params['devis_type'] == 1) {
            $select->where('titre like ?', 'DT_%');
        }

        $select->order('id desc');

        $devis = $db_devis->fetchAll($select);
        $this->indexPrivateAction($devis);
    }

    /**
     * @param $devis
     * @throws Zend_Paginator_Exception
     * @throws Zend_Session_Exception
     */
    private function indexPrivateAction($devis)
    {
        $filtre = new Application_Form_FiltreDevis();

        $params = $this->_getAllParams();
        $session = new Zend_Session_Namespace('filtreDevis');
        $session->filtres = $params;

        if($session->filtres != null){
            $filtre->populate($session->filtres);
        }

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
        //$form->getElement('refDossier')->setValue($this->getRefDossierMax());
        $form->getElement('numDevis')->setValue($this->getRefDossierMax());
        $this->view->form = $form;

        $tab = ['Adhesif', 'Deplacement', 'Faconnage', 'ForfaitPrestation', 'Fourniture',
            'Prestation', 'Produit', 'SousTraitance', 'Pose', 'FraisTechnique', 'Itemredaction'
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

        $this->view->duplicate = ($this->_getParam('duplicate') == "1");

        $devis = $db_devis->find($id_devis)->current();
        $this->view->devis = $devis;

        $this->view->montantRemise = $devis->remise;

        $tab = ['Adhesif', 'Deplacement', 'Faconnage', 'ForfaitPrestation', 'Fourniture',
                'Prestation', 'Produit', 'SousTraitance', 'Pose', 'FraisTechnique', 'Itemredaction'
               ];

        foreach($tab as $item) {
            $valItem = 'items'.$item;
            $select = $db_items->select()->where('id_devis = ?', $devis->id)
                ->where(' typeligne = ? ', strtolower($item));
            $this->view->$valItem = $db_items->fetchAll($select);
        }

        $db_client = new Application_Model_Clients();
        $client = $db_client->find($devis->id_client)->current();

        if ($this->view->duplicate) {
            $form->getElement('numDevis')->setValue($this->getRefDossierMax());
        } else {
            $form->getElement('numDevis')->setValue($devis->num_devis);
        }
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
        $this->view->fieldsCollapse = $this->getParam('fieldsCollapse');
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
            'Prestation', 'Produit', 'SousTraitance', 'Pose', 'FraisTechnique', 'Itemredaction'
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
            if(is_string($value) && strlen($value)){
                $datas[$key] = $value;
            }
        }

        $data = [];
        $data['id_client'] = $datas['idClient'];
        $data['date_validite'] = $datas[''];
        $data['delai'] = $datas['delai'];
        $data['titre'] = $datas['intitule'];
        $data['redaction'] = $datas['redactionDevis'];
        $data['valide'] = $datas['valide'];
        $data['facture'] = $datas['facture'];
        $data['paye'] = $datas['paye'];
        $data['redaction_facture'] = $datas['redaction_facture'];
        $data['acompte'] = $datas['acompte'];
        $data['remise'] = $datas['remise'];
        $data['ref'] = $datas['refDossier'];
        $data['num_devis']  = $datas['numDevis'];
        $data['reglement']  = $datas['reglement'];
        $data['jsonEntete'] = $datas['jsonEntete'];

        if (strrpos($datas['date_validation'], '0000') === false) {
            $data['date_validation'] = $datas['date_validation'];
        }
        if (strrpos($datas['date_paiement'], '0000') === false) {
            $data['date_paiement'] = $datas['date_paiement'];
        }
        if (strrpos($datas['date_facture'], '0000') === false) {
            $data['date_facture'] = $datas['date_facture'];
        }

        if ($bAjout) {
            $data['date'] = date('Y-m-d');
            $row = $db_devis->createRow($data);
            $idDevis = $row->save();
        } else {
            $data['id'] = $idDevis;

            try {
                $db_devis->update($data, array('id = ?' => $idDevis));
            } catch(Exception $e) {
                echo ($e->getMessage());
            }

        }

        //on supprime les items avec le devis
        $db_items->delete("id_devis = ".$idDevis);

        $tab = ['Adhesif', 'Deplacement', 'Faconnage', 'ForfaitPrestation', 'Fourniture',
            'Prestation', 'Produit', 'SousTraitance', 'Pose', 'FraisTechnique', 'Itemredaction'
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

        $this->_redirect('/devis/editer/id/' . $idDevis."?fieldsCollapse=".$datas['fieldsCollapse']);
    }

    public function getRefDossierMax()
    {
        $year = date('y');

        $db_projet = new Application_Model_Devis();
        $select = $db_projet->select()
            ->from('devis', array('max(num_devis) as max'))
            ->where('num_devis like ?', $year . "-%");
        $row = $db_projet->fetchRow($select);

        //$newNumProject = '';
        if(isset($row) && strpos($row->max, '-')>0) {
            list($y, $inc) = explode('-', $row->max);
            $inc++;

            $newNumProject = $y . '-' . str_pad ((string)$inc,4, "0", STR_PAD_LEFT);
        } else {
            $newNumProject = $year . '-'.str_pad ("1",4, "0", STR_PAD_LEFT) ;
        }

        return $newNumProject;

    }

    /**
     * @throws Zend_Paginator_Exception
     * @throws Zend_Session_Exception
     */
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
     * @throws Zend_Session_Exception
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

    public function articlesindexAction() {
        $db_articles = new Application_Model_Articles();
        $select = $db_articles->select();

        $filtre = new Application_Form_FiltreArticles();

        $params = $this->_getAllParams();
        $session = new Zend_Session_Namespace('filtreArticles');
        $session->filtres = $params;

        if($session->filtres != null){
            $filtre->populate($session->filtres);
        }

        $params = $this->_getAllParams();
        if(isset($params['code']) && strlen(trim($params['code']))){
            $select->where('code like ?', $params['code'].'%');
        }
        if(isset($params['libelle']) && strlen(trim($params['libelle']))){
            $select->where('libelle like ?', $params['libelle'].'%');
        }
        $select->order('libelle desc');

        $this->view->filtre = $filtre;

        $articles = $db_articles->fetchAll($select);
        $paginator = Zend_Paginator::factory($articles);
        $paginator->setItemCountPerPage(50);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;
    }

    public function articlesajouterAction() {
        $form = new Application_Form_Articles();

        $db_articles = new Application_Model_Articles();

        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
            $this->sauveArticle($db_articles, true);
        }

        $this->view->form = $form;
    }

    public function articlesediterAction() {
        $form = new Application_Form_Articles();

        $db_articles = new Application_Model_Articles();

        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
            $this->sauveArticle($db_articles, false);
        }

        $db_article = new Application_Model_Articles();
        $id_article = $this->_getParam('id');
        $article = $db_article->find($id_article)->current();

        $form->getElement('id')->setValue($article->id);
        $form->getElement('code')->setValue($article->code);
        $form->getElement('libelle')->setValue($article->libelle);
        $form->getElement('pu')->setValue($article->pu);
        $form->getElement('qte')->setValue($article->qte);

        $this->view->form = $form;
    }

    public function articlesdeleteAction() {
        $db_article = new Application_Model_Articles();
        $id_article = $this->_getParam('id');
        $article = $db_article->find($id_article)->current();

        $article->delete();

        $this->_redirect('/devis/articlesindex/');
    }

    private function sauveArticle($db_articles, $bAjout) {
        $idArticle = $this->_getParam('id');
        $data = $this->_request->getPost();

        try {
            if ($bAjout) {
                $row = $db_articles->createRow($data);
                $row->save();
            } else {
                $data['id'] = $idArticle;
                unset($data['Enregistrer']);
                $db_articles->update($data, array('id = ?' => $idArticle));
            }

            $this->_redirect('/devis/articlesindex/');
        } catch(Exception $e) {
            echo ($e->getMessage());
        }
    }
}
