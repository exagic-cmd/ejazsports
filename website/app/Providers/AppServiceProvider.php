<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot()
    {
        View::composer(['layouts.header', 'web.layouts.header'], function ($view) {
            $categories = DB::table('categories as c')
                ->leftJoin('category_parents as cp', 'c.id', '=', 'cp.category_id')
                ->leftJoin('categories as p', 'cp.parent_category_id', '=', 'p.id')
                ->where('c.show_in_menu', 1)
                ->select(
                    'c.id as category_id',
                    'c.title as category_title',
                    'p.id as parent_id',
                    'p.title as parent_title'
                )
                ->get();

            $grouped = $categories->groupBy('parent_id');

            $view->with('grouped', $grouped);
        });
    }
}
