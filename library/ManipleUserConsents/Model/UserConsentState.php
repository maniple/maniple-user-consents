<?php

/**
 * @property ManipleUserConsents_Model_UserConsent $UserConsent
 * @method ManipleUserConsents_Model_Table_UserConsentStates getTable()
 */
class ManipleUserConsents_Model_UserConsentState extends Zefram_Db_Table_Row
{
    const className = __CLASS__;

    protected $_tableClass = ManipleUserConsents_Model_Table_UserConsentStates::className;
}
