<?php
//发送邮箱验证
//发送邮件
        $email=$request["email"];//获取收件人邮箱
        //return $email;
        $sendmail = '2199374997@qq.com'; //发件人邮箱
        $sendmailpswd = "fpudncsirvtldhji"; //客户端授权密码,而不是邮箱的登录密码，就是手机发送短信之后弹出来的一长串的密码
        $send_name = 'lh';// 设置发件人信息，如邮件格式说明中的发件人，
        $toemail = $email;//定义收件人的邮箱
        $to_name = 'hl';//设置收件人信息，如邮件格式说明中的收件人
        $mail = new \PHPMailer\PHPMailer();
        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host = "smtp.qq.com";// 发送方的SMTP服务器地址
        $mail->SMTPAuth = true;// 是否使用身份验证
        $mail->Username = $sendmail;//// 发送方的
        $mail->Password = $sendmailpswd;//客户端授权密码,而不是邮箱的登录密码！
        $mail->SMTPSecure = "ssl";// 使用ssl协议方式
        $mail->Port = 465;//  qq端口465或587）
        $mail->setFrom($sendmail, $send_name);// 设置发件人信息，如邮件格式说明中的发件人，
        $mail->addAddress($toemail, $to_name);// 设置收件人信息，如邮件格式说明中的收件人，
        $mail->addReplyTo($sendmail, $send_name);// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
        $mail->Subject = "这里是邮件标题";// 邮件标题

        $code=rand(100000,999999);
        Session::set("code",$code);  //把产生的验证码存在Session，用于前台验证
        //return $code."----".session("code");
        $mail->Body = "邮件内容是 <b>您的验证码是：$code</b>，如果非本人操作无需理会！";// 邮件正文
        //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用
        if (!$mail->send()) { // 发送邮件
                     echo "Message could not be sent.";
            echo "Mailer Error: " . $mail->ErrorInfo;// 输出错误信息
        } else {
            return "发送成功";
        }
        die; 
 ?>
