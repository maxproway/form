<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require 'phpmailer/src/Exception.php';
	require 'phpmailer/src/PHPMailer.php';

    $mail = new PHPMailer(true);
	$mail->CharSet = 'UTF-8';
	$mail->IsHTML(true);

    $mail->setFrom('info@devmax.pro', 'devmax.pro');
    $mail->addAddress('info@devmax.pro');
    $mail->Subject = 'Devmax.pro Ticket Form';

    $importance = "Routine";
    if($_POST['importance'] == 'urgent') $hand = 'Urgent';

    //Email body
    $body = '<h1>New Ticket from Devmax.pro</h1>';

    if(trim(!empty($_POST['name']))){
        $body .= '<p><strong>Name:</strong> '.$_POST['name'].'</p>';
    }

    if(trim(!empty($_POST['email']))){
        $body .= '<p><strong>Email:</strong> '.$_POST['email'].'</p>';
    }

    if(trim(!empty($_POST['importance']))){
        $body .= '<p><strong>Importance:</strong> '.$_POST['importance'].'</p>';
    }

    if(trim(!empty($_POST['department']))){
        $body .= '<p><strong>Department:</strong> '.$_POST['department'].'</p>';
    }

    if(!empty($_FILES['image']['tmp_name'])){
        $filePath = __DIR__ / . '/files/' . $_FILES['image']['name']

        if(copy($_FILES['image']['tmp_name'], $filePath)){
            $fileAttach = $filePath;
             $body .= '<p><strong>Screenshot:</strong> ';
             $mail->addAttachment($fileAttach);
        }
    }

    $mail->Body = $body;

    if(!$mail->send()){
        $message = 'Error';
    } else {
        $message = 'Form has been sent';
    }

    $response = ['message' => $message];

    header('Content-type: application/json');
    echo json_encode($response);


    ?>