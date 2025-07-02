<?php

namespace Database\Seeders;

use App\Models\TermsCondition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermsConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TermsCondition::insert([
            [
                'title' => 'HOW WE USE YOUR INFORMATION',
                'description' => 'Thank you for visiting Dreambestcar.com Inc (the "Site")("Dreambestcar.com" "Us" or "We"). The Site also includes any version of this Site that is optimized or configured for use by mobile computing devices such as smartphones and tablets, as well as mobile applications and any other websites owned or operated by Us. These Terms and Conditions of Use ("Terms and Conditions") govern access to and use of the Site and services or products provided by Dreambestcar.com as made available on the Site or otherwise ("Services").',

            ],
            [
                'title' => 'SITE PROVISIONS AND SERVICES',
                'description' => 'We may collect both “non-personal” and “personal” information from you when you interact with our Site and Services.

                “Non-Personal Information” refers to information that may not by itself be reasonably associated with, linked to, or used to individually identify you or someone else. For example, general, non-specific information regarding your use of the Site and Services or derived from the information that you provide to us through the Site and Services.

                “Personal Information” refers to information that may be reasonably associated with, linked to, or used to individually identify you or allow you to be personally identified or contacted. For example, Personal Information may include information such as your name, email address, telephone number, address, either alone or in combination with other information.

                We may use the Non-Personal Information we collect and obtain for any lawful business purpose without any duty or obligation of accounting or otherwise to you, provided that the information remains Non-Personal Information. This may include, by way of example, developing products, services, and other offerings based on the Non-Personal Information and providing those offerings to other users and third parties. We may use the Personal Information we collect about you for a variety of purposes outlined in Dreambestcar.com Privacy Policy. Please note that, in most cases, the primary purposes for processing Personal Information are for the submission and transmittal of lead data and for powering targeted advertising and user experiences. Notably, Personal Information may also be used for additional, secondary purposes, which are provided in Dreambestcar.com Privacy Policy.

                Depending on where you reside, you may have the rights to access, correct, or delete your Personal Information, as well as opt out of the sale of your Personal Information and the processing of your Personal Information for targeted advertising. For more information about how Dreambestcar.com collects and processes Personal Information and how you may exercise these rights, please access the full Privacy Notice here.',

            ],
            [
                'title' => 'ACCESS AND USE',
                'description' => 'Dreambestcar.com operates the Site as an online advertising and research service for car buyers, sellers and enthusiasts. Dreambestcar.com does not sell vehicles directly and is never a party to any transaction between buyers and sellers. As a result, Dreambestcar.com does not (a) guarantee or ensure any vehicle or any transaction between a buyer and seller, (b) collect or process payment or transfer of title on behalf of buyers or sellers, or (c) warehouse, store, ship or deliver any vehicles.

                Advertisers on Dreambestcar.com may include information about special offers, incentives, or pricing programs associated with a specific brand, model, or vehicle ("Offers"). Dreambestcar.com is not responsible for the content of any such Offers, nor responsible for any errors or omissions in Offer contents or descriptions. You should contact the relevant advertiser for full details on any such Offers, including eligibility requirements, limitations and restrictions, and availability.

                By accessing the Site and Services, you agree that Dreambestcar.com is not responsible for any third party products and services information, whether such materials are accessed directly by you or used by Dreambestcar.com in providing the Services, including whether third party products and services information is accurate or whether the third party products and services information is suitable for your use or use in connection with the Services. You agree that Dreambestcar.com is not responsible for whether third party products and services information accessed by you is available for your use and for the performance or operation of any third party website.',

            ],

        ]);
    }
}
