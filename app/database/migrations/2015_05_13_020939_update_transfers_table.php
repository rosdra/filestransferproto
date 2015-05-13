<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateTransfersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfers', function(Blueprint $table)
        {
            $table->string("sender_email");
            $table->string("recipient_email");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfers', function(Blueprint $table)
        {
            $table->removeColumn('sender_email');
            $table->removeColumn('recipient_email');
        });
    }

}
