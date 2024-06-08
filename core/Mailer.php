<?php

namespace Booking;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    /** @var string */
    public const TEMPLATE_DIR = __DIR__.'/../app/Mailer/Template/';

    /**
     * @var PHPMailer
     */
    protected PHPMailer $mail;

    /**
     * @var array
     */
    protected array $conf;

    public function __construct()
    {
        $this->conf = config('mailer');
        $this->initialize();
    }

    protected function initialize(): void
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host = $this->conf['host'];
        $this->mail->SMTPAuth = false;
        $this->mail->Username = $this->conf['username'];
        $this->mail->Password = $this->conf['password'];
        $this->mail->Port = $this->conf['port'];
    }

    /**
     * Get mailer instance.
     *
     * @param string|null $fromEmail
     * @param string|null $fromName
     * @return PHPMailer
     * @throws Exception
     */
    public function getMail(string $fromEmail = null, string $fromName = null): PHPMailer
    {
        $this->mail->setFrom($fromEmail ?? $this->conf['email'], $fromName ?? $this->conf['name']);

        return $this->mail;
    }

    /**
     * Get template based on file template name and parameter data
     *
     * @param string $templateName
     * @param array $data
     * @return string
     */
    public function getTemplate(string $templateName, array $data = []): string
    {
        $template = self::TEMPLATE_DIR.$templateName.'.php';
        if (! file_exists($template)) return '';

        extract($data);
        ob_start();
        require $template;
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}
