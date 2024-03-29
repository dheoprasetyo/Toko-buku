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

Route::get('/', function(){
    return view('auth.login');
});

Auth::routes();
// fitur registrasi tidak relevan, untuk itu kita akan hapus route tersebut. Kini jika route register diakses maka akan diredirect ke login
Route::match(["GET", "POST"], "/register", function(){
    return redirect("/login");
})->name("register");

Route::get('/home', 'HomeController@index')->name('home');
Route::resource("users", "UserController");

Route::get('/categories/trash', 'CategoryController@trash')->name('categories.trash');
Route::get('/categories/{id}/restore', 'CategoryController@restore')->name('categories.restore');
Route::delete('/categories/{id}/delete-permanent','CategoryController@deletePermanent')->name('categories.delete-permanent');
// route untuk mencari kategori berdasarkan keyword. Route ini akan digunakan oleh select2 nantinya
Route::get('/ajax/categories/search','CategoryController@ajaxSearch');
Route::resource('categories', 'CategoryController');

Route::delete('/books/{id}/delete-permanent','BookController@deletePermanent')->name('books.delete-permanent');
Route::get('/books/trash', 'BookController@trash')->name('books.trash');
Route::post('/books/{id}/restore', 'BookController@restore')->name('books.restore');
Route::resource('books', 'BookController');