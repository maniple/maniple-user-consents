<?php

class ManipleUserConsents_Controller_Plugin_UserConsentsGuard extends Zend_Controller_Plugin_Abstract
{
    const className = __CLASS__;

    const REDIRECT_ROUTE = 'maniple-user-consents.consents-review.index';

    /**
     * @Inject('user.sessionManager')
     * @var ManipleUser_Service_Security
     */
    protected $_securityContext;

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

        if (!$this->_securityContext->isAuthenticated() || $this->_securityContext->isAllowed('manage_consents')) {
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
        $missingConsents = $this->_consentManager->getMissingConsents($this->_securityContext->getUser()->getId());

        if (empty($missingConsents)) {
            return;
        }

        /** @var Zend_Controller_Router_Rewrite $router */
        $router = Zend_Controller_Front::getInstance()->getRouter();

        /** @var Zend_Controller_Router_Route_Module $route */
        $route = $router->getRoute(self::REDIRECT_ROUTE);
        $routeUrl = $router->assemble(array(), self::REDIRECT_ROUTE);

        $routeDefaults = $route->getDefaults();

        $request->setModuleName($routeDefaults['module']);
        $request->setControllerName($routeDefaults['controller']);
        $request->setActionName($routeDefaults['action']);

        $continueUrl = $requestUri === '/' || $routeUrl === strtok($requestUri, '?') ? null : $requestUri;

        $request->setParam('has_missing_consents', true);
        $request->setParam('continue_url', $continueUrl);
    }
}
