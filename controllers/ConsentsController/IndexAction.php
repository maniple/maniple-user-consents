<?php

/**
 * @property Zend_Controller_Request_Http $_request
 * @method void checkAccess()
 */
class ManipleUserConsents_ConsentsController_IndexAction extends Maniple_Controller_Action_Standalone
{
    const className = __CLASS__;

    protected $_actionControllerClass = ManipleUserConsents_ConsentsController::className;

    /**
     * @Inject
     * @var Zefram_Db
     */
    protected $_db;

    /**
     * @Inject('user.sessionManager')
     * @var ManipleUser_Service_Security
     */
    protected $_userContext;

    /**
     * @Inject
     * @var ManipleCore_Settings_SettingsManager
     */
    protected $_settingsManager;

    public function run()
    {
        $this->checkAccess();

        /** @var ManipleUserConsents_Model_Table_Consents $consentsTable */
        $consentsTable = $this->_db->getTable(ManipleUserConsents_Model_Table_Consents::className);
        $this->view->consents = $consentsTable->fetchAll(array(
            'deleted_at IS NULL',
        ), array('display_priority DESC'));

        $this->view->assign(array(
            'consentsReviewTitle' => (string) $this->_settingsManager->get(ManipleUserConsents_ConsentsReview::TITLE_SETTING),
            'consentsReviewBody'  => (string) $this->_settingsManager->get(ManipleUserConsents_ConsentsReview::BODY_SETTING),
        ));
    }
}
