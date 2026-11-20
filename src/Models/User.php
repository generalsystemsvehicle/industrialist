<?php

namespace GeneralSystemsVehicle\Industrialist\Models;

use GeneralSystemsVehicle\Industrialist\Models\AbstractStatelessModel as Model;

/**
 * A model of the expected user object returned by Driver implmentations
 * @method ?string getNameId()
 * @method ?string setNameId(?string $value)
 * @method ?string getNameIdFormat()
 * @method ?string setNameIdFormat(?string $value)
 * @method bool getIsAuthenticated()
 * @method bool setIsAuthenticated(bool $value)
 * @method ?string getSessionIndex()
 * @method ?string setSessionIndex(?string $value)
 * @method ?int getSessionExpiration()
 * @method ?int setSessionExpiration(?int $value)
 * @method array<string, mixed>|null getErrors()
 * @method array<string, mixed>|null setErrors(array<string, mixed>|null $value)
 * @method ?string getLastRequestId()
 * @method ?string setLastRequestId(?string $value)
 * @method ?string getErrorReason()
 * @method ?string setErrorReason(?string $value)
 * @method ?string getLastResponse()
 * @method ?string setLastResponse(?string $value)
 * @method ?string getLastRequest()
 * @method ?string setLastRequest(?string $value)
 * @method array<string, mixed>|null getAttributes()
 * @method array<string, mixed>|null setAttributes(array<string, mixed>|null $value)
 * @method array<string, mixed>|null getAttributesWithFriendlyName()
 * @method array<string, mixed>|null setAttributesWithFriendlyName(array<string, mixed>|null $value)
 */
class User extends Model
{
    /**
     * @var array<string, mixed>
     */
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
