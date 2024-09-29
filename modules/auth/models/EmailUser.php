<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
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
