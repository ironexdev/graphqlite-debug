<?php declare(strict_types=1);

namespace App\GraphQL;

use TheCodingMachine\GraphQLite\Annotations\Mutation;

class IndexController
{
    #[Mutation]
    public function foo(): bool
    {
        return true;
    }
}
