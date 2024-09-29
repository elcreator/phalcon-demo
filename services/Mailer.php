<?php
/**
 * @author Artur Kyryliuk <mail@artur.work>
 */

class Mailer
{
    /**
     * @param string $subject
     * @param string $message
     * @param string $to
     * @param string $cc
     * @return bool
     */
    public function send(string $subject, string $message, string $to, string $cc = '')
    {
        $headers  = ['MIME-Version: 1.0', 'Content-type: text/html; charset=iso-8859-1'];
        if (!empty($cc)) {
            $headers[] = "Cc: $cc";
        }
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
}
