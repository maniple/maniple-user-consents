<?php

/**
 * @property Zend_Controller_Request_Http $_request
 * @method void checkAccess()
 */
class ManipleUserConsents_ConsentsController_CreateAction extends Maniple_Controller_Action_StandaloneForm
{
    const className = __CLASS__;

    protected $_actionControllerClass = ManipleUserConsents_ConsentsController::className;

    /**
     * @Inject
     * @var Zefram_Db
     */
    protected $_db;

    /**
     * @Inject
     * @var ManipleUserConsents_Model_Table_Consents
     */
    protected $_consentsTable;

    /**
     * @Inject
     * @var ManipleUserConsents_Model_Table_ConsentVersions
     */
    protected $_consentVersionsTable;

    protected function _init()
    {
        parent::_init();
        $this->checkAccess();

        $this->_form = new ManipleUserConsents_Form_Consent();
    }

    protected function _process()
    {
        $this->_db->beginTransaction();
        try {
            $consent = $this->_consentsTable->createRow(array(
                'is_required' => (int) $this->_form->getValue('is_required'),
                'is_active'   => (int) $this->_form->getValue('is_active'),
                'display_priority' => (int) $this->_form->getValue('display_priority'),
            ));
            $consent->save();

            $consentVersion = $this->_consentVersionsTable->createRow(array(
                'title' => $this->_form->getValue('title'),
                'body'  => $this->_form->getValue('body'),
            ));
            $consentVersion->Consent = $consent;
            $consentVersion->save();

            $consent->LatestVersion = $consentVersion;
            $consent->LatestMajorVersion = $consentVersion;
            $consent->save();

            $this->_db->commit();

        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

        return $this->view->url('maniple-user-consents.consents.index');
    }
}

