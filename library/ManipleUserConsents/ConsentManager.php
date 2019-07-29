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
     * @Inject('user.model.userMapper')
     * @var ManipleUser_Model_UserMapper
     */
    protected $_userRepository;

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

    /**
     * @Inject
     * @var ManipleUserConsents_Model_Table_UserConsents
     */
    protected $_userConsentsTable;

    /**
     * @param int|ManipleUserConsents_Model_Consent $consent
     * @return ManipleUserConsents_Model_Consent
     * @throws Exception
     */
    public function fetchConsent($consent)
    {
        if (!$consent instanceof ManipleUserConsents_Model_Consent) {
            $consentId = $consent;
            $consent = $this->_consentsTable->find((int) $consent)->current();

            if (!$consent) {
                throw new Exception(sprintf('Invalid consent ID %s', $consentId));
            }
        }

        return $consent;
    }

    /**
     * @param int|ManipleUserConsents_Model_Consent $consent
     * @return ManipleUserConsents_Model_ConsentVersion
     */
    public function fetchConsentSnapshot($consent)
    {
        if (!$consent instanceof ManipleUserConsents_Model_Consent) {
            $consentId = $consent;
            $consent = $this->_consentsTable->find((int) $consent)->current();

            if (!$consent) {
                throw new Exception(sprintf('Invalid consent ID %s', $consentId));
            }
        }

        $data = array(
            'consent_id'  => (int) $consent->getId(),
            'title'       => $this->_normalize($consent->title),
            'body'        => $this->_normalize($consent->body),
        );
        $hash = sha1(Zefram_Json::encode($data, array(
            'unencodedSlashes' => true,
            'unencodedUnicode' => true,
        )));

        $matchingVersion = $this->_consentVersionsTable->fetchRow(array('content_hash = ?' => $hash));

        if (!$matchingVersion) {
            $matchingVersion = $this->_consentVersionsTable->createRow(array_merge(
                $data,
                array(
                    'created_at'   => time(),
                    'content_hash' => $hash,
                )
            ));
            $matchingVersion->save();
        }

        // Ensure consent row is attached, to avoid re-fetching it from db
        $matchingVersion->Consent = $consent;

        return $matchingVersion;
    }

    protected function _normalize($str)
    {
        $str = trim($str);
        $str = preg_replace('/\s+/', ' ', $str);
        return $str;
    }

    /**
     * @return ManipleUserConsents_Model_Consent[]
     */
    public function getActiveConsents()
    {
        $consentsRowset = $this->_consentsTable->fetchAll(array(
            'is_active <> 0',
            'deleted_at IS NULL',
        ), 'display_priority DESC');

        $consents = array();
        foreach ($consentsRowset as $row) {
            /** @var ManipleUserConsents_Model_Consent $row */
            $consents[$row->getId()] = $row;
        }

        return $consents;
    }

    /**
     * Get consents user is missing responses to
     *
     * Users must provide decisions for all active consents, matched by majorVersionId,
     * otherwise consent is considered missing.
     *
     * @param $user
     * @return ManipleUserConsents_Model_Consent[]
     */
    public function getMissingConsents($user)
    {
        if ($user instanceof ManipleUser_Model_UserInterface) {
            $userId = (int) $user->getId();
        } else {
            $userId = (int) $user;
        }

        $activeConsents = array();

        foreach ($this->getActiveConsents() as $consent) {
            $latestMajorVersionId = $consent->getLatestMajorVersionId();
            if ($latestMajorVersionId) {
                $activeConsents[$latestMajorVersionId] = $consent;
            }
        }

        if (!count($activeConsents)) {
            return array();
        }

        $select = $this->_db->select();
        $select->from(
            array('uc' => $this->_userConsentsTable->getName()),
            $this->_userConsentsTable->getColsForSelect('uc__')
        );
        $select->join(
            array('cv' => $this->_consentVersionsTable->getName()),
            'cv.consent_version_id = uc.consent_version_id',
            $this->_consentVersionsTable->getColsForSelect('cv__')
        );
        $select->where(array(
            'uc.user_id = ?' => $userId,
            'cv.major_version_id IN (?)' => array_keys($activeConsents),
        ));

        /** @var ManipleUserConsents_Model_UserConsent[] $userConsents */
        $userConsents = array();

        foreach ($select->query()->fetchAll(Zend_Db::FETCH_ASSOC) as $row) {
            $datas = array();
            foreach ($row as $col => $value) {
                list($prefix, $col) = explode('__', $col, 2);
                $datas[$prefix][$col] = $value;
            }
            /** @var ManipleUserConsents_Model_UserConsent $row */
            $row = $this->_userConsentsTable->_createStoredRow($datas['uc']);
            $row->ConsentVersion = $this->_consentVersionsTable->_createStoredRow($datas['cv']);

            $userConsents[] = $row;
        }

        foreach ($userConsents as $userConsent) {
            $majorVersionId = $userConsent->ConsentVersion->getMajorVersionId();

            if (empty($activeConsents[$majorVersionId])) {
                // consent handled in earlier iterations
                continue;
            }

            if ($userConsent->isAccepted() || !$activeConsents[$majorVersionId]->isRequired()) {
                unset($activeConsents[$majorVersionId]);
                continue;
            }
        }

        return $activeConsents;
    }

    /**
     * @param Zend_Form $form
     * @throws Zend_Form_Exception
     * @internal
     */
    public function onCreateSignupForm(Zend_Form $form)
    {
        foreach ($this->getActiveConsents() as $consent) {
            /** @var ManipleUserConsents_Model_Consent $consent */
            $this->_addConsentCheckbox($form, $consent);
        }
    }

    public function createConsentsUpdateForm($user)
    {
        $form = new Zefram_Form2();

        foreach ($this->getMissingConsents($user) as $consent) {
            /** @var ManipleUserConsents_Model_Consent $consent */
            $this->_addConsentCheckbox($form, $consent);
        }

        return $form;
    }

    protected function _addConsentCheckbox(Zend_Form $form, ManipleUserConsents_Model_Consent $consent)
    {
        $validators = array();

        if ($consent->isRequired()) {
            $validators[] =  array('Identical', true, array(
                'token' => '1',
                'messages' => array(
                    Zend_Validate_Identical::NOT_SAME => 'Accepting this consent is mandatory',
                    // 'Udzielenie zgody jest wymagane',
                ),
            ));
        }

        $form->addElement('Checkbox', 'consent_' . $consent->getId(), array(
            'required'   => $consent->isRequired(),
            'label'      => $consent->getBody(),
            'validators' => $validators,
        ));
    }

    /**
     * @param ManipleUser_Model_UserInterface $user
     * @param array $data
     * @internal
     */
    public function onCreateUser(ManipleUser_Model_UserInterface $user, array $data = array())
    {
        // TODO: Snapshot should be done when creating signup data - not when activating user
        foreach ($data as $key => $value) {
            if (preg_match('/^consent_(?P<consent_id>\d+)/', $key, $match)) {
                $this->saveUserConsent($user, $match['consent_id'], $value);
            }
        }
    }

    public function getConsentDecisionsFromForm(Zend_Form $form)
    {
        $consents = array();
        foreach ($form->getElements() as $key => $element) {
            /** @var Zend_Form_Element $element */
            if (preg_match('/^consent_(?P<consent_id>\d+)/', $key, $match)) {
                $consents[$match['consent_id']] = $element->getValue();
            }
        }
        return $consents;
    }

    /**
     * @param ManipleUser_Model_UserInterface $user
     * @param ManipleUserConsents_Model_Consent|int $consentId
     * @param bool $decision
     * @return ManipleUserConsents_Model_UserConsent
     * @throws Exception
     */
    public function saveUserConsent(ManipleUser_Model_UserInterface $user, $consentId, $decision)
    {
        $consent = $this->fetchConsent($consentId);
        $userConsent = $this->_userConsentsTable->fetchRow(array(
            'user_id = ?'            => (int) $user->getId(),
            'consent_version_id = ?' => $consent->LatestVersion->getId(),
        ));

        if (!$userConsent) {
            $userConsent = $this->_userConsentsTable->createRow(array(
                'consent_version_id' => $consent->LatestVersion->getId(),
                'user_id'            => $user->getId(),
            ));
        }

        $userConsent->setFromArray(array(
            'is_required'      => $consent->isRequired() ? 1 : 0,
            'display_priority' => $consent->getDisplayPriority(),
            'expires_at'       => date_create()->modify('+1 year')->getTimestamp(),
        ));

        if ($decision) {
            $userConsent->setAccepted();
        } elseif ($userConsent->isAccepted()) {
            $userConsent->setRevoked();
        } else {
            $userConsent->setDeclined();
        }

        $userConsent->save();

        $this->_db->getTable(ManipleUserConsents_Model_Table_UserConsentStates::className)->insert(array(
            'user_consent_id' => $userConsent->getId(),
            'state'           => $userConsent->getState(),
            'saved_at'        => $userConsent->getSavedAt(),
            'is_required'     => (int) $userConsent->isRequired(),
        ));

        return $userConsent;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function getConsentDecision($name)
    {
        if (!$this->_userContext->isAuthenticated()) {
            return false;
        }
        $userId = (int) $this->_userContext->getUser()->getId();
        $userConsent = $this->_userConsentsTable->fetchRow(array(
            'user_id = ?' => $userId,
            'user_setting = ?' => (string) $name,
        ));
        return $userConsent && $userConsent->isAccepted();
    }
}
