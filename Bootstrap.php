<?php

class ManipleUserConsents_Bootstrap extends Maniple_Application_Module_Bootstrap
{
    public function getModuleDependencies()
    {
        return array(
            'maniple-user',
        );
    }

    public function getResourcesConfig()
    {
        return require dirname(__FILE__) . '/configs/resources.config.php';
    }

    public function getRoutesConfig()
    {
        return require dirname(__FILE__) . '/configs/routes.config.php';
    }

    public function getTranslationsConfig()
    {
        return array(
            'scan'    => Zend_Translate::LOCALE_DIRECTORY,
            'content' => dirname(__FILE__) . '/languages',
        );
    }

    public function getViewConfig()
    {
        return array(
            'scriptPaths' => dirname(__FILE__) . '/views/scripts',
            'helperPaths' => array(
                'ManipleUserConsents_View_Helper_' => dirname(__FILE__) . '/library/ManipleUserConsents/View/Helper/',
            ),
        );
    }

    /**
     * Register autoloader paths
     */
    protected function _initAutoloader()
    {
        Zend_Loader_AutoloaderFactory::factory(array(
            'Zend_Loader_StandardAutoloader' => array(
                'prefixes' => array(
                    'ManipleUserConsents_' => dirname(__FILE__) . '/library/ManipleUserConsents/',
                ),
            ),
        ));
    }

    /**
     * Setup view path spec
     */
    protected function _initViewRenderer()
    {
        /** @var Zefram_Controller_Action_Helper_ViewRenderer $viewRenderer */
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setViewScriptPathSpec(':module/:controller/:action.:suffix', 'maniple-user-consents');
        $viewRenderer->setViewSuffix('twig', 'maniple-user-consents');
    }

    protected function _initSharedEventManager()
    {
        $this->getApplication()->bootstrap('maniple');
        $that = $this;

        /** @var Zend_EventManager_SharedEventManager $sharedEventManager */
        $sharedEventManager = $this->getResource('SharedEventManager');

        $sharedEventManager->attach(
            ManipleUser_Signup_SignupManager::className,
            'createSignupForm',
            function (Zend_EventManager_Event $event) use ($that) {
                /** @var ManipleUserConsents_ConsentManager $consentManager */
                $consentManager = $that->getResource(ManipleUserConsents_ConsentManager::className);
                /** @noinspection PhpParamsInspection */
                $consentManager->onCreateSignupForm($event->getTarget());
            }
        );

        $sharedEventManager->attach(
            ManipleUser_Signup_SignupManager::className,
            'createUser',
            function (Zend_EventManager_Event $event) use ($that) {
                /** @var ManipleUserConsents_ConsentManager $consentManager */
                $consentManager = $that->getResource(ManipleUserConsents_ConsentManager::className);
                /** @noinspection PhpParamsInspection */
                $consentManager->onCreateUser($event->getTarget(), $event->getParam('data'));
            }
        );
    }

    protected function _initControllerPlugins()
    {
        $this->getApplication()->bootstrap('maniple');

        /** @var Zend_Controller_Front $frontController */
        $frontController = $this->getResource('FrontController');
        $frontController->registerPlugin(
            $this->getResource(ManipleUserConsents_Controller_Plugin_UserConsentsGuard::className),
            -1100
        );
    }
}
