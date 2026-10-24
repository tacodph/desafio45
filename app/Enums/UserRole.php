<?php

namespace App\Enums;

enum UserRole: string
{
    case Administrador = 'administrador';
    case Nutricionista = 'nutricionista';
    case Coach = 'coach';
    case Marketing = 'marketing';
    case Atleta = 'atleta';
}
