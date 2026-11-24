<?php

namespace App\Enums;

enum VerificationPurpose: string
{
    case Register = 'register';
    case Login = 'login';
    case PasswordReset = 'password_reset';
    case DonorApplication = 'donor_application';
}
