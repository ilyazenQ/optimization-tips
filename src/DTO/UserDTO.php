<?php 

namespace App\DTO;

class UserDTO implements DTOInterface {

    public function __construct(
        public string $name,
        public bool  $isActive
    ) {
    }

}