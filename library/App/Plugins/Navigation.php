<?php
/**
 * Charge le menu de navigation
 *
 * @author eric
 */
class App_Plugins_Navigation extends Zend_Controller_Plugin_Abstract {

    /**
     * @param Zend_Controller_Request_Abstract $request
     * @throws Zend_Config_Exception
     * @throws Zend_Exception
     * @throws Zend_Navigation_Exception
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;
        
        $auth = Zend_Auth::getInstance();
        
        $acl = Zend_Registry::get('acl');

        $view->controller = $request->getControllerName();

        if ($request->getModuleName() == 'backend') {

            $config = new Zend_Config_Xml(APPLICATION_PATH . "/configs/navigation.xml", 'backend');

            $navigation = new Zend_Navigation($config);

            $view->navigation($navigation);
        } else {
            $config = new Zend_Config_Xml(APPLICATION_PATH . "/configs/navigation.xml", 'frontend');

            $navigation = new Zend_Navigation($config);

            $view->navigation($navigation);
        }

        return;
    }

}