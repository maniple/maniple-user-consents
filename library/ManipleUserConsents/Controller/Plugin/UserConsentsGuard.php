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

        // TODO: ignore urls from cache
        if ($this->_consentManager->userHasAllActiveConsents($this->_authContext->getUser()->getId())) {
            return;
        }

        $request->setModuleName('maniple-user-consents');
        $request->setControllerName('user-consents');
        $request->setActionName('update');
    }
}
