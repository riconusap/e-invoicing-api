<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Invoice;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Placement;
use App\Models\ContractClient;
use App\Models\ContractEmployee;
use App\Models\PicExternal;
use App\Policies\InvoicePolicy;
use App\Policies\EmployeePolicy;
use App\Policies\ClientPolicy;
use App\Policies\PlacementPolicy;
use App\Policies\ContractClientPolicy;
use App\Policies\ContractEmployeePolicy;
use App\Policies\PicExternalPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Invoice::class => InvoicePolicy::class,
        Employee::class => EmployeePolicy::class,
        Client::class => ClientPolicy::class,
        Placement::class => PlacementPolicy::class,
        ContractClient::class => ContractClientPolicy::class,
        ContractEmployee::class => ContractEmployeePolicy::class,
        PicExternal::class => PicExternalPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
