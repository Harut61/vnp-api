<?php
/**
 * Created by PhpStorm.
 * User: ngpatel
 * Date: 28/9/20
 * Time: 8:23 PM
 */

namespace App\Services;


use Twig\Environment;

class EmailService
{
    /** @var Environment  $twigEnv */
    protected $twigEnv;
    /** @var  \Swift_Mailer */
    protected $mailer;

    public function __construct(Environment $twigEnv, \Swift_Mailer $swift_Mailer)
    {
        $this->twigEnv = $twigEnv;
        $this->mailer = $swift_Mailer;
    }

    /**
     * @param $template
     * @param $toEmails
     * @param array $params
     * @param $subject
     * @param string $contentType
     * @return bool|int
     */
    public function sendEmail($template, $toEmails,  $params = [], $subject,$contentType = 'text/html')
    {
        if(getenv("APP_ENV") === "test") return true;

        $message = (new \Swift_Message($subject))
            ->setFrom(getenv("FROM_EMAIL"))
            ->setTo($toEmails);

        $message->setBody($this->twigEnv->render( $template, $params), $contentType);

        return $this->mailer->send($message);
    }

}