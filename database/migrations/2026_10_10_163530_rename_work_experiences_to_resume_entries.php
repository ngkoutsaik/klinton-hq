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
        Schema::table('work_experiences', function (Blueprint $table) {
            $table->dropForeign(['resume_id']);
            $table->dropIndex('work_experiences_resume_id_foreign');
        });

        Schema::rename('work_experiences', 'resume_entries');

        Schema::table('resume_entries', function (Blueprint $table) {
            $table->renameColumn('role_name', 'title');
            $table->renameColumn('company_name', 'organization');
            $table->string('type')->default('work');
            $table->text('description')->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->index(['resume_id', 'type']);
            $table->foreign('resume_id')->references('id')->on('resumes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resume_entries', function (Blueprint $table) {
            $table->dropForeign(['resume_id']);
            $table->dropIndex(['resume_id', 'type']);
            $table->dropColumn('type');
            $table->renameColumn('title', 'role_name');
            $table->renameColumn('organization', 'company_name');
            $table->text('description')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
        });
        Schema::rename('resume_entries', 'work_experiences');

        Schema::table('work_experiences', function (Blueprint $table) {
            $table->foreign('resume_id')->references('id')->on('resumes')->cascadeOnDelete();
        });
    }
};
