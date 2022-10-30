<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCinemaSchema extends Migration
{
    /** ToDo: Create a migration that creates all tables for the following user stories

    For an example on how a UI for an api using this might look like, please try to book a show at https://in.bookmyshow.com/.
    To not introduce additional complexity, please consider only one cinema.

    Please list the tables that you would create including keys, foreign keys and attributes that are required by the user stories.

    ## User Stories

     **Movie exploration**
     * As a user I want to see which films can be watched and at what times
     * As a user I want to only see the shows which are not booked out

     **Show administration**
     * As a cinema owner I want to run different films at different times
     * As a cinema owner I want to run multiple films at the same time in different showrooms

     **Pricing**
     * As a cinema owner I want to get paid differently per show
     * As a cinema owner I want to give different seat types a percentage premium, for example 50 % more for vip seat

     **Seating**
     * As a user I want to book a seat
     * As a user I want to book a vip seat/couple seat/super vip/whatever
     * As a user I want to see which seats are still available
     * As a user I want to know where I'm sitting on my ticket
     * As a cinema owner I dont want to configure the seating for every show
     */
    public function up()
    {
        Schema::create('roles', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('films', function($table) {
            $table->increments('id');
            $table->string('title', 64);
            $table->text('description')->nullable;
            $table->dateTime('duration');
            $table->string('language', 16);
            $table->string('genre');
            $table->dateTime('release_date');
            $table->string('country', 64);
            $table->timestamps();
        });

        Schema::create('cinema', function($table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->integer('total_showrooms');
            $table->string('address');
            $table->string('phone');
            $table->timestamps();
        });

        Schema::create('showrooms', function($table) {
            $table->increments('id');
            $table->string('name', 64);
            $table->integer('total_seats');
            $table->integer('cinema_id')->unsigned();
            $table->foreign('cinema_id')->references('id')->on('cinema')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('showroom_seat', function($table) {
            $table->increments('id');
            $table->integer('seat_number');
            $table->string('seat_type');
            $table->integer('showroom_id')->unsigned();
            $table->foreign('showroom_id')->references('id')->on('showrooms')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('shows', function($table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->integer('showroom_id')->unsigned();
            $table->foreign('showroom_id')->references('id')->on('showrooms')->onDelete('cascade');
            $table->integer('film_id')->unsigned();
            $table->foreign('film_id')->references('id')->on('films')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('bookings', function($table) {
            $table->increments('id');
            $table->integer('number_of_seat');
            $table->dateTime('time');
            $table->integer('status')->default(1);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('show_seat', function($table) {
            $table->increments('id');
            $table->integer('status')->default(1);
            $table->float('price', 2);
            $table->integer('showroom_seat_id')->unsigned();
            $table->foreign('showroom_seat_id')->references('id')->on('showroom_seat')->onDelete('cascade');
            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows')->onDelete('cascade');
            $table->integer('booking_id')->unsigned();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('payments', function($table) {
            $table->increments('id');
            $table->float('amount', 2);
            $table->dateTime('date');
            $table->float('discount');
            $table->integer('transaction_id');
            $table->string('payment_method');
            $table->integer('booking_id')->unsigned();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('tickets', function($table) {
            $table->increments('id');
            $table->integer('show_id')->unsigned();
            $table->foreign('show_id')->references('id')->on('shows')->onDelete('cascade');
            $table->integer('payment_id')->unsigned();
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
            $table->integer('booking_id')->unsigned();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('films');
        Schema::dropIfExists('cinema');
        Schema::dropIfExists('showrooms');
        Schema::dropIfExists('showroom_seat');
        Schema::dropIfExists('shows');
        Schema::dropIfExists('show_seat');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('roles');
    }
}
