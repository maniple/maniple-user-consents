<?php

/**
 * @property Zend_Controller_Request_Http $_request
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

    public function run()
    {
        if (!$this->_userContext->isAuthenticated() || !$this->_userContext->isSuperUser()) {
            throw new Maniple_Controller_Exception_NotAllowed();
        }

        /** @var ManipleUserConsents_Model_Table_Consents $consentsTable */
        $consentsTable = $this->_db->getTable(ManipleUserConsents_Model_Table_Consents::className);
        $this->view->consents = $consentsTable->fetchAll(array(
            'deleted_at IS NULL',
        ), array('display_order ASC'));
    }
}
