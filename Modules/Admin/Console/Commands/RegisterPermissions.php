<?php

namespace Modules\Admin\Console\Commands;

use App;
use \DB;
use \Module;
use Modules\Admin\Database\Seeders\AdministratorSeeder;
use Modules\Admin\Entities\Role;
use Modules\Core\Services\CacheService;
use \ReflectionClass;
use \Route;
use Illuminate\Console\Command;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Entities\Guard;
use Modules\Admin\Entities\Permission;

class RegisterPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:permissions:register';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Script for automatic permissions adding to DB.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function handle()
    {
        $start = microtime(true);
        DB::table('permission')
            ->where('active', '=', 1)
            ->update(['active' => 0]);

        $routes = Route::getRoutes();
        $excludedMethods = config('permission.exclude_methods');

        foreach ($routes as $route) {
            $routeName = $route->getName();
            if ($routeName === null) {
                continue;
            }

            $action = $route->getActionMethod();
            if (isset($excludedMethods[$action]) || empty($route->defaults['description'])) {
                continue;
            }

            $checkModule = explode('\\', $route->getActionName());
            $module = Module::find($checkModule[1]);
            $moduleName = $checkModule[0];
            if ($module) {
                $moduleName = $module->getName();
            }

            $class = (new ReflectionClass($route->getController())) ->getShortName();

            $permission = [
                'description' => $route->defaults['description'],
                'guard_name' => Guard::DEFAULT_GUARD_NAME,
                'name' => $routeName,
                'module' => $moduleName,
                'controller' => $class,
                'action' => $action,
                'active' => 1,
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ];

            Permission::updateOrCreate(
                [
                    'name' => $routeName,
                ],
                $permission
            );
        }

        $roles = Role::where('priority', Role::PRIORITY_MAX)->get();
        $permissions = Permission::all();
        foreach ($roles as $role) {
            $role->permissions()->sync($permissions);

            $this->assignPermissions($role, $permissions);
        }

        $this->info('Time spent: ' . (microtime(true) - $start));
    }

    /**
     * @param $role
     * @param $permissions
     */
    protected function assignPermissions($role, $permissions)
    {
        $administrators = Administrator::whereHas('roles', function ($query) use ($role) {
            $query->where('role_id', $role->id);
        })->get();

        foreach ($administrators as $administrator) {
            $administrator->permissions()->sync($permissions);
        }

        $cacheService = App::make(CacheService::class);
        $cacheService->removeByPattern('allowed_permissions_*');
    }
}
