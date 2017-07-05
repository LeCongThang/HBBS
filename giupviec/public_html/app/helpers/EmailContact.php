<?php
/**
 * Created by PhpStorm.
 * User: Phan
 * Date: 12/29/2016
 * Time: 3:17 PM
 */

namespace app\helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailContact
{
    protected static $emailSend = 'info@proimage.vn';//

    /**
     * @var PHPMailer
     */
    protected $mailer;

    public $email;

    public $name;

    public $phone;

    public $content;


    public function __construct($data)
    {
        foreach ($data as $name => $val) {
            $this->{$name} = $val;
        }

        $this->mailer = new PHPMailer(true);
        //$this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->Username = 'mailer.proimage@gmail.com';
        $this->mailer->Password = 'yhejvnfznrlvfxzo';
        $this->mailer->Port = 587;
		$this->mailer->CharSet = "UTF-8";
        $this->mailer->SMTPSecure = 'tsl';
        $this->mailer->SMTPAuth = true;
        $this->mailer->addReplyTo($this->email, $this->name);
        $this->mailer->setFrom($this->email, 'Contact ProImage.vn');
        $this->mailer->addAddress(static::$emailSend);
        $this->mailer->Subject = "Contact from ProImage.vn";
        $this->mailer->isHTML(true);
        ob_start();
        include 'email.phtml';
        $this->mailer->Body = ob_get_clean();
    }

    public function send()
    {
        $result = $this->mailer->send();
        //var_dump($this->mailer->ErrorInfo);
        return $result;
    }
}