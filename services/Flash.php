<?php
/**
 * Created by PhpStorm.
 * User: Artur
 * Date: 20.05.2017
 * Time: 17:25
 */

class Flash extends \Phalcon\Flash\Direct
{
    const DEFAULT_TEMPLATE = '<button type=\"button\" class=\"close\" data-dismiss=\"alert\">
<span aria-hidden=\"true\">&times;</span></button>%s';
    protected $_messageInnerTemplate = '%s';

    /**
     * Outputs a message
     *
     * @param mixed $type
     * @param string $message
     */
    public function message(string $type, $message): ?string
    {
        return parent::message($type, sprintf($this->_messageInnerTemplate, $message));
    }

    /**
     * @param string $messageInnerTemplate
     */
    public function setMessageInnerTemplate($messageInnerTemplate)
    {
        $this->_messageInnerTemplate = $messageInnerTemplate;
    }
}
