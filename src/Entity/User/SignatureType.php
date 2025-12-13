<?php

namespace App\Entity\User;

enum SignatureType: string {
    case EMAIL = 'EMAIL';
    case PASSWORD = 'PASSWORD';
}
