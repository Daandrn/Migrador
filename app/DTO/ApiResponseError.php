<?php 

namespace App\DTO;

readonly class ApiResponseError
{
    public function __construct(
        public string  $code,
        public string  $message,
        public ?string $field,
        public array   $details = [],
    ) {
        //
    }

    public static function make(
        string  $code,
        string  $message,
        ?string $field = null,
        array   $details = [],
    ): self 
    {
        return new self(
            code: $code,
            message: $message,
            field: $field,
            details: $details,
        );
    }

    public function toArray(): array
    {
        return [
            'code'    => $this->code,
            'message' => $this->message,
            'field'   => $this->field,
            'details' => $this->details,
        ];
    }
}
