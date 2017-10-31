<?php

class IndexController extends Zend_Controller_Action
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
        
   	}

    public function indexAction()
    {
	    $auth = Zend_Auth::getInstance();
	   //liste des messages
	    
	   $db_messages = new Application_Model_Messages();
	   $select = $db_messages->select()
	   						->where('(id_destinataire = ' . $auth->getIdentity()->id_user . ' OR id_destinataire = NULL)')
	   						->where('vu = 0')
	   						->order('date DESC')->limit(5,0);
	   $messages = $db_messages->fetchAll($select);
	   
	   $this->view->messages = $messages;
       
       // liste des devis en attente de signature
       
       $db_devis = new Application_Model_Devis();
       $select = $db_devis->select()->where('valide = 0')->order('date_validite')->limit(10,0);
       $devis = $db_devis->fetchAll($select);
       
       $this->view->devis = $devis;
       
       // liste des fprojets à facturer
       
       $db_projet = new Application_Model_Devis();
       $select = $db_projet->select()->where('facture = ?', 1)->where('paye = ?', 0)->order('date_facture ASC')->limit(10,0);
       $factures = $db_projet->fetchAll($select);
       
       $this->view->factures = $factures;
       
       
    }

    public function decoAction()
    {
        // action body
    }


}





