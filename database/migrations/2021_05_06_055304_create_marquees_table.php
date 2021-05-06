<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarqueesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marquees', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('forever')->default(2)->comment('長期播放(1:是, 2:否)');
            $table->dateTime('start_at')->nullable()->comment('開始播放時間');
            $table->dateTime('end_at')->nullable()->comment('結束播放時間');
            $table->string('title', 100)->comment('跑馬燈標題');
            $table->string('description', 100)->comment('跑馬燈內容');
            $table->set('display', ['0','1','2','3','4'])->comment('顯示(0:全站, 1:總監, 2:股東, 3:代理, 4: 會員)');
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
        Schema::dropIfExists('marquees');
    }
}
