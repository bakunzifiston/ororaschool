<?php

namespace App;

enum UserRole: string
{
    case SuperAdmin = 'super-admin';
    case PlatformStaff = 'platform-staff';
    case Learner = 'learner';
}
