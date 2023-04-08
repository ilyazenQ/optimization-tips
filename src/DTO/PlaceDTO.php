<?php 

namespace App\DTO;

class PlaceDTO implements DTOInterface {

    public function __construct(
        public string $title
    ) {
    }

}
