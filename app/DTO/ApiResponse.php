<?php 

namespace App\DTO;

class ApiResponse
{   
    /**
     * @param ApiResponseError[] $errors Sempre opte por adicionar errros usando setErrors()
     */
    public function __construct(
        public bool   $success,
        public string $message,
        public array  $data = [],
        public ApiResponseError|array $errors = [],
        public int    $statusCode = 200,
    ) {
        //
    }

    /**
     * @param ApiResponseError[] $errors Sempre opte por adicionar errros usando setErrors()
     */
    public static function make(
        bool   $success,
        string $message,
        array  $data = [],
        ApiResponseError|array $errors = [],
        int    $statusCode = 200,
    ): self 
    {
        return new self(
            success: $success,
            message: $message,
            data: $data,
            errors: $errors,
            statusCode: $statusCode,
        );
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        
        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        
        return $this;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        
        return $this;
    }

    public function setErrors(ApiResponseError $errors): self
    {
        $this->errors[] =  $errors;
        
        return $this;
    }
    
    public function setStatusCode(int $statusCode = 200): self
    {
        $this->statusCode = $statusCode;
        
        return $this;
    }

    public function toArray(): array
    {
        return [
            'success'    => $this->success,
            'message'    => $this->message,
            'data'       => $this->data,
            'errors'     => $this->errors,
            'statusCode' => $this->statusCode,
        ];
    }
}
