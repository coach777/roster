<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ActivityType;


return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->enum('type', array_map(fn($at): string => $at->value, array_values(ActivityType::cases())));
            $table->timestamp('starts')->nullable();
            $table->timestamp('ends')->nullable();

            $table->string('from', 6)->nullable();
            $table->string('to', 6)->nullable();

            $table->string('activity_remark')->nullable();
            $table->text('row')->nullable();
            $table->timestamps();
        });

        //Initial roster seed
        $service = new App\Services\RosterService();
        $service->seedDemoRoster();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
