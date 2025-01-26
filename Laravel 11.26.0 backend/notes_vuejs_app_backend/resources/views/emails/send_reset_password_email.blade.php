<!DOCTYPE html>
<html>
<head>
    <title></title>

    <!--
	You can put your custom CSS if you wish
    -->
</head>
<body>
    <?php
        //$passwordResetLink = url("/reset_password/$token/$account_type");
        $passwordResetLink = "http://localhost:5173/reset_password";
    ?>
    <p>Hello,</p>
    <p>
        We noticed that you requested to reset your password for your account. 
        Please <a href="{{$passwordResetLink}}">reset password</a>
        or copy and paste the link below in your browser or click on it 
        to reset it:
    </p>
    <p>
        <a href="{{$passwordResetLink}}">{{ $passwordResetLink }}</a>
    </p>
    <p>
        If you did not request a password reset, please ignore this email. 
        Your password will remain unchanged.
    </p>
    <p>
        For security reasons, this link will expire in 30 minutes. 
        {{--If you encounter any issues or 
        need further assistance, please don't hesitate to contact our support team at 
        <a href="[email]">[email]</a>.--}}
    </p>
    <p>
        <div>Best regards,</div>
        <div>{{config("app.name")}} Team</div>
    </p>
    <p><center>&copy; {{config("app.name")}} {{date("Y")}}</center></p>
</body>
</html>