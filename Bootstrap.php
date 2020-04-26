<?php

class ManipleUserConsents_Bootstrap extends Maniple_Application_Module_Bootstrap
{
    public function getModuleDependencies()
    {
        return array(
            'maniple-core',
            'maniple-user',
        );
    }

    public function getResourcesConfig()
    {
        return require __DIR__ . '/configs/resources.config.php';
    }

    public function getRoutesConfig()
    {
        return require __DIR__ . '/configs/routes.config.php';
    }

    public function getTranslationsConfig()
    {
        return array(
            'scan'    => Zend_Translate::LOCALE_DIRECTORY,
            'content' => __DIR__ . '/languages',
        );
    }

    public function getViewConfig()
    {
        return array(
            'scriptPaths' => __DIR__ . '/views/scripts',
            'helperPaths' => array(
                'ManipleUserConsents_View_Helper_' => __DIR__ . '/library/ManipleUserConsents/View/Helper/',
            ),
            'scriptPathSpec' => ':module/:controller/:action.:suffix',
            'suffix' => 'twig',
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend_Loader_StandardAutoloader' => array(
                'prefixes' => array(
                    'ManipleUserConsents_' => __DIR__ . '/library/ManipleUserConsents/',
                ),
            ),
        );
    }

    protected function _initSharedEventManager()
    {
        $this->getApplication()->bootstrap('maniple');
        $that = $this;

        /** @var Zend_EventManager_SharedEventManager $sharedEventManager */
        $sharedEventManager = $this->getResource('SharedEventManager');

        $sharedEventManager->attach(
            ManipleUser_Service_Signup::className,
            'createSignupForm',
            function (Zend_EventManager_Event $event) use ($that) {
                /** @var ManipleUserConsents_ConsentManager $consentManager */
                $consentManager = $that->getResource(ManipleUserConsents_ConsentManager::className);
                /** @noinspection PhpParamsInspection */
                $consentManager->onCreateSignupForm($event->getTarget());
            }
        );

        $sharedEventManager->attach(
            ManipleUser_Service_Signup::className,
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

    protected function _initSettingsManager()
    {
        $this->getApplication()->bootstrap('maniple');

        /** @var Zend_EventManager_SharedEventManager $sharedEventManager */
        $sharedEventManager = $this->getResource('SharedEventManager');
        $sharedEventManager->attach(
            ManipleCore_Settings_SettingsManager::className,
            'init',
            function (Zend_EventManager_Event $event) {
                /** @var ManipleCore_Settings_SettingsManager $settingsManager */
                $settingsManager = $event->getTarget();

                $settingsManager->register(
                    ManipleUserConsents_ConsentsReview::TITLE_SETTING,
                    array(
                        'type'    => 'string',
                        'default' => ManipleUserConsents_ConsentsReview::TITLE_DEFAULT,
                    )
                );
                $settingsManager->register(
                    ManipleUserConsents_ConsentsReview::BODY_SETTING,
                    array(
                        'type'    => 'string',
                        'default' => ManipleUserConsents_ConsentsReview::BODY_DEFAULT,
                    )
                );
            }
        );
    }

    public function getMenuManagerConfig()
    {
        return array(
            'builders' => array(
                ManipleUserConsents_Menu_MenuBuilder::className,
            ),
        );
    }
}
