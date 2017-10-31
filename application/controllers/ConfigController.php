<?php

class ConfigController extends Zend_Controller_Action
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
	    $form = new Application_Form_AddPreset();
	    
	    $this->view->form = $form;
	    
        $db_preset = new Application_Model_PresetTaches();
        $select = $db_preset->select()->order('libelle_tache');
        $presets = $db_preset->fetchAll($select);
        
        $this->view->presets = $presets;
        
        if($this->_request->isPost() && $form->isValid($this->_request->getPost())){
	    
	    	$formDatas = $this->_request->getPost();
	    	$row = $db_preset->createRow();
	    	$row->libelle_tache = $formDatas[libelle_tache];
	    	$row->save();
			
			
			$this->_redirect('/config/');
	    }
    }

    public function deletepresetAction()
    {
        $id_tache = $this->_getParam('id');
        
        $db_preset = new Application_Model_PresetTaches();
        $db_preset->delete('id = ' . $id_tache);
        
        $this->_redirect('/config/');
    }


}



