<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing questions
        DB::table('questions')->truncate();

        $questions = [
            // Step 1: Property Type
            [
                'question_text' => 'What type of property do you manage?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'residential', 'label' => 'Residential'],
                    ['value' => 'commercial', 'label' => 'Commercial'],
                    ['value' => 'mixed', 'label' => 'Mixed Use'],
                    ['value' => 'hoa', 'label' => 'HOA/Community Association'],
                ],
                'help_text' => 'Select the type of property you need management services for',
                'placeholder' => null,
                'step' => 1,
                'order_in_step' => 0,
                'is_required' => true,
                'validation_rules' => ['required'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],

            // Step 2: Property Address
            [
                'question_text' => 'Property Street Address',
                'input_type' => 'text',
                'options' => null,
                'help_text' => 'Enter the full street address of your property',
                'placeholder' => '123 Main Street',
                'step' => 2,
                'order_in_step' => 0,
                'is_required' => true,
                'validation_rules' => ['required', 'string', 'max:255'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'City',
                'input_type' => 'text',
                'options' => null,
                'help_text' => null,
                'placeholder' => 'Enter city name',
                'step' => 2,
                'order_in_step' => 1,
                'is_required' => true,
                'validation_rules' => ['required', 'string', 'max:100'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'State',
                'input_type' => 'select',
                'options' => null, // Will be populated dynamically from states API
                'help_text' => null,
                'placeholder' => 'Select state',
                'step' => 2,
                'order_in_step' => 2,
                'is_required' => true,
                'validation_rules' => ['required'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'ZIP Code',
                'input_type' => 'text',
                'options' => null,
                'help_text' => null,
                'placeholder' => '12345',
                'step' => 2,
                'order_in_step' => 3,
                'is_required' => true,
                'validation_rules' => ['required', 'regex:/^\d{5}$/'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],

            // Step 3: Property Details
            [
                'question_text' => 'Number of Units',
                'input_type' => 'number',
                'options' => null,
                'help_text' => 'How many units does your property have?',
                'placeholder' => 'Enter number of units',
                'step' => 3,
                'order_in_step' => 0,
                'is_required' => true,
                'validation_rules' => ['required', 'integer', 'min:1'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'Total Square Footage',
                'input_type' => 'number',
                'options' => null,
                'help_text' => 'Approximate total square footage of the property',
                'placeholder' => 'Enter square footage',
                'step' => 3,
                'order_in_step' => 1,
                'is_required' => false,
                'validation_rules' => ['nullable', 'integer', 'min:1'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'Additional Services Needed',
                'input_type' => 'multiselect',
                'options' => [
                    ['value' => 'maintenance', 'label' => 'Maintenance & Repairs'],
                    ['value' => 'landscaping', 'label' => 'Landscaping'],
                    ['value' => 'snow_removal', 'label' => 'Snow Removal'],
                    ['value' => 'cleaning', 'label' => 'Cleaning Services'],
                    ['value' => 'pest_control', 'label' => 'Pest Control'],
                    ['value' => 'security', 'label' => 'Security Services'],
                ],
                'help_text' => 'Select all services you might need (optional)',
                'placeholder' => null,
                'step' => 3,
                'order_in_step' => 2,
                'is_required' => false,
                'validation_rules' => ['nullable', 'array'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],

            // Step 4: Contact Information
            [
                'question_text' => 'Full Name',
                'input_type' => 'text',
                'options' => null,
                'help_text' => null,
                'placeholder' => 'John Doe',
                'step' => 4,
                'order_in_step' => 0,
                'is_required' => true,
                'validation_rules' => ['required', 'string', 'max:100'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'Email Address',
                'input_type' => 'email',
                'options' => null,
                'help_text' => null,
                'placeholder' => 'john@example.com',
                'step' => 4,
                'order_in_step' => 1,
                'is_required' => true,
                'validation_rules' => ['required', 'email', 'max:255'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'Phone Number',
                'input_type' => 'phone',
                'options' => null,
                'help_text' => null,
                'placeholder' => '(555) 123-4567',
                'step' => 4,
                'order_in_step' => 2,
                'is_required' => true,
                'validation_rules' => ['required', 'regex:/^[\d\s\-\(\)]+$/'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'Company Name (if applicable)',
                'input_type' => 'text',
                'options' => null,
                'help_text' => null,
                'placeholder' => 'Your Company LLC',
                'step' => 4,
                'order_in_step' => 3,
                'is_required' => false,
                'validation_rules' => ['nullable', 'string', 'max:255'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],

            // Step 5: Additional Information
            [
                'question_text' => 'When do you need property management services?',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'immediately', 'label' => 'Immediately'],
                    ['value' => '1-3_months', 'label' => 'Within 1-3 months'],
                    ['value' => '3-6_months', 'label' => 'Within 3-6 months'],
                    ['value' => 'just_researching', 'label' => 'Just researching'],
                ],
                'help_text' => null,
                'placeholder' => null,
                'step' => 5,
                'order_in_step' => 0,
                'is_required' => true,
                'validation_rules' => ['required'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'Current Property Management Status',
                'input_type' => 'radio',
                'options' => [
                    ['value' => 'self_managed', 'label' => 'Currently self-managing'],
                    ['value' => 'have_manager', 'label' => 'Have a property manager'],
                    ['value' => 'new_property', 'label' => 'New property acquisition'],
                ],
                'help_text' => null,
                'placeholder' => null,
                'step' => 5,
                'order_in_step' => 1,
                'is_required' => true,
                'validation_rules' => ['required'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
            [
                'question_text' => 'Additional Comments or Special Requirements',
                'input_type' => 'textarea',
                'options' => null,
                'help_text' => 'Tell us anything else we should know about your property or needs',
                'placeholder' => 'Enter any additional details...',
                'step' => 5,
                'order_in_step' => 2,
                'is_required' => false,
                'validation_rules' => ['nullable', 'string', 'max:1000'],
                'depends_on_question_id' => null,
                'depends_on_answer' => null,
                'is_active' => true,
            ],
        ];

        foreach ($questions as $question) {
            Question::create($question);
        }

        $this->command->info('Successfully seeded ' . count($questions) . ' questions across 5 steps.');
    }
}
