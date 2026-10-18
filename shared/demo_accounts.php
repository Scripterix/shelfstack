<?php
declare(strict_types=1);

/**
 * Public credentials used only by this local exercise application.
 *
 * @return array<string, array<string, string>>
 */
function demo_accounts(): array
{
    return [
        'User1' => [
            'nickname' => 'User1',
            'display_name' => 'John Dee',
            'password' => 'JD1234#',
            'role' => 'customer',
        ],
        'User2' => [
            'nickname' => 'User2',
            'display_name' => 'Alice Chain',
            'password' => 'AC1234#',
            'role' => 'customer',
        ],
        'Admin' => [
            'nickname' => 'Admin',
            'display_name' => 'Administrator',
            'password' => 'Admin1234#',
            'role' => 'admin',
        ],
    ];
}

