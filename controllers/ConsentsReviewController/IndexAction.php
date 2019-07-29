<?php

/**
 * @property Zend_Controller_Request_Http $_request
 */
class ManipleUserConsents_ConsentsReviewController_IndexAction extends Maniple_Controller_Action_StandaloneForm
{
    const className = __CLASS__;

    protected $_actionControllerClass = ManipleUserConsents_ConsentsReviewController::className;

    /**
     * @Inject
     * @var ManipleCore_Settings_SettingsManager
     */
    protected $_settingsManager;

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

    /**
     * @Inject
     * @var Zefram_Db
     */
    protected $_db;

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

        if ($this->getParam('has_missing_consents') !== true) {
            $this->_helper->redirector->gotoUrlAndExit('/');
            exit;
        }

        $this->view->assign(array(
            'title' => (string) $this->_settingsManager->get(ManipleUserConsents_ConsentsReview::TITLE_SETTING),
            'body'  => (string) $this->_settingsManager->get(ManipleUserConsents_ConsentsReview::BODY_SETTING),
        ));
    }

    protected function _prepare()
    {
        $this->_form = $this->_consentManager->createConsentsUpdateForm($this->_userContext->getUser());
    }

    protected function _process()
    {
        $this->_db->beginTransaction();
        try {
            foreach ($this->_consentManager->getConsentDecisionsFromForm($this->_form) as $consentId => $decision) {
                $this->_consentManager->saveUserConsent($this->_userContext->getUser(), $consentId, $decision);
            }
            $this->_db->commit();

        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

        // TODO redirect to user home
        $this->_helper->redirector->gotoRoute('doko.workshops.my_meetings');
    }
}
