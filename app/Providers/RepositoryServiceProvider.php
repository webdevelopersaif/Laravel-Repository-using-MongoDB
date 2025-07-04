<?php
namespace App\Providers;

use App\Repositories\PostRepositoryInterface;
use App\Repositories\EloquentPostRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(PostRepositoryInterface::class, EloquentPostRepository::class);
    }

    public function boot()
    {
        //
    }
}