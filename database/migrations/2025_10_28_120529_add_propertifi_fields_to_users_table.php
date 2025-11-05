<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPropertifiFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('type')->default('User')->after('id');
            $table->string('mobile')->nullable()->after('email');
            $table->string('gender')->nullable()->after('mobile');
            $table->date('dob')->nullable()->after('gender');
            $table->text('address')->nullable()->after('password');
            $table->string('city')->nullable()->after('address');
            $table->string('zipcode')->nullable()->after('city');
            $table->string('photo')->nullable()->after('zipcode');
            $table->string('state')->nullable()->after('photo');
            $table->string('country')->nullable()->after('state');
            $table->timestamp('last_login')->nullable()->after('country');
            $table->boolean('is_delete')->default(0)->after('last_login');
            $table->string('company_name')->nullable()->after('is_delete');
            $table->boolean('email_varification')->default(0)->after('company_name');
            $table->text('about')->nullable()->after('email_varification');
            $table->string('p_contact_name')->nullable()->after('about');
            $table->string('p_contact_no')->nullable()->after('p_contact_name');
            $table->string('p_contact_email')->nullable()->after('p_contact_no');
            $table->string('position')->nullable()->after('p_contact_email');
            $table->boolean('featured')->default(0)->after('position');
            $table->integer('credits')->default(0)->after('featured');
            $table->boolean('single_family')->default(0)->after('credits');
            $table->boolean('multi_family')->default(0)->after('single_family');
            $table->boolean('association_property')->default(0)->after('multi_family');
            $table->boolean('commercial_property')->default(0)->after('association_property');
            $table->string('website')->nullable()->after('commercial_property');
            $table->string('city_extra')->nullable()->after('website');
            $table->string('state_extra')->nullable()->after('city_extra');
            $table->string('otp')->nullable()->after('state_extra');
            $table->string('country_code')->nullable()->after('otp');
            $table->unsignedBigInteger('role_id')->nullable()->after('country_code');
            $table->unsignedBigInteger('added_by')->nullable()->after('role_id');
            $table->string('portfolio_type')->nullable()->after('added_by');
            $table->integer('units')->nullable()->after('portfolio_type');
            $table->boolean('status')->default(1)->after('units');
            $table->string('temp_pass')->nullable()->after('status');
            $table->string('slug')->nullable()->unique()->after('temp_pass');
            $table->string('seo_title')->nullable()->after('slug');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->text('seo_keywords')->nullable()->after('seo_description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'type', 'mobile', 'gender', 'dob', 'address', 'city', 'zipcode',
                'photo', 'state', 'country', 'last_login', 'is_delete',
                'company_name', 'email_varification', 'about', 'p_contact_name',
                'p_contact_no', 'p_contact_email', 'position', 'featured', 'credits',
                'single_family', 'multi_family', 'association_property',
                'commercial_property', 'website', 'city_extra', 'state_extra',
                'otp', 'country_code', 'role_id', 'added_by', 'portfolio_type',
                'units', 'status', 'temp_pass', 'slug', 'seo_title',
                'seo_description', 'seo_keywords'
            ]);
        });
    }
}
