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

        // TODO: No need to enforce consents if user has Consents Manager permission
        if (!$this->_authContext->isAuthenticated() /* || $this->_authContext->isSuperUser() */) {
            return;
        }

        if (($request->getModuleName() === 'maniple-user' && $request->getControllerName() === 'auth') ||
            ($request->getControllerName() === 'error')
        ) {
            return;
        }

        // Allow request URIs present in hrefs in latest versions of active consents
        $requestUri = $request->getRequestUri();

        // TODO: 1) Cache these results
        $consents = $this->_consentManager->getActiveConsents(true);
        foreach ($consents as $consent) {
            $body = ManipleUserConsents_Filter_RelativizeHrefs::filterStatic($consent->getBody());
            preg_match_all('/href="([^"]+)"/i', $body, $matches);

            foreach ($matches[1] as $url) {
                if ($url === $requestUri) {
                    return;
                }
            }
        }

        // TODO: 2) Check MAX(updated_at) of consent versions
        // TODO: 3) Store (bool) getMissingConsents() in user session with date when it was
        //       computed, if earlier than MAX(updated_at) then need to recompute
        $missingConsents = $this->_consentManager->getMissingConsents($this->_authContext->getUser()->getId());

        if (empty($missingConsents)) {
            return;
        }

        $request->setModuleName('maniple-user-consents');
        $request->setControllerName('consents-review');
        $request->setActionName('index');
        $request->setParam('has_missing_consents', true);
    }
}
