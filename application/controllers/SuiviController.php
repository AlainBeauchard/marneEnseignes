<?php

class SuiviController extends Zend_Controller_Action
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
        $currentYear = date('Y');
        
        $db_devis = new Application_Model_Devis();
        for($i=1; $i <= (int)date('m'); $i++){
	        $select = $db_devis->select()
	        					->where('date like ?', date('Y') . '-' . date('m', mktime(0,0,0,$i, 1, date('Y'))).'-%');
	        $r = $db_devis->fetchAll($select);
			$num_devis[$i] = count($r);
        }
        
        for($i=1; $i <= (int)date('m'); $i++){
	        $select = $db_devis->select()
	        					->where('date like ?', date('Y') . '-' . date('m', mktime(0,0,0,$i, 1, date('Y'))).'-%')
	        					->where('valide = 1');
	        $r = $db_devis->fetchAll($select);
			$num_devis_acceptes[$i] = count($r);
        }
        
        for($i=1; $i <= (int)date('m'); $i++){
	        $select = $db_devis->select()
	        					->where('date like ?', date('Y') . '-' . date('m', mktime(0,0,0,$i, 1, date('Y'))).'-%')
	        					->where('facture = 1')
	        					->where('paye = 0');
	        $r = $db_devis->fetchAll($select);
			$num_factures[$i] = count($r);
        }
        
        for($i=1; $i <= (int)date('m'); $i++){
	        $select = $db_devis->select()
	        					->where('date like ?', date('Y') . '-' . date('m', mktime(0,0,0,$i, 1, date('Y'))).'-%')
	        					->where('paye = 1');
	        $r = $db_devis->fetchAll($select);
			$num_factures_payees[$i] = count($r);
        }
        
        for($i=1; $i <= (int)date('m'); $i++){
 	        $d = date('Y') . '-' . date('m', mktime(0,0,0,$i, 1, date('Y')));
			$ca_factures_dues[$i] = $db_devis->getTotalFactures($d, 0);
        }
        
        for($i=1; $i <= (int)date('m'); $i++){
	        $d = date('Y') . '-' . date('m', mktime(0,0,0,$i, 1, date('Y')));
			$ca_factures_payees[$i] = $db_devis->getTotalFactures($d, 1);
        }
        
        $this->view->num_devis = $num_devis;
        $this->view->num_devis_acceptes = $num_devis_acceptes;
        $this->view->num_factures = $num_factures;
        $this->view->num_factures_payees = $$num_factures_payees;
        $this->view->ca_factures_dues = $ca_factures_dues;
        $this->view->ca_factures_payees = $ca_factures_payees;
        

    }


}

