<?php 

namespace App\Types;

use App\Helpers\SqlValidator;

final class SqlQuery
{
    public function __construct(
        private string $query,
    ) {
        $this->query = SqlValidator::make($query);
    }

    public function __toString()
    {
        return $this->query;
    }
}
