<?php

/**
 * Class Mail
 * @author Marek Mazur | Colin Harper
 */
class Mail
{
    /**
     * @var null|string
     */
    private $to = null;

    /**
     * @var null|string
     */
    private $subject = 'Message';

    /**
     * @var null|string
     */
    private $message = null;

    /**
     * @var null|string
     */
    private $header = null;

    /**
     * @var null|string
     */
    private $body = null;

    /**
     * @var null|string
     */
    private $footer = null;

    /**
     * @var null|string
     */
    private $recipientName = null;

    /**
     * Mail construct class.
     */
    public function __construct()
    {
    }

    public function sendPlainEmail($to, $subject, $message)
    {
        if (!empty($to)) {
            $headers = 'From: no-replay@mt-hub.eu' . "\r\n" .
                'Reply-To: no-replay@mt-hub.eu' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $response = mail($to, $subject, $message, $headers);

            return $response;
        }

        return null;
    }

    /**
     * Send an HTML email.
     *
     * @param  string $to Email address
     * @param  string $subject Subject of email
     * @param  string $message Body of the email
     *
     * @return string|null
     */
    public function sendHtmlEmail($to, $subject, $message)
    {
        $header = '';//$this->getHeader();
        $footer = '';//$this->getFooter();

        $message = $header . $message . $footer;

        $headers = "From: no-replay@mt-hub.eu\r\n";
        $headers .= "Reply-To: no-replay@mt-hub.eu\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

        if (!empty($to)) {
            $response = mail($to, $subject, $message, $headers);

            return $response;
        }

        return null;
    }

    /**
     * @param $email
     * @return bool
     */
    public static function isValidEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    /**
     * Get email subject.
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set email subject.
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get email recipient.
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set email recipient.
     *
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * Get recipient name.
     */
    public function getRecipientName()
    {
        return $this->recipientName ? $this->recipientName : 'User';
    }

    /**
     * Set recipient name.
     *
     * @param string $recipientName
     */
    public function setRecipientName($recipientName)
    {
        $this->recipientName = $recipientName;
    }

    /**
     * Get the value of Message
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of Message
     *
     * @param mixed message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of Header
     *
     * @return mixed
     */
    public function getHeader()
    {
        $header = '';//$this->header ? $this->header : include FULL_PATH . 'Mail/header.php';

        return $header;
    }

    /**
     * Set the value of Header
     *
     * @param mixed header
     *
     * @return self
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get the value of Body
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of Body
     *
     * @param mixed body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = include $body;

        return $this;
    }

    /**
     * Get the value of Footer
     *
     * @return mixed
     */
    public function getFooter()
    {
        $footer = '';//$this->footer ? $this->footer : include FULL_PATH . 'Mail/footer.php';

        return $footer;
    }

    /**
     * Set the value of Footer
     *
     * @param mixed footer
     *
     * @return self
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;

        return $this;
    }
}
