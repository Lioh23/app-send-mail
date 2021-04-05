<?php

require "./vendor/phpmailer/phpmailer/src/Exception.php";
require "./vendor/phpmailer/phpmailer/src/OAuth.php";
require "./vendor/phpmailer/phpmailer/src/PHPMailer.php";
require "./vendor/phpmailer/phpmailer/src/POP3.php";
require "./vendor/phpmailer/phpmailer/src/SMTP.php";

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mensagem 
{
    private $para = null;
    private $assunto = null;
    private $mensagem = null;
    public $status = [
        'codigo_status' => null, 
        'descricao_status' => ''
    ]; 

    public function __get($atributo) 
    {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) 
    {
        $this->$atributo = $valor;
    }

    public function mensagemValida() 
    {
        if(empty($this->para) || empty($this->assunto) || empty($this->mensagem)) {
            return false;
        }

        return true;
    }
}

$mensagem = new Mensagem();

foreach($_POST as $campo => $valor) {
    $mensagem->__set($campo, $valor);
}

if(!$mensagem->mensagemValida()) {
    header('Location: index.php');
}

$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->SMTPDebug = 0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'seuemail@gmail.com';                     //SMTP username
    $mail->Password   = 'suasenha';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('seuemail@gmail.com', 'Testando envio de mensagem');
    $mail->addAddress($mensagem->para);     //Add a recipient
    // $mail->addReplyTo('aurelin95@gmail.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $mensagem->assunto;
    $mail->Body    = $mensagem->mensagem;
    $mail->AltBody = $mensagem->mensagem;

    $mail->send();

    $mensagem->status['codigo_status'] = 1;
    $mensagem->status['descricao_status'] = 'E-mail enviado com sucesso!';

} catch (Exception $e) {

    $mensagem->status['codigo_status'] = 2;
    $mensagem->status['descricao_status'] ='Não foi possível enviar este e-mail, por favor tente novamente!';

} ?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title><?= $mensagem->status['codigo_status'] == 1 ? 'Sucesso!' : 'Erro no envio' ?></title>
</head>
<body>
    <div class="container">
        <div class="py-3 text-center">
            <img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
            <h2>Send Mail</h2>
            <p class="lead">Seu app de envio de e-mails particular!</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php 
                $status = [];
                if($mensagem->status['codigo_status'] == 1)  {
                    
                    $status['class'] = 'text-success';
                    $status['titulo'] = 'Sucesso!';
                    $status['descricao'] = $mensagem->status['descricao_status'];
                   
                } else {

                    $status['class'] = 'text-danger';
                    $status['titulo'] = 'Ops!';
                    $status['descricao'] = $mensagem->status['descricao_status'];
                } ?>
                <div class="containter">
                    <h1 class="display-4 <?= $status['class'] ?>"><?= $status['titulo'] ?></h1>
                    <p><?= $status['descricao'] ?></p>
                    <a class="btn btn-success btn-lg mt-5 text-white" href="">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
