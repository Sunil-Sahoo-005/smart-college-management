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
            Schema::create('student_class_enrollments', function (Blueprint $table) {
                $table->id();

                // SaaS + academic scope
                $table->foreignId('tenant_id')
                    ->constrained('tenants')
                    ->cascadeOnDelete();

                $table->foreignId('academic_year_id')
                    ->constrained('academic_years')
                    ->cascadeOnDelete();

                // Core relations
                $table->foreignId('student_id')
                    ->constrained('students')
                    ->cascadeOnDelete();

                $table->foreignId('class_id')
                    ->constrained('classes')
                    ->cascadeOnDelete();

                $table->foreignId('section_id')
                    ->constrained('sections')
                    ->cascadeOnDelete();

                $table->foreignId('stream_id')
                    ->nullable()
                    ->constrained('streams')
                    ->nullOnDelete();

                // Student classification
                $table->enum('student_type', ['regular', 'lateral'])
                    ->nullable();

                $table->enum('residence_type', ['day_scholar', 'hosteller'])
                    ->nullable();

                $table->date('dateof_admission')->nullable();//new
                $table->date('date_of_leaving')->nullable(); //new

                $table->enum('status', [
                    'active',
                    'promoted',
                    'transferred',
                    'completed'
                ])->default('active');

               

                $table->timestamps();

                $table->unique(['student_id', 'academic_year_id']);
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('student_class_enrollments');
        }
    };