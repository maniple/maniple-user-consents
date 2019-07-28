<?php

class ManipleUserConsents_Controller_Plugin_UserConsentsGuard extends Zend_Controller_Plugin_Abstract
{
    const className = __CLASS__;

    /**
     * @Inject('user.sessionManager')
     * @var ManipleUser_Service_Security
     */
    protected $_authContext;

    /**
     * @Inject
     * @var ManipleUserConsents_ConsentManager
     */
    protected $_consentManager;

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!$request instanceof Zend_Controller_Request_Http) {
            return;
        }

        if (!$this->_authContext->isAuthenticated() /* || $this->_authContext->isSuperUser() */) {
            return;
        }

        // TODO: No need to enforce consents if user has Consents Manager permission

        if (($request->getModuleName() === 'maniple-user' && $request->getControllerName() === 'auth') ||
            ($request->getControllerName() === 'error')
        ) {
            return;
        }

        // To nie moze byc w sesji, bo po zmianach ktore zrobi admin nie ma szans na odswiezenie
        // - to musi byc w cache'u?
        $missingConsents = $this->_consentManager->getMissingConsents($this->_authContext->getUser()->getId());

        if (empty($missingConsents)) {
            return;
        }

        $request->setModuleName('maniple-user-consents');
        $request->setControllerName('user-consents');
        $request->setActionName('update-required');
        $request->setParam('hasMissingConsents', true);
    }
}
