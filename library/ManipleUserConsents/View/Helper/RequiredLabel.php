<?php

class ManipleUserConsents_View_Helper_RequiredLabel extends Zend_View_Helper_Abstract
{
    /**
     * @param string $label
     * @param string $hint OPTIONAL
     * @return string
     */
    public function requiredLabel($label, $hint = null)
    {
        if (!$hint) {
            $hint = 'This field is mandatory';
        }

        $required = sprintf('<span title="%s" style="cursor:pointer;">*<span class="sr-only">(Required)</span></span>', $this->view->translate($hint));

        if (preg_match('/^\s*<(p|div)(>|\s)/i', $label)) {
            $pos = strpos($label, '>');
            return substr($label, 0, $pos + 1) . $required . ' ' . substr($label, $pos + 1);
        }

        return $required . ' ' . $label;
    }
}
