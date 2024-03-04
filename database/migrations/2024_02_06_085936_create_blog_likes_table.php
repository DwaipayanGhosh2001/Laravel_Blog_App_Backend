<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_likes', function (Blueprint $table) {
            $table->id('like_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('blog_id');
            $table->timestamps();

            //This is the reference to the id of the user and when a user is deleted all the blogs related to the user will be deleted. 
//the foreign key is the user_id and the reference is taken through id on the users table.
$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
//same for blog foreign key making references to the blog table
$table->foreign('blog_id')->references('blog_id')->on('blogs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_likes');
    }
};
