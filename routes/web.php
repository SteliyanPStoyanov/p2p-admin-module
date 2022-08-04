<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * You can check default auth behavior below:
 * vendor\laravel\ui\src\AuthRouteMethods.php
 */


use Illuminate\Support\Facades\Route;

Route::get('/', '\App\Http\Controllers\HomePageController@index')->name('homepage');
Route::get('/how-it-works', '\App\Http\Controllers\HomePageController@howItWorks')->name('howItWorks');
Route::get('/about-us', '\App\Http\Controllers\HomePageController@aboutUs')->name('aboutUs');
Route::get('/user-agreement', '\App\Http\Controllers\HomePageController@template')->name('userAgreement');
Route::get('/assignment-agreement', '\App\Http\Controllers\HomePageController@assignmentAgreementTemplate')->name('assignmentAgreement');
Route::get('/privacy-policy', '\App\Http\Controllers\HomePageController@privacyPolicy')->name('privacyPolicy');
Route::get('/cookie-policy', '\App\Http\Controllers\HomePageController@privacyPolicy')->name('cookiePolicy');
Route::get('/refer-a-friend-t-and-c', '\App\Http\Controllers\HomePageController@referAFriend')->name('referAFriend');
Route::get('/help', '\App\Http\Controllers\HomePageController@help')->name('help');
Route::get('/refer-a-friend', '\App\Http\Controllers\HomePageController@affiliate')->name('affiliate');
Route::get('/blog', '\App\Http\Controllers\HomePageController@blog')->name('blog');

Route::get('/blog/get-blog-pages','HomePageController@AjaxBlogPages')->name('blog-page.ajax-blog-pages');

Route::get('/loan-originators', '\App\Http\Controllers\HomePageController@loanOriginators')->name('loan-originators');
Route::get('/invest', '\App\Http\Controllers\HomePageController@invest')->name('invest');
Route::get('/invest-refresh', '\App\Http\Controllers\HomePageController@refresh')->name('invest-refresh');

Route::group(['prefix' => 'admin'], function () {
    Auth::routes(
        [
            'register' => false,
            'reset' => false,
            'confirm' => false,
        ]
    );
});


/**
 * Only home page
 * All other routes should be separated via module
 */
Route::group(
    ['middleware' => ['auth']],
    function () {
        Route::get('/admin', 'DashboardController@index')->name('admin');
        Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
    }
);
