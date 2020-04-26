<?php

class ManipleUserConsents_Menu_MenuBuilder implements Maniple_Menu_MenuBuilderInterface
{
    const className = __CLASS__;

    /**
     * @Inject
     * @var ManipleUser_Service_Security
     */
    protected $_securityContext;

    /**
     * @param Maniple_Menu_Menu $menu
     */
    public function buildMenu(Maniple_Menu_Menu $menu)
    {
        if ($menu->getName() === 'maniple.primary') {
            return $this->_buildPrimaryMenu($menu);
        }
    }

    protected function _buildPrimaryMenu(Maniple_Menu_Menu $menu)
    {
        if (!$this->_securityContext->isAllowed(ManipleUserConsents_Perm::MANAGE_CONSENTS)) {
            return;
        }

        $menu->addPage(array(
            'label' => 'Consents management',
            'route' => 'maniple-user-consents.consents.index',
            'type'  => 'mvc',
        ));
    }
}
