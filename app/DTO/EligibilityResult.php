<?php

namespace App\DTO;

class EligibilityResult
{
    public function __construct(
        public bool $allowed,
        public ?string $reason = null
    ) {}
    
}