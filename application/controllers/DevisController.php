<?php

class DevisController extends Zend_Controller_Action
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
        $response->insert('sidebar', $this->view->render('sidebardevis.phtml'));
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
        $select = $db_devis->select()->where('valide = 0')->order('date DESC');
        $devis = $db_devis->fetchAll($select);

        $paginator = Zend_Paginator::factory($devis);
        $paginator->setItemCountPerPage(50);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;
    }

    /**
     *
     */
    public function ajouterAction()
    {
        $form = new Application_Form_Devis();

        $form->getElement('dateCreation')->setValue(date('d/m/Y'));

        $this->view->form = $form;
    }

    /**
     * @throws Zend_Db_Table_Exception
     * @throws Zend_Form_Exception
     */
    public function editerAction()
    {
        $form = new Application_Form_Devis();

        $id_devis = $this->_getParam('id');

        $db_devis = new Application_Model_Devis();
        $db_items = new Application_Model_ItemDevis();

        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
            $this->sauveFormDevis($db_devis, $db_items);
        }

        $devis = $db_devis->find($id_devis)->current();
        $this->view->devis = $devis;

        $selectAdhesif = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'adhesif');
        $itemsAdhesif = $db_items->fetchAll($selectAdhesif);
        $selectDeplacement = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'deplacement');
        $itemsDeplacement = $db_items->fetchAll($selectDeplacement);
        $selectFaconnage = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'faconnage');
        $itemsFaconnage = $db_items->fetchAll($selectFaconnage);
        $selectForfaitPrestation = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'forfaitprestation');
        $itemsForfaitPrestation = $db_items->fetchAll($selectForfaitPrestation);
        $selectFourntiure = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'fourniture');
        $itemsFourntiure = $db_items->fetchAll($selectFourntiure);
        $selectFraisTechnique = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'fraistechnique');
        $itemsFraisTechnique = $db_items->fetchAll($selectFraisTechnique);
        $selectPose = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'pose');
        $itemsPose = $db_items->fetchAll($selectPose);
        $selectPrestation = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'prestation');
        $itemsPrestation = $db_items->fetchAll($selectPrestation);
        $selectProduit = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'produit');
        $itemsProduit = $db_items->fetchAll($selectProduit);
        $selectSousTraitance = $db_items->select()->where('id_devis = ?', $devis->id)
        ->where(' typeligne = ? ', 'soustraitance');
        $itemsSousTraitance = $db_items->fetchAll($selectSousTraitance);

        $this->view->itemsProduits = $itemsProduit;
        $this->view->itemsDeplacement = $itemsDeplacement;
        $this->view->itemsFaconnage = $itemsFaconnage;
        $this->view->itemsForfaitPrestation = $itemsForfaitPrestation;
        $this->view->itemsFourniture = $itemsFourntiure;
        $this->view->itemsFraisTechniques = $itemsFraisTechnique;
        $this->view->itemsPose = $itemsPose;
        $this->view->itemsPrestation = $itemsPrestation;
        $this->view->itemsSousTraitance = $itemsSousTraitance;
        $this->view->itemsAdhesif = $itemsAdhesif;

        $db_client = new Application_Model_Clients();
        $client = $db_client->find($devis->id_client)->current();

        $form->getElement('codeClient')->setValue($client->ref);
        $form->getElement('nomClient')->setValue($client->contact_nom);
        $form->getElement('refDossier')->setValue($devis->ref);
        $form->getElement('delai')->setValue($devis->delai);
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

    /**
     * @param Application_Model_Devis $db_devis
     * @param Application_Model_ItemDevis $db_items
     */
    public function sauveFormDevis($db_devis, $db_items)
    {
        $idDevis = $this->_getParam('id');
        foreach($this->_request->getPost() as $key => $value){
            if(strlen($value)){
                $datas[$key] = $value;
            }
        }
        if($db_devis->update($datas, array('id_devis = ?' => $idDevis))){

            //on supprime les items avec le devis
            $db_items->delete("id_devis = ".$idDevis);

            //TODO on ajoute ceux que l'on vient de recevoir !

            $this->_redirect('/devis/editer/id/' . $idDevis);
        }
    }
}

