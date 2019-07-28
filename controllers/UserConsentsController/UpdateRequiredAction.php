<?php

/**
 * @property Zend_Controller_Request_Http $_request
 */
class ManipleUserConsents_UserConsentsController_UpdateRequiredAction extends Maniple_Controller_Action_StandaloneForm
{
    const className = __CLASS__;

    protected $_actionControllerClass = ManipleUserConsents_UserConsentsController::className;

    /**
     * @Inject('ManipleUser.UserSettings')
     * @var ManipleUser_UserSettings_Service
     */
    protected $_userSettings;

    /**
     * @Inject
     * @var ManipleUserConsents_ConsentManager
     */
    protected $_consentManager;

    /**
     * @Inject('user.sessionManager')
     * @var ManipleUser_Service_Security
     */
    protected $_userContext;

    protected function _init()
    {
        parent::_init();

        if (!$this->_userContext->isAuthenticated()) {
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

        if ($this->getParam('hasMissingConsents') !== true) {
            $this->_helper->redirector->gotoUrlAndExit('/');
            exit;
        }
    }

    protected function _prepare()
    {
        $this->_form = $this->_consentManager->createConsentsUpdateForm($this->_userContext->getUser());
    }

    protected function _process()
    {
        foreach ($this->_consentManager->getConsentDecisionsFromForm($this->_form) as $consentId => $decision) {
            $this->_consentManager->saveUserConsent($this->_userContext->getUser(), $consentId, $decision);
        }

        // TODO redirect to user home
        $this->_helper->redirector->gotoRoute('doko.workshops.my_meetings');
    }
}
