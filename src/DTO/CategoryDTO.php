<?php 

namespace App\DTO;

class CategoryDTO implements DTOInterface {

    public function __construct(
        public string $title
    ) {
    }

}
