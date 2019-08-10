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
    protected $_securityContext;

    /**
     * @Inject
     * @var Zefram_Db
     */
    protected $_db;

    protected function _init()
    {
        parent::_init();

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
        $this->_form = $this->_consentManager->createConsentsUpdateForm($this->_securityContext->getUser());
        $this->_form->addElement('hidden', 'continue_url', array(
            'value' => $this->getScalarParam('continue_url'),
        ));
    }

    protected function _process()
    {
        $this->_db->beginTransaction();
        try {
            foreach ($this->_consentManager->getConsentDecisionsFromForm($this->_form) as $consentId => $decision) {
                $this->_consentManager->saveUserConsent($this->_securityContext->getUser(), $consentId, $decision);
            }
            $this->_db->commit();

        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

        $continueUrl = $this->_form->getValue('continue_url');
        if (substr($continueUrl, 0, 1) !== '/') {
            $continueUrl = null;
        }

        if (!$continueUrl) {
            $config = $this->getResource('modules')->offsetGet('maniple-user')->getOptions();
            $continueUrl = isset($config['afterLoginRoute'])
                ? $this->view->url($config['afterLoginRoute'])
                : $this->view->baseUrl('/');
        }

        $this->_helper->redirector->gotoUrlAndExit($continueUrl);
    }
}
