<?php

/**
 * @property Zend_Controller_Request_Http $_request
 */
class ManipleUserConsents_UserConsentsController_UpdateAction extends Maniple_Controller_Action_StandaloneForm
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

    protected function _prepare()
    {
        $this->_form = new Zefram_Form2();
        $this->_consentManager->onCreateSignupForm($this->_form);
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
