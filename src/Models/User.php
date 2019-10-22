<?php

namespace Riverbedlab\Industrialist\Models;

use Riverbedlab\Industrialist\Models\AbstractStatelessModel as Model;

class User extends Model
{
    protected $attributes = [
        'name_id' => null,
        'name_id_format' => null,
        'is_authenticated' => false,
        'session_index' => null,
        'session_expiration' => null,
        'errors' => [],
        'error_reason' => null,
        'last_request_id' => null,
        'last_request' => null,
        'last_response' => null,
        'attributes' => [],
        'attributes_with_friendly_name' => []
    ];
}
