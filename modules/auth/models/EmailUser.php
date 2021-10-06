<?php
/**
 * @file    User.php
 * @brief
 * @author  Artur Kirilyuk (artur.kirilyuk@gmail.com)
 * @package Auth\models
 */

namespace Auth\Models;

class EmailUser extends User
{
    public function initialize()
    {
        $this->setSource('user');
        $this->skipAttributesOnCreate(['createdAt']);
    }
}
