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
            'body_type'   => $consent->body_type,
            'is_required' => (int) (bool) $consent->is_required,
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
        $snapshot = $this->fetchConsentSnapshot($consentId);
        $userConsent = $this->_userConsentsTable->createRow(array(
            'consent_id'         => $snapshot->Consent->getId(),
            'consent_version_id' => $snapshot->getId(),
            'user_id'            => $user->getId(),
            'saved_at'           => time(),
            'decision'           => (int) $decision,
            'display_priority'   => $snapshot->Consent->display_priority,
        ));
        $userConsent->save();

        return $userConsent;
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

        // TODO This can be cached
        $activeConsents = $this->_consentsTable->fetchAll(array('is_active <> 0'));

        if (!count($activeConsents)) {
            return true;
        }

        // TODO this can be calculated at login - and stored in session
        $userConsents = $this->_userConsentsTable
            ->fetchAll(array(
                'user_id = ?' => $userId,
                'consent_version_id IN (?)' => $activeConsents->collectColumn('current_version_id')
            ));

        if (count($userConsents) < count($activeConsents)) {
            return false;
        }

        foreach ($userConsents as $userConsent) {
            /** @var ManipleUserConsents_Model_UserConsent $userConsent */
            if ($userConsent->isMissing()) {
                return false;
            }
        }

        return true;
    }
}
