<?php

class AjaxController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_helper->layout()->disableLayout();
    }

    public function indexAction()
    {
		// action body
    }

    public function clientsAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_client = new Application_Model_Clients();

		$r = $db_client->searchClient($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function cataloguefournisseurAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_catalogue = new Application_Model_Catalogue();

		$r = $db_catalogue->searchFournisseur($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function cataloguestAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_catalogue = new Application_Model_Catalogue();

		$r = $db_catalogue->searchSurfaceTotale($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function cataloguereferenceAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_catalogue = new Application_Model_Catalogue();

		$r = $db_catalogue->searchReference($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function cataloguecodemeAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_catalogue = new Application_Model_Catalogue();

		$r = $db_catalogue->searchCodeMe($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function cataloguetypeAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_catalogue = new Application_Model_Catalogue();

		$r = $db_catalogue->searchType($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function catalogueepaisseurAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_catalogue = new Application_Model_Catalogue();

		$r = $db_catalogue->searchEpaisseur($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function catalogueformatAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_catalogue = new Application_Model_Catalogue();

		$r = $db_catalogue->searchFormat($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function cataloguedesignationAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_catalogue = new Application_Model_Catalogue();

		$r = $db_catalogue->searchDesignation($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function additemAction()
    {

		$db_items = new Application_Model_ItemDevis();

		$item = $db_items->createRow();
		$item->id_devis = $this->getParam('id_devis');
		if ((int) $this->getParam('id_item') == 0)
		{
			$item->item = $this->getParam('designation');
		}else{
			$item->id_item = $this->getParam('id_item');
		}
		
		$item->pu = $this->getParam('pu');
		$item->qte = $this->getParam('qte');
		$item->pht = $this->getParam('pht');
		$item->remise = $this->getParam('remise');
		$item->marge = $this->getParam('marge');
		$id_item = $item->save();

		$this->view->id_item = $id_item;
		$this->view->item = $this->getParam('id_item');
		$this->view->designation = $this->getParam('designation');
		$this->view->pu = $this->getParam('pu');
		$this->view->qte = $this->getParam('qte');
		$this->view->pht = $this->getParam('pht');
		$this->view->remise = $this->getParam('remise');
		$this->view->marge = $this->getParam('marge');
    }

    public function refproduitAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_produits = new Application_Model_Produits();
		$r = $db_produits->searchProduit($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function deleteitemAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_items = new Application_Model_ItemDevis();
		$db_items->delete('id = ' . $this->_getParam('id_item'));
    }

    public function ajouttacheAction()
    {
		$db_taches = new Application_Model_Taches();
		if(!(int)$this->_getParam('id_tache')){
			$tache = $db_taches->createRow();
			$tache->id_projet = $this->_getParam('id_projet');
			$tache->tache = $this->_getParam('tache');
			$tache->id_user1 = $this->_getParam('id_user1');
			$tache->id_user2 = $this->_getParam('id_user2');
			$tache->id_user3 = $this->_getParam('id_user3');
			$tache->duree = $this->_getParam('duree');
			$tache->visible = ($this->_getParam('visible')==null)? 0:$this->_getParam('visible');
			$id_tache = $tache->save();
		}else{
			$id_tache = $this->_getParam('id_tache');
			$tache = $db_taches->find($id_tache)->current();
			$tache->tache = $this->_getParam('tache');
			$tache->id_user1 = $this->_getParam('id_user1');
			$tache->id_user2 = $this->_getParam('id_user2');
			$tache->id_user3 = $this->_getParam('id_user3');
			$tache->duree = $this->_getParam('duree');
			$tache->id_projet = $this->_getParam('id_projet');
			$tache->save();
			
			$db_event = new Application_Model_Calendrier();
			$select = $db_event->select()->where('id_tache = ?', $id_tache);
			$events = $db_event->fetchAll($select);
			if (count($events)){
				foreach($events as $event){
					$event->nb_heures = $this->_getParam('duree');
					$event->id_user1 = $this->_getParam('id_user1');
					$event->id_user2 = $this->_getParam('id_user2');
					$event->id_user3 = $this->_getParam('id_user3');
					$event->save();
				}
			}
			
		}

		$this->view->id_tache = $id_tache;
		$this->view->id_projet = $this->_getParam('id_projet');
		$this->view->tache = $this->_getParam('tache');
		$this->view->duree = $this->_getParam('duree');
		$this->view->id_user1 = $this->_getParam('id_user1');
		$this->view->id_user2 = $this->_getParam('id_user2');
		$this->view->id_user3 = $this->_getParam('id_user3');


    }

    public function filtreclientAction()
    {
		$params = $this->_getAllParams();
		
		$session = new Zend_Session_Namespace('filtreClient');
		$session->filtres = $params;

    }

    public function projetsAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$db_projet = new Application_Model_Projets();

		$r = $db_projet->searchProjet($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function saveredactionAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$t = $this->_getParam('t');
		$id_devis = $this->_getParam('id_devis');

		$db_devis = new Application_Model_Devis();
		$devis = $db_devis->find($id_devis)->current();
		$content = $this->_getParam('redaction');
		
		if($t == 'devis'){
			$devis->redaction = $content;
		}else{
			$devis->redaction_facture = $content;
		}

		$devis->save();

    }

    public function validetapeAction()
    {

		$id_tache = $this->_getParam('id_tache');
		$note = $this->_getParam('commentaire');
		
		$db_taches = new Application_Model_Taches();
		$tache = $db_taches->find($id_tache)->current();
		$date = new Zend_Date();
		
		$db_calendar = new Application_Model_Calendrier();
		$select = $db_calendar->select()->where('id_tache = ?', $id_tache);
		
		$r = $db_calendar->fetchAll($select);
		
		foreach($r as $t){
			$t->realise = 1;
			$t->save();
		}
		
		$tache->commentaire = $note;

		if($tache->fait == 0){
			$tache->fait = 1;
			$tache->date_fait = $date->get('yyyy-MM-dd');
			$r= 1;
		}else{
			$tache->fait = 0;
			$tache->date_fait = '0000-00-00';
			$r = 0;
		}
		$tache->save();

		$this->view->etat = $r;
		$this->view->date_fait = $date->get('dd/MM/yyyy');
		$this->view->note = $note;

    }

    public function masktacheAction()
    {
    	$id_tache = $this->_getParam('id_tache');
    	$db_taches = new Application_Model_Taches();
    	$tache = $db_taches->find($id_tache)->current();
        $tache->visible = $this->_getParam('visible');
    	$tache->save();
    }

    public function deletetacheAction()
    {
		$id_tache = $this->getParam('id_tache');

		$db_calendar = new Application_Model_Calendrier();
		$select = $db_calendar->select()->where('id_tache = ?', $id_tache);
		
		$rows = $db_calendar->fetchAll($select);
		if(count($rows->toArray())){
			$db_calendar->delete('id_tache = ' . $id_tache);	
		}
		
		$db_taches = new Application_Model_Taches();
		$db_taches->delete('id = ' . $id_tache);
		
    }

    public function addeventAction()
    {
		$date = $this->_getParam('date');
		$event = $this->_getParam('event');
		$id_user = $this->_getParam('id_user');
		$nbh = $this->_getParam('nb_heures');
		$priority = $this->_getParam('priority');

		$db_event = new Application_Model_Calendrier();
		$ev = $db_event->createRow();
		$ev->date = $date;
		$ev->event = $event;
		$ev->id_user1 = $id_user;
		$ev->nb_heures = $nbh;
		$ev->priority = $priority;
		$ev->realise = 0;
		$id = $ev->save();

		$this->view->id = $id;
		$this->view->event = $event;
		$this->view->id_user1 = $id_user;
		$this->view->nbh = $nbh;
		$p = '';
		for($i=0; $i< $priority; $i++){
			$p .= '!';
		}
		$this->view->priority = $p;
    }

    public function addtachetocalAction()
    {
		$this->_helper->viewRenderer->setNoRender();
		
		$id_user = $this->_getParam('id_user');
		$id_tache = $this->_getParam('id_tache');
		$date = $this->_getParam('date');
		$nbh = $this->_getParam('nb_heures');
		$priority = $this->_getParam('priority');
		
		$db_calendar = new Application_Model_Calendrier();
		$db_calendar->delete('id_tache = ' . $id_tache);
		
		$users = $this->getUsersInTaches($id_tache);
		
		$add = $db_calendar->createRow();
		$add->id_user1 = $users[0];
		$add->id_user2 = $users[1];
		$add->id_user3 = $users[2];
		$add->id_tache = $id_tache;
		$add->priority = $priority;
		list($j,$m,$y) = explode('/', $date);
		$ts = $y . '-' . $m . '-' . $j;
		$add->date = $ts;
		$add->nb_heures = $nbh;
		$add->realise = 0;
		$id = $add->save();

		echo $id?$id:0;
    }

    public function uploadAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$id_projet = $this->_getParam('id_projet');

		$output_dir = APPLICATION_PATH . "/../public/files/";
		$output_dir_projet = APPLICATION_PATH . "/../public/files/" . $id_projet;

		if(!is_dir($output_dir_projet)){
			mkdir($output_dir_projet);
		}

		if(isset($_FILES["myfile"]))
		{
			$ret = array();

			$error =$_FILES["myfile"]["error"];
			//You need to handle  both cases
			//If Any browser does not support serializing of multiple files using FormData()
			if(!is_array($_FILES["myfile"]["name"])) //single file
				{
				$fileName = $_FILES["myfile"]["name"];
				move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir_projet . "/" . strtolower($fileName));
				$ret[]= $fileName;
			}
			else  //Multiple files, file[]
				{
				$fileCount = count($_FILES["myfile"]["name"]);
				for($i=0; $i < $fileCount; $i++)
				{
					$fileName = $_FILES["myfile"]["name"][$i];
					move_uploaded_file($_FILES["myfile"]["tmp_name"][$i],$output_dir_projet . "/" . strtolower($fileName));
					$ret[]= $fileName;
				}

			}
			echo json_encode($ret);
		}

    }

    public function refreshdirAction()
    {
		$id_projet = $this->_getParam('id_projet');

		if($handle = @opendir(APPLICATION_PATH . "/../public/files/" . $id_projet)){

			while(false !== ($ligne = readdir($handle))){
				if($ligne != '.' && $ligne != '..'){
					$dirs[] = $ligne;
				}
			}
		}

		$this->view->dirs = $dirs;
		$this->view->id_projet= $id_projet;
    }

    public function dlAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$file = $this->_getParam('f');
		$dir = 'files/' . $this->_getParam('dir') . '/' . $file;

		if(file_exists($dir) && !is_dir($dir)){
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($dir));
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: ' . filesize($dir));

			ob_clean();
			flush();
			readfile($dir);
		}else{
			echo "<h1>Echec de téléchargement, fichier introuvable : " . $dir . "</h1>";
		}

    }

    public function deleteAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$file = $this->_getParam('f');

		if(unlink('files/' . $file)){
			echo 'ok';
		}

    }

    public function validdevisAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$id_devis = $this->_getParam('id_devis');

		$db_devis = new Application_Model_Devis();
		$devis = $db_devis->find($id_devis)->current();
		$date = new Zend_Date();
		$devis->date_validation = $date->get('yyyy-MM-dd');
		$devis->valide = 1;
		if($devis->save()){
			echo 1;
		}else{
			echo 0;
		}

    }

    public function etapesAction()
    {
		$this->_helper->viewRenderer->setNoRender();

		$etat = 0;// ($this->_getParam('etat'))? 0:1;

		$db_taches = new Application_Model_Taches();
		$select = $db_taches->select()->where('tache like ?', $this->_getParam('term'). '%')->where('id_projet in (select id_projet from projets where fait = 0 and cloture = ?)', $etat)->group('tache');
		$taches = $db_taches->fetchAll($select);

		foreach($taches as $tache){
			$tmp[] = '{ "id":"' . $tache->id . '", "value":"' . $tache->tache . '"}';
		}

		echo '[' . new Zend_Json_Expr(implode(',', $tmp)) . ']';
    }

    public function filtreprojetAction()
    {

		$params = $this->_getAllParams();
		
		$session = new Zend_Session_Namespace('filtreProjet');
		$session->filtres = $params;
		
		$this->view->currentUser = $params[user];

    }

    public function filtrefactureAction()
    {
		$params = $this->_getAllParams();

		$db_devis = new Application_Model_Devis();

		$select = $db_devis->select()->from(array('f'=>'devis'));

		if((int) $params[annee] && (int) $params[mois]){
			$select->where('date like ?', $params[annee] . '-' . $params[mois] . '-%');
		}

		if((int) $params[id_client]){
			$select->where('id_client = ?', $params[id_client]);
		}

		if(strlen(trim($params[client])) > 0){
			$select->join(array('c'=>'clients'), 'c.societe like "%' . $params[client] . '%" AND c.id_client = f.id_client');
		}

		if((int) $params[id_projet]){
			$select->where('f.id_projet = ?', $params[id_projet]);
		}

		if(strlen(trim($params[projet])) > 0){
			$select->where('titre like ?', '%' . $params[projet] . '%');
		}

		$select->where('valide = ?', $params[valide]);
		$select->setIntegrityCheck(false);
		echo $select;
		$rows = $db_devis->fetchAll($select);

		$this->view->factures = $rows;
    }

    public function reglementsAction()
    {
        $this->_helper->viewRenderer->setNoRender();

		$db_reglt = new Application_Model_Reglements();

		$r = $db_reglt->searchReglt($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function tachesAction()
    {
        $this->_helper->viewRenderer->setNoRender();

		$db_preset = new Application_Model_PresetTaches();

		$r = $db_preset->searchPreset($this->_getParam('term'));

		echo '[' . $r . ']';
    }

    public function moveeventAction()
    {
	    $this->_helper->viewRenderer->setNoRender();
	    
        $id_tache = $this->_getParam('id_tache');
        $new_date = $this->_getParam('new_date');
        
        $db_calendar = new Application_Model_Calendrier();
        $event = $db_calendar->find($id_tache)->current();
        $event->date = $new_date;
        $event->save();
    }

    public function updatesortAction()
    {
	    $this->_helper->viewRenderer->setNoRender();
	    
        $list = $this->_getParam('list');
        $rang = 1;
        
		foreach($list as $value){
			list($tmp, $id) = explode('_', $value);
			$db_taches = new Application_Model_Calendrier();
			$tache = $db_taches->find($id)->current();
			$tache->rang = $rang;
			$tache->save();
			$rang++;
		}
		
    }

    public function produitAction()
    {
	    $this->_helper->viewRenderer->setNoRender();
	    
        $id = $this->_getParam('id');
        
        $db_catalogue = new Application_Model_Produits();
        $catalogue = $db_catalogue->find($id)->current();
        
        if($catalogue->unitaire == null){
	        if($catalogue->prixM2 == null){
		        $prixU = $catalogue->prixML;
	        }else{
		        $prixU = $catalogue->prixM2;
	        }
        }else{
	        $prixU = $catalogue->unitaire;
        }
        
        //echo $catalogue->designation . '||' . $prixU . '||' . $catalogue->coeff_marge . '||' . $id;
        echo $catalogue->code_me;
    }

    public function filtrecatalogueAction()
    {
	    
// 	    var_dump($this->_getAllParams());
	    
        $ref = $this->_getParam('ref');
        $code_me = $this->_getParam('codeMe');
        $fournisseur = $this->_getParam('fournisseur');
        $designation = $this->_getParam('designation');
        $type = $this->_getParam('type');
        $surfaceTotale = $this->_getParam('surface_totale');
        $epaisseur = $this->_getParam('epaisseur');

        $fromDevis = $this->_getParam('fromDevis');
        
        $db_catalogue = new Application_Model_Produits();
        $select = $db_catalogue->select();
        
        if(strlen(trim($ref))){
	        $select->where('reference like ?', $ref . '%');
        }

        if(strlen(trim($code_me))){
	        $select->where('code_me like ?', $code_me . '%');
        }

        if(strlen(trim($epaisseur))){
	        $select->where('epaisseur = ?', $epaisseur);
        }
        
        if(strlen(trim($type))){
	        $select->where('type like ?', $type . '%');
        }
        
        if(strlen(trim($fournisseur))){
	        $select->where('fournisseur like ?', $fournisseur . '%');
        }

        if(strlen(trim($surfaceTotale))){
	        $select->where('surface_totale like ?', $surfaceTotale . '%');
        }

        if(strlen(trim($designation))){
	        $select->where('designation like ?', '%' . $designation . '%');
        }
        
        $catalogues = $db_catalogue->fetchAll($select);

        $this->view->fromDevis = $fromDevis;
        $this->view->catalogues = $catalogues;
        			
    }

    public function savemodeleAction()
    {
        $nom = $this->_getParam('nom');
        $contenu = $this->_getParam('contenu');
        
        $db_modele = new Application_Model_Modeles();
        
        $row = $db_modele->createRow();
        $row->titre = $nom;
        $row->contenu = $contenu;
        $row->save();
        
        $rows = $db_modele->fetchAll();
        $this->view->rows = $rows;
    }

    public function getmodeleAction()
    {
	    $this->_helper->viewRenderer->setNoRender();
	    
        $id = $this->_getParam('id');
        
        $db_modele = new Application_Model_Modeles();
        $modele = $db_modele->find($id)->current();
        
        echo $modele->contenu;
        
    }

    public function resetfiltreclientAction()
    {
	   $this->_helper->viewRenderer->setNoRender();
       $session = new Zend_Session_Namespace('filtreClient');
       unset($session->filtres);
    }

    public function resetfiltreprojetAction()
    {
	   $this->_helper->viewRenderer->setNoRender();
       $session = new Zend_Session_Namespace('filtreProjet');
       unset($session->filtres);
    }

    public function getadresseAction()
    {
        $client = $this->_getParam('id_client');
        
        $db_client = new Application_Model_Clients();
        $client = $db_client->find($client)->current();
        
        $this->view->adresse = $client->adresse;
        $this->view->cp = $client->codepostal;
        $this->view->ville = $client->ville;
        $this->view->contact_nom = $client->contact_nom;
        $this->view->contact_mail = $client->contact_mail;
        $this->view->contact_tel = $client->contact_tel;
        $this->view->ref = $client->ref;
        
    }

    public function getinfosAction()
    {
        $dossier = $this->_getParam('dossier');
        
        $db_projet = new Application_Model_Projets();
        $select = $db_projet->select()
        					->where('dossier = ?', $dossier);
        
        $row = $db_projet->fetchRow($select);
        
        $db_client = new Application_Model_Clients();
        $client = $db_client->find($row->id_client)->current();
        
        $this->view->client = $client;
        $this->view->projet = $row;

    }

    public function cloturesAction()
    {
	    $session = new Zend_Session_Namespace('filtreProjet');
		$session->filtres['cloture'] = 1;
		
        $db_projets = new Application_Model_Projets();
        $select = $db_projets->select()->where('cloture = 1');
        
        $this->view->projets = $db_projets->fetchAll($select);
    }

    public function updatesemAction()
    {
	    $this->_helper->viewRenderer->setNoRender();
	    
        $id_projet = $this->_getParam('projet');
        $sem = $this->_getParam('sem');
        
        $db_projet = new Application_Model_Projets();
        $projet = $db_projet->find($id_projet)->current();
        
        $projet->sem = $sem;
        $projet->save();
    }

    public function filtrecloturesAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        
		$params = $this->_getAllParams();
		
		$session = new Zend_Session_Namespace('filtreProjetsClotures');
		$session->filtres = $params;

    }

    public function resetfiltreprojetcloturesAction()
    {
       $this->_helper->viewRenderer->setNoRender();
       $session = new Zend_Session_Namespace('filtreProjetsClotures');
       unset($session->filtres);
    }

    public function getUsersInTaches($id_taches)
    {
	    
	    $db_taches = new Application_Model_Taches();
	    $tache = $db_taches->find($id_taches)->current();

	    
	    $users = [$tache->id_user1, $tache->id_user2, $tache->id_user3];
	    
	    return $users;
	    
    }

    public function codeclientAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        
        $codeclient = $this->_getParam('codeclient');

		$db_client = new Application_Model_Clients();
		$r = $db_client->searchCodeClient($codeclient);

		echo '[{"datas":{' . $r . '}}]';
    }

    public function getnewmessageAction()
    {
	    $this->_helper->viewRenderer->setNoRender();
	    
        $auth = Zend_Auth::getInstance();
        $db_messages = new Application_Model_Messages();
		$select = $db_messages->select()
							->where('vu = 0')
							->where('id_destinataire = ?', $auth->getIdentity()->id_user);
		
		$receivedmessages = $db_messages->fetchAll($select);
		
		echo count($receivedmessages);
    }

    public function gettacheinfosAction()
    {
	    $this->_helper->viewRenderer->setNoRender();
	    
        $id = $this->_getParam('id');
        
        $db_tache = new Application_Model_Taches();
        $tache = $db_tache->find($id)->current();
        
        echo $tache->id.'|||'.$tache->tache.'|||'.$tache->id_user1.'|'.$tache->id_user2.'|'.$tache->id_user3.'|||'.$tache->duree;
    }

    public function changecolorprojetAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
	    
	    $id_projet = $this->_getParam('id_projet');
	    $nomColor = $this->_getParam('colorclass');

	    $db_projet = new Application_Model_Projets();
        $projet = $db_projet->find($id_projet)->current();
        
        $projet->colorclass = $nomColor;
        $projet->save();
    }

    public function switchtodevisarelancerAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
	    
	    $id_projet = $this->_getParam('id_projet');
	    
	    $db_projet = new Application_Model_Projets();
        $projet = $db_projet->find($id_projet)->current();

        if($projet->devisarelancer != 1)
            $projet->devisarelancer = (1);
        else
            $projet->devisarelancer = (0);
        $projet->save();	
    }

    public function switchtoenattenteAction()
    {
    	$this->_helper->viewRenderer->setNoRender();

	    $id_projet = $this->_getParam('id_projet');

	    $db_projet = new Application_Model_Projets();
        $projet = $db_projet->find($id_projet)->current();

        if($projet->devisarelancer != 2)
            $projet->devisarelancer = (2);
        else
            $projet->devisarelancer = (0);
        $projet->save();
    }

    public function panelproduitsAction()
    {

    }
    public function panelsoustraitanceAction()
    {

    }
    public function panelprestationAction()
    {

    }
    public function panelfaconnageAction()
    {

    }
    public function panelfraistechniquesAction()
    {

    }
    public function paneladhesifAction()
    {

    }
    public function panelforfaitprestationAction()
    {

    }
    public function paneldeplacementAction()
    {

    }
    public function panelfournitureAction()
    {

    }
    public function panelposeAction()
    {

    }

    public function detailproduitAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $idSearch = $this->_getParam('id');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('id_produit = ?', $idSearch);
        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= Zend_Json::encode($produit);
        }

        echo($str);
    }

    public function listeclientsAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_clients = new Application_Model_Clients();
        $select = $db_clients->select();
        $select->where('ref = ?', $codeSearch.'');
        $select->orWhere('contact_nom like ?', '%'.$codeSearch.'%');
        $select->order('contact_nom');

        $clients = $db_clients->fetchAll($select);

        $str = '';
        $ind = 0;
        foreach ($clients as $client)
        {
            if ($ind<50) {
                $str .= '<option value="' . $client->societe .' '.$client->ref. '" data-ref="' . $client->ref . '" data-id="' . $client->id_client . '" />' . $client->societe . '</option>';
            }
            $ind++;
        }

        echo($str);
    }

    public function listedossierAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $str = '';
        if (strlen($codeSearch)> 1) {
            $db_projet = new Application_Model_Projets();
            $select = $db_projet->select();
            $select->where('dossier like ?', $codeSearch . '%');
            $select->order('dossier');

            $projets = $db_projet->fetchAll($select);

            $ind = 0;
            foreach ($projets as $projet) {
                if ($ind < 50) {
                    $db_client = new Application_Model_Clients();
                    $selectClient = $db_client->select();
                    $selectClient->where('id_client = ?', $projet->id_client);

                    $clients = $db_client->fetchAll($selectClient);

                    foreach ($clients as $client) {
                        $str .= '<option value="' . $projet->dossier . '" 
                            data-idClient="' . $projet->id_client . '" 
                            data-refClient="' . $client->ref . '" 
                            data-societeClient="' . $client->societe . '" 
                            data-titre="' . $projet->titre . '" />' . $projet->dossier . '</option>';
                    }
                }
                $ind++;
            }
        }
        echo($str);
    }

    public function listeproduitsAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $intMax = 20;

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('upper(code_me) like upper(?)', $codeSearch.'%');
        //$select->where('type like ?', 'PVC');
        $select->order('code_me')->limit($intMax, 0);

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);

    }

    public function detailadhesifAction()
    {
        $this->detailproduitAction();
    }

    public function listeadhesifsAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('code_me like ?', $codeSearch.'%');
        $select->order('code_me');

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);
    }
    public function detailsoustraitanceAction()
    {
        $this->detailproduitAction();
    }

    public function listesoustraitancesAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('code_me like ?', $codeSearch.'%');
        $select->order('code_me');

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);
    }

    public function detailprestationAction()
    {
        $this->detailproduitAction();
    }

    public function listeprestationsAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('code_me like ?', $codeSearch.'%');
        $select->where('type like ?', 'PRESTATION');
        $select->order('code_me');

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);
    }

    public function detailfaconnageAction()
    {
        $this->detailproduitAction();
    }

    public function listefaconnagesAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('code_me like ?', $codeSearch.'%');
        $select->order('code_me');

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);
    }

    public function detailfraistechniqueAction()
    {
        $this->detailproduitAction();
    }

    public function listefraistechniquesAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('code_me like ?', $codeSearch.'%');
        $select->order('code_me');

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);
    }

    public function detailforfaitprestationAction()
    {
        $this->detailproduitAction();
    }

    public function listeforfaitprestationsAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('code_me like ?', $codeSearch.'%');
        $select->order('code_me');

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);
    }

    public function detaildeplacementAction()
    {
        $this->detailproduitAction();
    }

    public function listedeplacementsAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('code_me like ?', $codeSearch.'%');
        $select->order('code_me');

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);
    }

    public function detailfournitureAction()
    {
        $this->detailproduitAction();
    }

    public function listefournituresAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('code_me like ?', $codeSearch.'%');
        $select->order('code_me');

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);
    }

    public function detailposeAction()
    {
        $this->detailproduitAction();
    }

    public function listeposesAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $codeSearch = $this->_getParam('code');

        $db_catalogue = new Application_Model_Catalogue();
        $select = $db_catalogue->select();
        $select->where('code_me like ?', $codeSearch.'%');
        $select->where('type like ?', 'PRESTATION%POSE');
        $select->order('code_me');

        $produits = $db_catalogue->fetchAll($select);

        $str = '';
        foreach ($produits as $produit)
        {
            $str .= '<option value="'.$produit->code_me.'" data-id="'.$produit->id_produit.'" />'.$produit->designation.'</option>';
        }

        echo($str);
    }

}



