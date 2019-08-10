<?php

/**
 * @property Zend_Controller_Request_Http $_request
 */
class ManipleUserConsents_ConsentsController extends Maniple_Controller_Action
{
    const className = __CLASS__;

    /**
     * @Inject('user.sessionManager')
     * @var ManipleUser_Service_Security
     */
    protected $_securityContext;

    public function checkAccess()
    {
        if (!$this->_securityContext->isAuthenticated()) {
            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->json(array(
                    'status' => 'error',
                    'type' => 'auth_required',
                ));
            } else {
                $continue = $this->getScalarParam('continue', $this->_request->getRequestUri());
                $this->_helper->redirector->gotoUrlAndExit(
                    $this->view->url('user.auth.login') . '?continue=' . urlencode($continue)
                );
            }
            exit;
        }

        if (!$this->_securityContext->isAllowed('manage_consents')) {
            throw new Maniple_Controller_Exception_NotAllowed();
        }
    }
}
