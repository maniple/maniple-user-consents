<?php

/**
 * @property Zend_Controller_Request_Http $_request
 * @method void checkAccess()
 */
class ManipleUserConsents_ConsentsReviewController_EditAction extends Maniple_Controller_Action_StandaloneForm
{
    const className = __CLASS__;

    protected $_actionControllerClass = ManipleUserConsents_ConsentsReviewController::className;

    /**
     * @Inject
     * @var ManipleCore_Settings_SettingsManager
     */
    protected $_settingsManager;

    /**
     * @Inject('user.sessionManager')
     * @var ManipleUser_Service_Security
     */
    protected $_userContext;

    protected function _init()
    {
        parent::_init();
        $this->checkAccess();
    }

    protected function _prepare()
    {
        $this->_form = new ManipleUserConsents_Form_ConsentsReview($this->_settingsManager);
    }

    protected function _process()
    {
        $this->_settingsManager->set(
            ManipleUserConsents_ConsentsReview::TITLE_SETTING,
            $this->_form->getValue('title')
        );
        $this->_settingsManager->set(
            ManipleUserConsents_ConsentsReview::BODY_SETTING,
            $this->_form->getValue('body')
        );

        $this->_helper->redirector->gotoRoute('maniple-user-consents.consents.index');
    }
}
