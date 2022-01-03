<?php declare(strict_types=1);

namespace App\GraphQL\User;

use App\GraphQL\User\Input\CreateUserInput;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

class UserController
{
    #[Mutation]
    public function createUser(
        CreateUserInput $createUserInput
    ): bool
    {
        return true;
    }
}
