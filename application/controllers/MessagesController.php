<?php

class MessagesController extends Zend_Controller_Action
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
		$response->insert('sidebar', $this->view->render('sidebarmessages.phtml'));  
    }

    public function indexAction()
    {

        $auth = Zend_Auth::getInstance();
        
        $db_messages = new Application_Model_Messages();
		$select = $db_messages->select()
							->where('id_destinataire = ?', $auth->getIdentity()->id_user)
							->orWhere('id_destinataire = NULL')
							->order('date DESC');
							
		$receivedmessages = $db_messages->fetchAll($select);
	   
		$this->view->receivedmessages = $receivedmessages;
		
		$select = $db_messages->select()->where('id_user = ?', $auth->getIdentity()->id_user)->order('date DESC');
		$sendmessages = $db_messages->fetchAll($select);
	   
		$this->view->sendmessages = $sendmessages;
	   
 
    }

    public function viewAction()
    {
	    $auth = Zend_Auth::getInstance();
	    
        $db_messages = new Application_Model_Messages();
        $message = $db_messages->find($this->_getParam('id'))->current();
        
        if ($message->id_user != $auth->getIdentity()->id_user)
        {
	        $message->vu = 1;
	        $date = new Zend_Date();
	        $message->date_vu = $date->get('YYYY-MM-dd');
	        if($message->heure_vu == null){
		    	$message->heure_vu = $date->get('H:mm');    
	        }
	        $message->save();
        }
        
        $this->view->message = $message;
    }

    public function deleteAction()
    {
       $this->_helper->viewRenderer->setNoRender();

		$db_items = new Application_Model_Messages();
		$db_items->delete('id_message = ' . $this->_getParam('id'));
		
		$this->_redirect("/messages");
    }

    public function newAction()
    {
	    $dossier = $this->_getParam('dossier');
	    
	    $auth = Zend_Auth::getInstance();
	    $db_messages = new Application_Model_Messages();
        $form = new Application_Form_Messages();
        
		if(strlen(trim($dossier))){
		    $db_projet = new Application_Model_Projets();
			$select = $db_projet->select()->from(array('p'=>'projets'));
			
			$select->join(array('c'=>'clients'), 
					'c.id_client = p.id_client');
			
			$select->where('dossier = ?', $dossier);
			
			$select->setIntegrityCheck(false);
			
			$infos = $db_projet->fetchRow($select);
			
			$form->getElement('titre')->setValue($infos->societe . " : " . $infos->titre);
			$form->getElement('dossier')->setValue($dossier);
	    }
	    
        $this->view->form = $form;
        
        if($this->_request->isPost() )
        {	
			$tabPost = $this->_request->getPost();
			unset( $tabPost['id_destinataire'] );
			if( $form->isValid( $tabPost ) )
			{
		        $formDatas = $this->_request->getPost();
		        
				//On sauvegarde plusieurs messages ... pour tous les destinataires (comme ça pas de modif d'analyse)
		        $idDestinataires = $formDatas['id_destinataire_plus'] ;
		        array_push($idDestinataires, $formDatas['id_destinataire']);

				if( is_array($idDestinataires) )
				{
			        foreach($idDestinataires as $id_destinataire)
			        {
			        	if( $id_destinataire>0)
			        	{
					        $message = $db_messages->createRow($formDatas);
					        $message->id_user = $auth->getIdentity()->id_user;
					        $message->id_destinataire = $id_destinataire;
					        $date = new Zend_Date();
					        $message->date = $date->get('yyyy-MM-dd');
					        $message->heure = $date->get('H:mm');
					        $message->vu = 0;
					        $message->save();
					        //var_dump( $id_destinataire );
					    }
				    }
				}
				else
				{
						$message = $db_messages->createRow($formDatas);
				        $message->id_user = $auth->getIdentity()->id_user;
				        $date = new Zend_Date();
				        $message->date = $date->get('yyyy-MM-dd');
				        $message->heure = $date->get('H:mm');
				        $message->vu = 0;
				        $message->save();
				}
		        
		        $this->_redirect('/messages/');
		    }
        }
        
        
    }

    public function sentAction()
    {
	    $auth = Zend_Auth::getInstance();
	    
	    $db_messages = new Application_Model_Messages();
        $select = $db_messages->select()
            ->where('id_user = ?', $auth->getIdentity()->id_user)
            ->where('date >= ? ', date("Y-m-d", strtotime("-1 year")))
            ->order('date DESC');
		$sendmessages = $db_messages->fetchAll($select);
	   
		$this->view->sendmessages = $sendmessages;
    }

    public function reponseAction()
    {
	    $rep = $this->_getParam('reponse');

       	$auth = Zend_Auth::getInstance();
	    $db_messages = new Application_Model_Messages();
	    
        $form = new Application_Form_Messages();
        $form->setAction("/messages/saveresponse/reponse/" . $rep);
        
        $form->setAttrib("id", "responseMessage");
        
        $this->view->form = $form;
        
        $this->view->messageInitial = $message->titre;
        
	    $message = $db_messages->find($rep)->current();
		$form->populate($message->toArray());
		$form->getElement('titre')->setValue('RE : ' . $message->titre);
		$form->getElement('message')->setValue(strip_tags($message->message));
		$form->getElement('id_destinataire')->setValue(strip_tags($message->id_user));
        

    }

    public function saveresponseAction()
    {
	    $rep = $this->_getParam('reponse');
	    $auth = Zend_Auth::getInstance();
	    
	    $db_messages = new Application_Model_Messages();
	    
	    $form = new Application_Form_Messages();
	    
        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
	        $formDatas = $this->_request->getPost();
	        
	        $reponse = $db_messages->createRow($formDatas);
	        $reponse->message = $formDatas[message];
	        $reponse->id_user = $auth->getIdentity()->id_user;
	        $date = new Zend_Date();
	        $reponse->date = $date->get('yyyy-MM-dd');
	        $reponse->heure = $date->get('H:mm');
	        $reponse->reponse = $rep;
	        $reponse->vu = 0;
	        $reponse->save();
			
			$this->_redirect("/messages/index");
        }
    }


}













