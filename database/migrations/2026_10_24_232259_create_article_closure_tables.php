<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_closure_runs', function (Blueprint $table): void {
            $table->id();
            $table->string('kind');
            $table->string('status')->default('running');
            $table->timestamp('started_at');
            $table->timestamp('heartbeat_at');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'heartbeat_at']);
        });
        Schema::create('article_closure_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('run_id')->constrained('article_closure_runs');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->default('pending');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('next_attempt_at')->nullable();
            $table->timestamp('dispatch_lease_until')->nullable();
            $table->timestamp('sending_at')->nullable();
            $table->uuid('sending_token')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
            $table->unique(['run_id', 'user_id']);
            $table->index(['status', 'next_attempt_at']);
        });
        Schema::create('article_closure_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('batch_id')->constrained('article_closure_batches');
            $table->unsignedBigInteger('article_id');
            $table->string('title');
            $table->text('url');
            $table->text('edit_url');
            $table->string('reason');
            $table->timestamp('closed_at');
            $table->timestamps();
            $table->unique(['batch_id', 'article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_closure_items');
        Schema::dropIfExists('article_closure_batches');
        Schema::dropIfExists('article_closure_runs');
    }
};
