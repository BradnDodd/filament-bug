<?php

use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/post/{post}', function (Post $post){
    return view('pages.show-post', ['post' => $post]);
})->name('post.show');
