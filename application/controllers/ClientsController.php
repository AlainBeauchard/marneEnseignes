<?php

class ClientsController extends Zend_Controller_Action
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
        $response->insert('sidebar', $this->view->render('sidebarclient.phtml'));
    }

    public function indexAction()
    {
	    $session = new Zend_Session_Namespace('filtreClient');
	    
        $filtreClient = new Application_Form_FiltreClient();
        
        if($this->_request->isPost() && $filtreClient->isValid($this->_request->getPost())){
			
			$formDatas = $this->_request->getPost();
			$params = $this->_request->getPost();
			$session->filtres = $params;
		}
        
        if($session->filtres != null){
	        $filtreClient->populate($session->filtres);
        }
        $filtreClient->getElement('type_filtre')->setValue('client');
        
        $this->view->filtreClient = $filtreClient;
		
		$db_client = new Application_Model_Clients();
		$select = $db_client->select();

		if(strlen(trim($session->filtres[num_client]))){
			$select->where('ref = ?', $session->filtres[num_client]);
		}
		if(strlen(trim($session->filtres[client]))){
			$select->where('societe like ?', '%' . $session->filtres[client] . '%');
		}
		if(strlen(trim($session->filtres[adresse]))){
			$select->where('(adresse like "%' . $session->filtres[adresse] . '%" OR codepostal like "%' . $session->filtres[adresse] . '%" OR ville like "%' . $session->filtres[adresse] . '%")');
		}
		
		$select->order('societe');
        $clients = $db_client->fetchAll($select);
                
        $paginator = Zend_Paginator::factory($clients);
		$paginator->setItemCountPerPage(100);
		$paginator->setCurrentPageNumber($this->_getParam('page'));
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

		$this->view->paginator = $paginator;
    }

    public function ajouterAction()
    {
		$form = new Application_Form_Client();
		$this->view->form = $form;
		
		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			$db_client = new Application_Model_Clients();
			$row = $db_client->createRow($this->_request->getPost());
			$row->prospect = 1;
			if($id = $row->save()){
				$this->_redirect('/clients/editer/id/' . $id);
			}
		}
		
    }

    public function editerAction()
    {
        $form = new Application_Form_Client();
		$this->view->form = $form;
		
		$this->view->id_client = $this->_getParam('id');
		
		$db_client = new Application_Model_Clients();
		$client = $db_client->find($this->_getParam('id'))->current();
		$form->populate($client->toArray());
		
		if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
			foreach($this->_request->getPost() as $key => $value){
				if(strlen($value)){
					$datas[$key] = $value;
				}
			}
			if($db_client->update($datas, array('id_client = ?' => $this->_getParam('id')))){
				$this->_redirect('/clients/editer/id/' . $this->_getParam('id'));
			}
			
		}
    }

    public function ficheAction()
    {
        $id = $this->_getParam('id');
        
        $db_client = new Application_Model_Clients();
        $client = $db_client->find($id)->current();
        
        $this->view->client = $client;
        
        $db_projet = new Application_Model_Projets();
        $select = $db_projet->select()->where('id_client = ?', $id)->order('date_commande DESC');
        $projets = $db_projet->fetchAll($select);
        
        $this->view->projets = $projets;
        
        $db_devis = new Application_Model_Devis();
        $select = $db_devis->select()->where('id_client = ?', $id)->order('date DESC');
        $devis = $db_devis->fetchAll($select);
        
        $this->view->devis = $devis;
        
        $noteClient = new Application_Form_NotesClient();
        $noteClient->getElement('id_client')->setValue($id);
        $noteClient->getElement('note')->setValue($client->note);

        $this->view->noteClient = $noteClient;
        
        if($this->_request->isPost() && $noteClient->isValid($this->_request->getPost())){
	    
	    	$formDatas = $this->_request->getPost();
	    	$client->note = $formDatas[note];
	    	$client->save();
	    	   
	    }
    }

    public function deleteAction()
    {
        $db_client = new Application_Model_Clients();
        $db_client->delete('id_client = ' . $this->_getParam('id'));
        
        $this->_redirect('/clients/');
    }


}









