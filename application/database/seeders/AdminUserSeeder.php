<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Services\Eloquent\UserService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * @param UserService $userService
     */
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * @return void
     */
    public function run()
    {
        $adminEmail = Config::get('admin.email');
        if ($this->userService->query()->where('email', $adminEmail)->exists()) {
            return;
        }
        $this->userService->firstOrCreate([
            'name' => Config::get('admin.name'),
            'email' => Config::get('admin.email'),
            'password' => Hash::make((Config::get('admin.password'))),
        ]);
    }
}
