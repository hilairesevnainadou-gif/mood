<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Liste des colonnes à ajouter
            $columnsToAdd = [
                'project_name' => function($table) {
                    if (!Schema::hasColumn('users', 'project_name')) {
                        $table->string('project_name')->nullable()->after('sector');
                    }
                },
                'project_type' => function($table) {
                    if (!Schema::hasColumn('users', 'project_type')) {
                        $table->string('project_type')->nullable()->after('project_name');
                    }
                },
                'project_description' => function($table) {
                    if (!Schema::hasColumn('users', 'project_description')) {
                        $table->text('project_description')->nullable()->after('project_type');
                    }
                },
                'funding_needed' => function($table) {
                    if (!Schema::hasColumn('users', 'funding_needed')) {
                        $table->decimal('funding_needed', 15, 2)->nullable()->after('project_description');
                    }
                },
                'expected_jobs' => function($table) {
                    if (!Schema::hasColumn('users', 'expected_jobs')) {
                        $table->integer('expected_jobs')->default(0)->after('funding_needed');
                    }
                },
                'project_duration' => function($table) {
                    if (!Schema::hasColumn('users', 'project_duration')) {
                        $table->integer('project_duration')->nullable()->after('expected_jobs');
                    }
                },
                'own_contribution' => function($table) {
                    if (!Schema::hasColumn('users', 'own_contribution')) {
                        $table->decimal('own_contribution', 15, 2)->nullable()->after('project_duration');
                    }
                },
                'expected_revenue' => function($table) {
                    if (!Schema::hasColumn('users', 'expected_revenue')) {
                        $table->decimal('expected_revenue', 15, 2)->nullable()->after('own_contribution');
                    }
                }
            ];

            // Ajouter chaque colonne si elle n'existe pas
            foreach ($columnsToAdd as $column => $callback) {
                $callback($table);
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Vous pouvez choisir de supprimer ou non les colonnes lors du rollback
            // $table->dropColumn([
            //     'project_name',
            //     'project_type',
            //     'project_description',
            //     'funding_needed',
            //     'expected_jobs',
            //     'project_duration',
            //     'own_contribution',
            //     'expected_revenue'
            // ]);
        });
    }
};
