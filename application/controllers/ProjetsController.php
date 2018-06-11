<?php

class ProjetsController extends Zend_Controller_Action
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
        $response->insert('sidebar', $this->view->render('sidebarprojet.phtml'));
    }

    public function indexAction()
    {
    	//Pour ne pas perdre l'info que l'on n'afiiche que les projets en devis à relancer (où non)
		if(isset($_GET["dr"]))
			$_SESSION["dr"] = $_GET["dr"];
		else
		{
			if( !isset($_SESSION["dr"]) )
				$_SESSION["dr"] = 0;
		}

		$session = new Zend_Session_Namespace('filtreProjet');
		
		$filtreProjet = new Application_Form_FiltreClient();
		
		if($this->_request->isPost() && $filtreProjet->isValid($this->_request->getPost())){
			
			$formDatas = $this->_request->getPost();
			$params = $this->_request->getPost();
			$session->filtres = $params;
			$this->view->currentUser = $params['user'];
		}
		
		
		if($session->filtres != null){
		   $filtreProjet->populate($session->filtres);
		}
		
		$this->view->currentUser = $session->filtres['users'];
		
		$filtreProjet->getElement('type_filtre')->setValue('projet');
		$this->view->filtreProjet = $filtreProjet;
		
		$db_projet = new Application_Model_Projets();
		
		$select = $db_projet->select()
						->from(array('p'=>'projets'));

			
		if(strlen(trim($session->filtres['num_client'])) > 0 || strlen(trim($session->filtres['client'])) > 0){
			if(strlen(trim($session->filtres['num_client'])) > 0){
				$select->join(array('c'=>'clients'),
					'c.ref = "' . $session->filtres['num_client'] . '" AND c.id_client = p.id_client', array('ref', 'societe'));
			}else if(strlen(trim($session->filtres['client'])) > 0){
				$select->join(array('c'=>'clients'),
					'c.societe like "%' . $session->filtres['client'] . '%" AND c.id_client = p.id_client', array('ref', 'societe'));
			}
		}
		
		if((int) $session->filtres['users'] && strlen(trim($session->filtres['etapes']))){
				$select->join(array('t'=>'projetstaches'),
					'(t.id_user1 = "' . $session->filtres['users'] . '" OR t.id_user2 = "' . $session->filtres['users'] . '" OR t.id_user3 = "' . $session->filtres['users'] . '") AND fait = 0 AND t.id_projet = p.id_projet AND tache like "%' . $session->filtres['etapes'] . '%" AND fait = 0 AND VISIBLE=1 AND t.id_projet = p.id_projet');
					
				$this->view->user = $session->filtres['users'];
		}else{
			if((int) $session->filtres['users']){
				$select->join(array('t'=>'projetstaches'),
					'(t.id_user1 = "' . $session->filtres['users'] . '" OR t.id_user2 = "' . $session->filtres['users'] . '" OR t.id_user3 = "' . $session->filtres['users'] . '") AND fait = 0 AND t.id_projet = p.id_projet' );
				$this->view->user = $session->filtres['users'];
			}
	
			if(strlen(trim($session->filtres['etapes']))){
				$select->join(array('t'=>'projetstaches'),
					'tache like "%' . $session->filtres['etapes'] . '%" AND fait = 0 AND t.id_projet = p.id_projet');
			}
		}
		
			
		if($session->filtres['sem']){
			if ($session->filtres['sem'] == "v")
			{
				$select->where('sem IS NULL OR sem = "" ');
			}
			elseif($session->filtres['sem'] == "nv")
			{
				$select->where('sem != NULL OR sem != "" ');
			}else{
				$select->where('sem like ?', $session->filtres['sem']);	
			}
			
		}
		
		if($session->filtres['mois']){
			$select->where('p.date_commande like ?', '%-'.$session->filtres['mois'].'-%');
		}
			
		if($session->filtres['dossier']){
			$select->where('p.dossier like ?', $session->filtres['dossier']);
		}
		
		if($session->filtres['contact']){
			$select->where('p.contact_nom like ?', '%'.$session->filtres['contact'].'%');
		}
	
		if(strlen(trim($session->filtres['projet'])) > 0){
			$select->where('p.titre like ?', '%' . $session->filtres['projet'] . '%');
		}
	
		if((int) $session->filtres['id_client']){
			$select->where('p.id_client = ?', $session->filtres['id_client']);
		}
		
		$select->where('p.cloture = 0');
		
		//on ne veut pas des projets 'marquer comme devis à relancer'
		$devisarelancer = $_SESSION["dr"];
		if( !isset($devisarelancer) || $devisarelancer == "" )
			$devisarelancer = 0;

		$select->where('p.devisarelancer = ?', $devisarelancer);

		$select->group('p.id_projet');
		//$select->order('p.dossier DESC, t.date_fait ASC');
		$select->order(['p.dossier DESC']);

		$select->setIntegrityCheck(false);

       $projets = $db_projet->fetchAll($select);
       
       $paginator = Zend_Paginator::factory($projets);
       $paginator->setItemCountPerPage(250);
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       Zend_Paginator::setDefaultScrollingStyle('Sliding');
       Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

       $this->view->paginator = $paginator;
       $this->view->origin = 'projet';
       $this->view->nom_id_pc = 'id_projet_pc';
       $this->view->nom_name_pc = 'id_projet';
    }

    public function ajoutAction()
    {
	    $auth = Zend_Auth::getInstance();
	    
        $form = new Application_Form_Projet();
        $this->view->form = $form;
        
		if((int) $this->_getParam('id_client')){
		    $db_client = new Application_Model_Clients();
		    $client = $db_client->find($this->_getParam('id_client'))->current();

		    $form->getElement('id_client')->setValue($client->id_client);
		    $form->getElement('client')->setValue($client->societe);
		    $form->getElement('code_client')->setValue($client->ref);
		    $form->getElement('contact_nom')->setValue($client->contact_nom);
		    $form->getElement('contact_mail')->setValue($client->contact_mail);
		    $form->getElement('contact_tel')->setValue($client->contact_tel);
		    $form->getElement('nomlivraison')->setValue($client->societe);
		    $form->getElement('adresselivraison')->setValue($client->adresse);
		    $form->getElement('codepostal')->setValue($client->codepostal);
		    $form->getElement('ville')->setValue($client->ville);
	    }

        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
	        $date = new Zend_Date();
	        
	        $formDatas = $this->_request->getPost();
	        
	        $db_projet = new Application_Model_Projets();
	        $projet = $db_projet->createRow();
	        $projet->dossier = $formDatas[dossier];
	        $projet->id_client = $formDatas[id_client];
	        $projet->titre = $formDatas[titre];
	        $projet->id_devis = $formDatas[id_devis];
	        $projet->contact_nom = $formDatas[contact_nom];
	        $projet->contact_tel = $formDatas[contact_tel];
	        $projet->contact_mail = $formDatas[contact_mail];
	        $projet->nomlivraison = $formDatas[nomlivraison];
	        $projet->adresselivraison = $formDatas[adresselivraison];
	        $projet->codepostal = $formDatas[codepostal];
	        $projet->ville = $formDatas[ville];
	        $projet->chemin = $formDatas[chemin];
	        $projet->devisarelancer = 0;
	        $projet->colorclass = '';
	        list($j, $m, $a) = explode('/', $formDatas[date_commande]);
	        $projet->date_commande = $a . "-" . $m . "-" . $j;
	        $projet->date_creation = $date->get('YYYY-MM-dd');
	        $id_projet = $projet->save();
	        
	        $date = new Zend_Date();
	        
	        $db_notes = new Application_Model_Notesprojet();
	        $note = $db_notes->createRow();
	        $note->id_projet = $id_projet;
	        $note->note = $formDatas[note];
	        $note->date = $date->get('YYYY-MM-dd');
	        $note->id_user = $auth->getIdentity()->id_user;
	        
	        $note->save();
	        
	        $this->_redirect('/projets/editer/id/' . $id_projet);
	        
/*
	        $this->view->id_projet = $id_projet;
	        
	        $formProjet = new Application_Form_AjoutTache();
	        $this->view->formProjet = $formProjet;
	        
	        $formProjet->getElement('id_projet')->setValue($id_projet);
*/
	        
        }
      
    }

    public function editerAction()
    {
        $id_projet = $this->_getParam('id');
        
        $auth = Zend_Auth::getInstance();
        
        $db_projet = new Application_Model_Projets();
        $projet = $db_projet->find($id_projet)->current();
        
        $this->view->projet = $projet;
        
        $db_client = new Application_Model_Clients();
        $client = $db_client->find($projet->id_client)->current();
        
        $formTache = new Application_Form_Plantache();
        $this->view->formTache = $formTache;
        
        $form = new Application_Form_Projet();
        $form->populate($projet->toArray());

        if ($projet->code_client == null){
	        $form->getElement('code_client')->setValue($client->ref);
        }
        
		if( isset($projet->date_commande) == false )
			$projet->date_commande = date('m/d/Y');

        $form->getElement('date_commande')->setValue(date('d/m/Y', strtotime($projet->date_commande)));
        $form->getElement('client')->setValue($client->societe);
        $this->view->form = $form;
        
        $form->getElement('creerdevis')->setLabel('Modifier le projet');
        
		$formProjet = new Application_Form_AjoutTache();
		$this->view->formProjet = $formProjet;
		
		$formProjet->getElement('id_projet')->setValue($id_projet);
		
		$db_taches = new Application_Model_Taches();
		$select = $db_taches->select()
            ->where('id_projet = ?', $id_projet)
            ->where('visible = ?', 1);
            //->order('STR_TO_DATE(date_fait,\'%d/%m/%Y\')');
		$taches = $db_taches->fetchAll($select);

		$taches = $db_taches->trieTableauTaches($taches);

		$this->view->taches = $taches;
		
		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			$formDatas = $this->_request->getPost();
	        $projet->dossier = $formDatas[dossier];
	        $projet->id_client = $formDatas[id_client];
	        $projet->titre = $formDatas[titre];
	        $projet->id_devis = $formDatas[id_devis];
	        $projet->contact_nom = $formDatas[contact_nom];
	        $projet->contact_tel = $formDatas[contact_tel];
	        $projet->contact_mail = $formDatas[contact_mail];
	        $projet->nomlivraison = $formDatas[nomlivraison];
	        $projet->adresselivraison = $formDatas[adresselivraison];
	        $projet->codepostal = $formDatas[codepostal];
	        $projet->ville = $formDatas[ville];
	        $projet->chemin = $formDatas[chemin];
	        $projet->note = '';
	        list($j, $m, $a) = explode('/', $formDatas[date_commande]);
	        $projet->date_commande = $a . "-" . $m . "-" . $j;
	        $projet->save();
	        
	        $date = new Zend_Date();
	        
	        $db_notes = new Application_Model_Notesprojet();
	        $note = $db_notes->createRow();
	        $note->id_projet = $id_projet;
	        $note->note = $formDatas[note];
	        $note->id_user = $auth->getIdentity()->id_user;
	        $note->date = $date->get('YYYY-MM-dd');
	        $note->save();
		}
        
        
    }

    public function ficheAction()
    {
        $id_projet = $this->_getParam('id');
        $id_PreviousProjet = $this->_getParam('idPrevious');

        $auth = Zend_Auth::getInstance();
        
        $db_projet = new Application_Model_Projets();
        $projet = $db_projet->find($id_projet)->current();
        
        $db_notes = new Application_Model_Notesprojet();
        $select = $db_notes->select()->where('id_projet = ?', $id_projet)->order('id DESC');
        $notes = $db_notes->fetchAll($select);
        
        $this->view->notes = $notes;
        
        $noteForm = new Application_Form_NotesProjet();
        $noteForm->getElement('id_projet')->setValue($id_projet);
//         $noteForm->getElement('note')->setValue($projet->note);
        $this->view->noteForm = $noteForm;
        
        $db_messages = new Application_Model_Messages();
        $select = $db_messages->select()->where('dossier like ?', $projet->dossier);
        
        $this->view->messages = $db_messages->fetchAll($select);
        
        $db_devis = new Application_Model_Devis();
        $select = $db_devis->select()
        					->where('ref = ?', trim($projet->dossier))
        					->order('date');
        					
        $this->view->devis = $db_devis->fetchAll($select);
        
        $noteTache = new Application_Form_TacheCommentaire();
        $this->view->noteTache = $noteTache;
        
        $formTache = new Application_Form_Plantache();
        $this->view->formTache = $formTache;
        
        $this->view->projet = $projet;
        $this->view->idPreviousProjet = $id_PreviousProjet;

        $db_taches = new Application_Model_Taches();
        $select = $db_taches->select()->where('id_projet = ?', $id_projet)->order(["STR_TO_DATE(date_fait,'%d/%m/%Y')", "id"]);
        $taches = $db_taches->fetchAll($select);

        $taches = $db_taches->trieTableauTaches($taches);

        $this->view->taches = $taches;
        
        if($this->_request->isPost() && $noteForm->isValid($this->_request->getPost())){
	    	$formDatas = $this->_request->getPost();
	    	
	    	$date = new Zend_Date();
	    	
	        $note = $db_notes->createRow();
	        $note->id_projet = $id_projet;
	        $note->note = $formDatas[note];
	        $note->id_user = $auth->getIdentity()->id_user;
	        $note->date = $date->get('YYYY-MM-dd');
	        $note->save();
	        
	        $this->_redirect('/projets/fiche/id/' . $id_projet);
	    }
    }

    public function historiqueAction()
    {
	   $session = new Zend_Session_Namespace('filtreProjetsClotures');
	    
       $filtreProjet = new Application_Form_FiltreClient();
       
       	if($this->_request->isPost() && $filtreProjet->isValid($this->_request->getPost())){
			
			$params = $this->_request->getPost();
			$session->filtres = $params;
		}
	   	   
	   if(count($session->filtres)){
			$filtreProjet->populate($session->filtres);   
	   }
	   
	   $this->view->filtreProjet = $filtreProjet;
	    
       $db_projet = new Application_Model_Projets();
       $select = $db_projet->select()->from(array('p'=>'projets'));
       
   		if(strlen(trim($session->filtres['num_client'])) > 0 || strlen(trim($session->filtres['client'])) > 0){
			if(strlen(trim($session->filtres['num_client'])) > 0){
				$select->join(array('c'=>'clients'),
					'c.ref = "' . $session->filtres['num_client'] . '" AND c.id_client = p.id_client');
			}else if(strlen(trim($session->filtres['client'])) > 0){
				$select->join(array('c'=>'clients'),
					'c.societe like "' . $session->filtres['client'] . '%" AND c.id_client = p.id_client');
			}
		}
		
		if(strlen(trim($session->filtres['projet'])) > 0){
			$select->where('p.titre like ?', '%' . $session->filtres['projet'] . '%');
		}
		
		if(strlen(trim($session->filtres['contact'])) > 0){
			$select->where('p.contact_nom like ?', '%' . $session->filtres['contact'] . '%');
		}
		
		if($session->filtres['dossier']){
			$select->where('p.dossier like ?', $session->filtres['dossier']);
		}

       $select->where('p.cloture = 1')
       			->order('p.dossier DESC');
       
       $select->setIntegrityCheck(false);

       $projets = $db_projet->fetchAll($select);
       
       $paginator = Zend_Paginator::factory($projets);
       $paginator->setCurrentPageNumber($this->_getParam('page'));
       $paginator->setItemCountPerPage(250);
       Zend_Paginator::setDefaultScrollingStyle('Sliding');
       Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

       $this->view->paginator = $paginator;
    }

    public function switchAction()
    {
        $id_projet = $this->_getParam('id');
        
        $date = new Zend_Date();
        
        $db_projet = new Application_Model_Projets();
        $projet = $db_projet->find($id_projet)->current();
        
        if($projet->cloture == 1){
	        $projet->cloture = 0;
        }else{
	        $projet->cloture = 1;
			$projet->date_cloture = $date->get('yyyy-MM-dd');
        }
        

        $projet->save();
        
        $this->_redirect('/projets/fiche/id/' . $id_projet);
    }

    public function fichiersAction()
    {
	    $id_projet = $this->_getParam('id');
	    
        $db_projet = new Application_Model_Projets();
        $projet = $db_projet->find($id_projet)->current();
        
        $this->view->projet = $projet;
        
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id');
        
        $db_projet = new Application_Model_Projets();
        
        $db_taches = new Application_Model_Taches();
        $select = $db_taches->select()
        					->where('id_projet = ?', $id);
        $taches = $db_taches->fetchAll($select);
        
        $cal = new Application_Model_Calendrier();
        if( isset($tache->id)  )
        	$cal->delete('id_tache = ' . $tache->id);
/*
        if(count($taches->toArray()) > 0){
	        foreach($taches as $tache){
		        
	        }
        }
*/
		
		$db_projet->delete('id_projet = ' . $id);
		
		$this->_redirect('/projets');
        
    }

    public function deletetacheAction()
    {
	    $this->_helper->layout()->disableLayout();
	    $this->_helper->viewRenderer->setNoRender();
	    
        $id_tache = $this->_getParam('id');
        $id_projet = $this->_getParam('id_projet');
        
        $db_taches = new Application_Model_Taches();
        $db_taches->delete('id = ' . $id_tache);
        
        $db_calendar = new Application_Model_Calendrier();
		$select = $db_calendar->select()->where('id_tache = ?', $id_tache);
		
		$rows = $db_calendar->fetchAll($select);
		if(count($rows->toArray())){
			$db_calendar->delete('id_tache = ' . $id_tache);	
		}
        
		$this->_redirect('/projets/fiche/id/' . $id_projet);
		
    }

    public function deletenoteAction()
    {
        $id_note = $this->_getParam('id_note');
        $id_projet = $this->_getParam('id_projet');
        
        $db_notes = new Application_Model_Notesprojet();
        $db_notes->delete('id = ' . $id_note);
        
        $this->_redirect('/projets/fiche/id/' . $id_projet);
    }

    public function editnoteAction()
    {
        $id_note = $this->_getParam('id_note');
        
        $db_notes = new Application_Model_Notesprojet();
        $note = $db_notes->find($id_note)->current();
        
		$this->view->note = $note;
		
		$formNote = new Application_Form_EditeNote();
		$formNote->populate($note->toArray());
		$formNote->setAction('/projets/sauvenote');
        $this->view->form = $formNote;

        //$this->_redirect('/projets/fiche/id/' . $id_projet);
    }

	public function sauvenoteAction()
    {
        $id_note = $this->_getParam('id');
        
        $db_notes = new Application_Model_Notesprojet();
        $note = $db_notes->find($id_note)->current();
        
        $note->note = $this->_getParam('note'); 

        $note->save();

        $this->_redirect('/projets/fiche/id/' . $note->id_projet);
        
    }

	public function retournoteAction()
    {
        $id_projet = $this->_getParam('id_projet');
        
        $this->_redirect('/projets/fiche/id/' . $id_projet);
    }
}
