<?php

namespace App\DTO;

use App\Traits\ArrayToProps;

class UserDTO
{
    use ArrayToProps;

    public $first_name;
    public $last_name;
    public $email;
    public $contact_no;
    public $address;
    public $dob;
    public $role_id;
    public $password;
}
