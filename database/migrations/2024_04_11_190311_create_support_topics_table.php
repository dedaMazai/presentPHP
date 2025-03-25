<?php

use App\Models\SupportTopics;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // Insert data
        $data = [
            [
                'id' => 1,
                'name' => 'Не отображаются начисления',
                'is_published' => true,
            ],
            [
                'id' => 2,
                'name' => 'Не отображаются ИПУ',
                'is_published' => true,
            ],
            [
                'id' => 3,
                'name' => 'Не отображаются оплаты',
                'is_published' => true,
            ],
            [
                'id' => 4,
                'name' => 'Не отображается помещение',
                'is_published' => true,
            ],
            [
                'id' => 5,
                'name' => 'Изменить номер',
                'is_published' => true,
            ],
            [
                'id' => 6,
                'name' => 'Прикрепить члена семьи',
                'is_published' => true,
            ],
            [
                'id' => 7,
                'name' => 'Смена собственника',
                'is_published' => true,
            ],
        ];

        foreach ($data as $datum) {
            SupportTopics::create($datum);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_topics');
    }
};
