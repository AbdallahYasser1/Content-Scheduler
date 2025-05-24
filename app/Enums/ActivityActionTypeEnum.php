<?php

namespace App\Enums;

enum ActivityActionTypeEnum :string
{
case CREATE = 'create';
case UPDATE = 'update';
case DELETE = 'delete';
case PUBLISH = 'publish';

}
