<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoload() {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
                    'namespace' => '',
                    'basePath' => APPLICATION_PATH)
        );

        return $moduleLoader;
    }
    
    protected function _initLocale() {
        $locale = new Zend_Locale('fr_FR');
        Zend_Registry::set('Zend_Locale', $locale);
    }
    
     protected function _initViewHelpers() {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();

        $view->setEscape('stripslashes');

        $view->addHelperPath(
                APPLICATION_PATH . "/views/helpers",
                "Zend_View_Helper");

        $view->doctype('HTML5');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
        $view->headTitle('Marne Enseigne');
        $view->headTitle()->setSeparator(' - ');

        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $view->jQuery()->enable();
        $view->jQuery()->uiEnable();
    }
    
/*
    protected function _initPlugins() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new App_Plugins_Navigation());
    }
*/

    protected function _initAdapteur() {
        $this->bootstrap('db');
        $dbAdapter = $this->getPluginResource('db');
        Zend_Registry::set('db', $dbAdapter->getDbAdapter('db'));
    }
    
    protected function _initTranslation() {

		$translator = new Zend_Translate(
			array(
				'adapter' => 'array',
				'content' => APPLICATION_PATH . '/../resources/languages',
				'locale' => 'fr_FR',
				'scan' => Zend_Translate::LOCALE_DIRECTORY
			)
		);
		Zend_Validate_Abstract::setDefaultTranslator($translator);
	}
	
		protected function _initAcl(){
		$acl = new Zend_Acl;
		
		$guest = new Zend_Acl_Role('user');
		
		$acl->addRole($guest);
		$acl->addRole(new Zend_Acl_Role('admin'), 'user');

		$acl->addResource(new Zend_Acl_Resource('user'));
		$acl->addResource(new Zend_Acl_Resource('admin'));

		$acl->allow('user', 'user');
		$acl->allow('admin', 'admin');

		Zend_Registry::set('acl', $acl);
	}


}

