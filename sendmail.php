<?php
	use PHPMailer\PHPMailer\PHPMailer;
	//use PHPMailer\PHPMailer\Exception; //uncomment if use exception
    //use PHPMailer\PHPMailer\SMTP; //uncomment if use SMTP

    require 'phpmailer/src/PHPMailer.php';
	//require 'phpmailer/src/Exception.php'; //uncomment if use exception
    //require 'phpmailer/src/SMTP.php'; //uncomment if use SMTP

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $SMTP = false; //set to true for using SMTP

    $project_name = trim($_POST["project_name"]);
    $admin_email  = trim($_POST["admin_email"]);
    $recipient_email = $admin_email; //to prevent spam use sender and recipient emails the same, better send from the same domain as the email address
    $form_subject = trim($_POST["form_subject"]);
    $email = trim($_POST["email"]);

    $mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
	$mail->IsHTML(true);

    //Recipients
    $mail->setFrom($admin_email, $project_name);
    $mail->addAddress($recipient_email);
    $mail->addReplyTo($email, $name);
    $mail->Subject = $form_subject;

    if($SMTP){ //use SMTP Credentials if SMTP choosen ($SMTP set to true above)
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;       //Enable verbose debug output
        $mail->isSMTP();                                           //Send using SMTP
        $mail->Host       = 'smtp.server.com';         //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                      //Enable SMTP authentication
        $mail->Username   = 'address@mail.com';                     //SMTP username
        $mail->Password   = '****';                        //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  //Enable implicit TLS encryption
        $mail->Port       = 465; //use465 for ssl, and 587 for tls
    }

    // $importance = "Routine";
    // if($_POST['importance'] == 'urgent') $importance = 'Urgent';

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

    if(trim(!empty($_POST['message']))){
        $body .= '<p><strong>Message:</strong> '.$_POST['message'].'</p>';
    }

    // attaching file
    if(!empty($_FILES['image']['tmp_name'])){
        if (array_key_exists('image', $_FILES)) {
            //First handle the upload
            //Don't trust provided filename - same goes for MIME types
            //See http://php.net/manual/en/features.file-upload.php#114004 for more thorough upload validation

            //Extract an extension from the provided filename
            $ext = PHPMailer::mb_pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            //Define a safe location to move the uploaded file to, preserving the extension
            $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['image']['name'])) . '.' . $ext;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {
                //Upload handled successfully

                //Attach the uploaded file
                if (!$mail->addAttachment($uploadfile, 'My uploaded file')) {
                    $msg = 'Failed to attach file ' . $_FILES['image']['name'];
                    //did not figure out what to do here next. Probably do not need to email message and use exit() to stop script and sent response about failed file attaching.
                } else{
                    $body .= '<p><strong>Screenshot:</strong></p>';
                }
            } else{
                $msg = 'Failed to move file to ' . $uploadfile;
                //did not figure out what to do here next. Probably do not need to email message and use exit() to stop script and sent response about failed file uploading
            }
        }
    }

    // end of attaching file

    $mail->Body = $body;
    // $mail->Body = $message;

    if(!$mail->send()){
        if ($isAjax) {
            http_response_code(500);
        }
        $response = [
            "status" => false,
            "message" => 'Sorry, something went wrong. Please try again later.'
        ];
    } else {
        $response = [
            "status" => true,
            "message" => 'Message sent! Thanks for contacting me.'
        ];
    }

    if ($isAjax) {
        header('Content-type: application/json');
        echo json_encode($response);
        exit();
    }

?>