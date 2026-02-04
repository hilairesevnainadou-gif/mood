<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trainings', function (Blueprint $table) {
            // Ajouter les champs manquants
            $table->string('short_description')->nullable()->after('description');
            $table->string('category')->nullable()->after('short_description');
            $table->string('subcategory')->nullable()->after('category');
            $table->string('level')->default('beginner')->after('subcategory');
            $table->string('duration_unit')->default('minutes')->after('duration_minutes');
            $table->decimal('price', 10, 2)->default(0)->after('duration_unit');
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->string('currency')->default('XOF')->after('discount_price');
            $table->string('instructor_name')->nullable()->after('currency');
            $table->text('instructor_bio')->nullable()->after('instructor_name');
            $table->string('cover_image')->nullable()->after('instructor_bio');
            $table->string('video_url')->nullable()->after('cover_image');
            $table->timestamp('start_date')->nullable()->after('video_url');
            $table->timestamp('end_date')->nullable()->after('start_date');
            $table->timestamp('registration_deadline')->nullable()->after('end_date');
            $table->integer('max_participants')->nullable()->after('registration_deadline');
            $table->integer('current_participants')->default(0)->after('max_participants');
            $table->string('language')->default('fr')->after('current_participants');
            $table->string('format')->default('online')->after('language');
            $table->text('prerequisites')->nullable()->after('format');
            $table->text('learning_objectives')->nullable()->after('prerequisites');
            $table->boolean('certification_included')->default(false)->after('learning_objectives');
            $table->string('certification_name')->nullable()->after('certification_included');
            $table->boolean('is_featured')->default(false)->after('certification_name');
            $table->boolean('is_popular')->default(false)->after('is_featured');
            $table->decimal('rating', 3, 2)->default(0)->after('is_popular');
            $table->integer('reviews_count')->default(0)->after('rating');
            $table->string('meta_title')->nullable()->after('reviews_count');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');
            
            // Ajouter un index pour optimiser les recherches
            $table->index(['category', 'is_active']);
            $table->index(['start_date', 'end_date']);
            $table->index('is_featured');
            $table->index('is_popular');
        });
    }

    public function down()
    {
        Schema::table('trainings', function (Blueprint $table) {
            // Supprimer les champs ajoutés
            $table->dropColumn([
                'short_description',
                'category',
                'subcategory',
                'level',
                'duration_unit',
                'price',
                'discount_price',
                'currency',
                'instructor_name',
                'instructor_bio',
                'cover_image',
                'video_url',
                'start_date',
                'end_date',
                'registration_deadline',
                'max_participants',
                'current_participants',
                'language',
                'format',
                'prerequisites',
                'learning_objectives',
                'certification_included',
                'certification_name',
                'is_featured',
                'is_popular',
                'rating',
                'reviews_count',
                'meta_title',
                'meta_description',
                'meta_keywords'
            ]);
            
            // Supprimer les index
            $table->dropIndex(['category', 'is_active']);
            $table->dropIndex(['start_date', 'end_date']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['is_popular']);
        });
    }
};