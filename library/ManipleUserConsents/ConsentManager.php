<?php

class ManipleUserConsents_ConsentManager
{
    const className = __CLASS__;

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
     * @param Zend_Form $form
     * @throws Zend_Form_Exception
     * @internal
     */
    public function onCreateSignupForm(Zend_Form $form)
    {
        $consents = $this->_consentsTable->fetchAll(array('is_active <> 0'), 'display_priority DESC');

        foreach ($consents as $consent) {
            /** @var ManipleUserConsents_Model_Consent $consent */
            $form->addElement('Checkbox', 'consent_' . $consent->getId(), array(
                'required' => (bool) (int) $consent->isRequired(),
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
        $userConsent = $this->_userConsentsTable->createRow(array(
            'consent_id'         => $consent->getId(),
            'consent_version_id' => $consent->LatestVersion->getId(),
            'major_version_id'   => $consent->LatestVersion->getMajorVersionId(),
            'user_id'            => $user->getId(),
            'saved_at'           => time(),
            'is_required'        => $consent->isRequired() ? 1 : 0,
            'decision'           => $decision ? 1 : 0,
            'display_priority'   => $consent->display_priority,
        ));
        $userConsent->save();

        return $userConsent;
    }

    public function updateConsent(ManipleUserConsents_Model_Consent $consent)
    {

    }

    /**
     * @param ManipleUser_Model_UserInterface|int $user
     * @return bool
     */
    public function userHasAllActiveConsents($user)
    {
        if ($user instanceof ManipleUser_Model_UserInterface) {
            $userId = (int) $user->getId();
        } else {
            $userId = (int) $user;
        }

        $activeConsentsRowset = $this->_consentsTable->fetchAll(array(
            'deleted_at = 0',
            'is_active <> 0',
        ));

        if (!count($activeConsentsRowset)) {
            return true;
        }

        /** @var ManipleUserConsents_Model_Consent[] $activeConsents */
        $activeConsents = array();
        foreach ($activeConsentsRowset as $row) {
            if ($row->major_version_id) {
                $activeConsents[$row->major_version_id] = $row;
            }
        }

        $userConsentsRowset = $this->_userConsentsTable->fetchAll(array(
            'user_id = ?' => $userId,
            'revoked_at IS NULL',
        ));

        /** @var ManipleUserConsents_Model_UserConsent[] $userConsents */
        $userConsents = array();
        foreach ($userConsentsRowset as $userConsent) {
            /** @var ManipleUserConsents_Model_UserConsent $userConsent */
            $majorVersionId = $userConsent->ConsentVersion->getMajorVersionId();

            if (isset($activeConsents[$majorVersionId])) {
                // either no consent was detected previously, or consent explicitly given
                if (empty($userConsents[$majorVersionId]) || $userConsent->isGranted()) {
                    $userConsents[$majorVersionId] = $userConsent;
                }
            }
        }

        foreach ($activeConsents as $majorVersionId => $consent) {
            if (empty($userConsents[$majorVersionId])) {
                return false;
            }
            if ($consent->isRequired() && !$userConsents[$majorVersionId]->isGranted()) {
                return false;
            }
        }

        return true;
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
        return $userConsent && $userConsent->isGranted();
    }
}
