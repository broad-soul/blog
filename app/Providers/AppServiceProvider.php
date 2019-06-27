<?php

namespace App\Providers;

use App\Comment;
use Illuminate\Support\ServiceProvider;
use App\Post;
use App\Category;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Schema::defaultStringLength(191);
        view()->composer('pages._sidebar', function ($view){
            $view->with('popularPosts', Post::getPopularPosts(3));
            $view->with('featuredPosts', Post::featuredPosts(2));
            $view->with('recentPosts', Post::recentPosts(4));
            $view->with('categories', Category::all());
        });
        view()->composer('admin._sidebar', function ($view){
            $view->with('newCommentsCount', Comment::where('status', 0)->count());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
