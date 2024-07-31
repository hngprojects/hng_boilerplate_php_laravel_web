<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faqs = [
            [
                'question' => 'What is the return policy?',
                'answer' => 'Our return policy allows you to return products within 30 days of purchase. The items must be in their original condition with all packaging and tags intact. For more details, visit our return policy page.',
            ],
            [
                'question' => 'How do I track my order?',
                'answer' => 'Once your order has been shipped, you will receive an email with a tracking number. You can use this number on our website to track the status of your delivery.',
            ],
            [
                'question' => 'What payment methods are accepted?',
                'answer' => 'We accept various payment methods including credit/debit cards, PayPal, and bank transfers. For a full list of accepted payment methods, please visit our payment information page.',
            ],
            [
                'question' => 'How do I contact customer support?',
                'answer' => 'You can contact our customer support team via email, phone, or live chat. Visit our contact us page for more information on how to reach us.',
            ],
            [
                'question' => 'Are there any discounts for bulk purchases?',
                'answer' => 'Yes, we offer discounts for bulk purchases. Please contact our sales team with your requirements, and we will provide you with a custom quote.',
            ],
            [
                'question' => 'How do I create an account?',
                'answer' => 'Creating an account is easy. Click on the "Sign Up" button at the top of our website, and fill in your details. Once registered, you can enjoy a faster checkout process and keep track of your orders.',
            ],
            [
                'question' => 'What do I do if I receive a defective product?',
                'answer' => 'If you receive a defective product, please contact our customer support team immediately. We will arrange for a replacement or a refund as per our return policy.',
            ],
            [
                'question' => 'Do you ship internationally?',
                'answer' => 'Yes, we ship to many countries around the world. International shipping costs and delivery times vary based on your location. For more details, please visit our shipping information page.',
            ],
            [
                'question' => 'How can I apply a discount code?',
                'answer' => 'You can apply a discount code at checkout. Enter the code in the designated field and click "Apply" to see the discount reflected in your order total.',
            ],

        ];


        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
