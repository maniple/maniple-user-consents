<?php

/**
 * @property Zend_Controller_Request_Http $_request
 * @method void checkAccess()
 */
class ManipleUserConsents_ConsentsController_EditAction extends Maniple_Controller_Action_StandaloneForm
{
    const className = __CLASS__;

    protected $_actionControllerClass = ManipleUserConsents_ConsentsController::className;

    /**
     * @Inject
     * @var Zefram_Db
     */
    protected $_db;

    /**
     * @var ManipleUserConsents_Model_Consent
     */
    protected $_consent;

    protected function _init()
    {
        parent::_init();
        $this->checkAccess();

        /** @var ManipleUserConsents_Model_Table_Consents $consentsTable */
        $consentsTable = $this->_db->getTable(ManipleUserConsents_Model_Table_Consents::className);

        $consent = $consentsTable->findRow((int) $this->getScalarParam('consent_id'));
        if (!$consent) {
            throw new Maniple_Controller_NotFoundException();
        }

        $this->_consent = $consent;

        $this->_form = new ManipleUserConsents_Form_Consent($consent);
        $this->_form->addElement('checkbox', 'create_major_version', array(
            'label'       => 'Create major revision',
            'description' => 'This will force all existing users to review and update their consents after logging in',
            'type'        => 'checkbox',
        ));
    }

    protected function _process()
    {
        $data = $this->_form->getValues();

        $consent = $this->_consent;
        $this->_db->beginTransaction();

        try {
            /** @var ManipleUserConsents_Model_Table_UserConsents $userConsentsTable */
            $userConsentsTable = $this->_db->getTable(ManipleUserConsents_Model_Table_UserConsents::className);

            // detect changes first
            $latestVersion = $consent->LatestVersion;

            // Consent versions referenced by UserConsents are immutable
            $shouldCreateNewVersion =
                $userConsentsTable->countByConsentVersion($latestVersion)
                && ($data['title'] !== $latestVersion->title || $data['body'] !== $latestVersion->body);

            if ($shouldCreateNewVersion) {
                /** @var ManipleUserConsents_Model_Table_ConsentVersions $consentVersionsTable */
                $consentVersionsTable = $this->_db->getTable(ManipleUserConsents_Model_Table_ConsentVersions::className);
                $version = $consentVersionsTable->createRow();
                $version->Consent = $consent;
            } else {
                $version = $consent->LatestVersion;
            }

            $shouldCreateMajorVersion = $data['create_major_version'];
            if ($shouldCreateMajorVersion) {
                // create major version or promote existing version to major, if there are none user consents
                $version->major_version_id = null;
            } elseif ($version !== $latestVersion) {
                $prevVersion = $latestVersion;
                $version->major_version_id = $prevVersion->isMajorVersion()
                    ? $prevVersion->getId()
                    : $prevVersion->MajorVersion->getId();
            }

            $version->title = $data['title'];
            $version->body = $data['body'];
            $version->save();

            $consent->is_required = $data['is_required'] ? 1 : 0;
            $consent->is_active = $data['is_active'] ? 1 : 0;
            $consent->LatestVersion = $version;
            if ($version->isMajorVersion()) {
                $consent->LatestMajorVersion = $version;
            }
            $consent->save();

            $this->_db->commit();

        } catch (Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }

        return $this->view->url('maniple-user-consents.consents.index');
    }
}

