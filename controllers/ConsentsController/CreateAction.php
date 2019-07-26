<?php

/**
 * @property Zend_Controller_Request_Http $_request
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

        $this->_form = new Zefram_Form2(array(
            'elements' => array(
                'title' => array(
                    'type' => 'text',
                ),
                'body' => array(
                    'type' => 'textarea',
                ),
                'is_required' => array(
                    'type' => 'checkbox',
                    'options' => array(
                        'label' => 'Is active?',
                    ),
                ),
                'is_active' => array(
                    'type' => 'checkbox',
                    'options' => array(
                        'label' => 'Is active?',
                    ),
                ),
                'system_key' => array(
                    'type' => 'text',
                    'options' => array(
                        'required' => true,
                        'label' => 'System key',
                        // TODO: check uniqueness in consentsTable
                    ),
                ),
                '__submit' => array(
                    'type' => 'submit',
                ),
            ),
        ));
    }

    protected function _process()
    {
        $this->_db->beginTransaction();
        try {
            $consent = $this->_consentsTable->createRow(array(
                'is_required' => (int) $this->_form->getValue('is_required'),
                'is_active'   => (int) $this->_form->getValue('is_active'),
                'created_at'  => time(),
                'system_key'  => $this->_form->getValue('system_key'),
            ));
            $consent->save();

            $consentVersion = $this->_consentVersionsTable->createRow(array(
                'created_at' => time(),
                'title'      => $this->_form->getValue('title'),
                'body'       => $this->_form->getValue('body'),
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

