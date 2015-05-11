<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersFileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transfer_file', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string("original_name");
            $table->string("object_name");
            $table->bigInteger("size");
            $table->string("mimetype");
            $table->string("slug");
                $table->integer("transfer_id");
			$table->timestamps();
            $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('transfer_file');
	}

}
