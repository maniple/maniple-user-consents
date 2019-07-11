<?php

class ManipleUserConsents_ConsentManager
{
    const className = __CLASS__;

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
     * @param int|ManipleUserConsents_Model_Consent $consent
     * @return ManipleUserConsents_Model_ConsentVersion
     */
    public function fetchConsentSnapshot($consent)
    {
        /** @var ManipleUserConsents_Model_Table_ConsentVersions $consentVersionsTable */
        $consentVersionsTable = $this->_db->getTable(ManipleUserConsents_Model_Table_ConsentVersions::className);

        if (!$consent instanceof ManipleUserConsents_Model_Consent) {
            $consentId = $consent;
            $consent = $this->_getConsentsTable()->find((int) $consent)->current();

            if (!$consent) {
                throw new Exception(sprintf('Invalid consent ID %s', $consentId));
            }
        }

        $data = array(
            'consent_id'  => (int) $consent->getId(),
            'title'       => trim($consent->title),
            'body'        => trim($consent->body),
            'body_type'   => $consent->body_type,
            'is_required' => (int) (bool) $consent->is_required,
        );
        $hash = sha1(Zefram_Json::encode($data, array(
            'unencodedSlashes' => true,
            'unencodedUnicode' => true,
        )));

        $matchingVersion = $consentVersionsTable->fetchRow(array('content_hash = ?' => $hash));

        if (!$matchingVersion) {
            $matchingVersion = $consentVersionsTable->createRow(array_merge(
                $data,
                array(
                    'created_at'   => time(),
                    'content_hash' => $hash,
                )
            ));
            $matchingVersion->save();
        }

        return $matchingVersion;
    }

    /**
     * @param Zend_Form $form
     * @throws Zend_Form_Exception
     * @internal
     */
    public function onCreateSignupForm(Zend_Form $form)
    {
        $consents = $this->_getConsentsTable()->fetchAll(array('is_active <> 0'), 'display_order ASC');

        foreach ($consents as $consent) {
            /** @var ManipleUserConsents_Model_Consent $consent */
            $form->addElement('Checkbox', 'consent_' . $consent->getId(), array(
                'required' => (bool) (int) $consent->is_required,
                'label' => $consent->body,
                //options? YES NO 0 1
                'validators' => array(
                    array('Identical', true, array(
                        'token' => '1',
                        'messages' => array(
                            Zend_Validate_Identical::NOT_SAME => 'Udzielenie zgody jest wymagane',
                        ),
                    )),
                ),
            ));
        }
    }

    /**
     * @param ManipleUser_Model_UserInterface $user
     * @param array $data
     * @internal
     */
    public function onCreateUser(ManipleUser_Model_UserInterface $user, array $data = array())
    {
        // TODO: Snapshot should be done when creating signup data - refactor this
        // TODO: once maniple-user signup process will not use registrations table
        foreach ($data as $key => $value) {
            if (preg_match('/^consent_(?P<consent_id>\d+)/', $key, $match)) {
                $snapshot = $this->fetchConsentSnapshot($match['consent_id']);
                $userConsent = $this->_db->getTable(ManipleUserConsents_Model_Table_UserConsents::className)->createRow(array(
                    'consent_version_id' => $snapshot->getId(),
                    'user_id' => $user->getId(),
                    'created_at' => time(),
                    'revoked_at' => $value ? null : time(),
                ));
                $userConsent->save();
            }
        }
    }

    /**
     * @return ManipleUserConsents_Model_Table_Consents
     */
    protected function _getConsentsTable()
    {
        /** @var ManipleUserConsents_Model_Table_Consents $consentsTable */
        $consentsTable = $this->_db->getTable(ManipleUserConsents_Model_Table_Consents::className);

        return $consentsTable;
    }
}
