<?php

namespace App\Models\Assignment\DataTransferObjects;

use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\File;
use App\User;

class ItemlistData extends Data
{
    public function __construct(
        public ?int $user_id,
        public ?int $tipus,
        public ?int $status,
        public ?string $json,
        public File $file,
        public User $user,
    ) {
    }
}
