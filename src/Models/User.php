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
 * @method ?string getSessionExpiration()
 * @method ?int setSessionExpiration(?int $value)
 * @method ?array getErrors()
 * @method ?array setErrors(?array $value)
 * @method ?string getLastRequestId()
 * @method ?string setLastRequestId(?string $value)
 * @method ?string getErrorReason()
 * @method ?string setErrorReason(?string $value)
 * @method ?string getLastResponse()
 * @method ?string setLastResponse(?string $value)
 * @method ?string getLastRequest()
 * @method ?string setLastRequest(?string $value)
 * @method ?array getAttributes()
 * @method ?arrat setAttributes(?array $value)
 * @method ?array setAttributesWithFriendlyName()
 * @method ?array setAttributesWithFriendlyName(?array $value)
 */
class User extends Model
{
    /**
     * @var array
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
