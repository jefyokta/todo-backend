<?php

namespace App\Schema;

class BaseJsonSchema
{
    public bool $success;
    public string $message;
    public mixed $data;
    public mixed $errors;
    public array $meta;

    public function __construct(
        bool $success = true,
        string $message = 'OK',
        mixed $data = [],
        mixed $errors = null,
        array $meta = []
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->errors = $errors;
        $this->meta = $meta;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'errors' => $this->errors,
            'meta' => $this->meta,
        ];
    }
}
