<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'phone' => $faker->phoneNumber,
        'bonus' => $faker->numberBetween(0, 1000),
        'isDealer' => $faker->boolean(20), // 20% chance of being a dealer
    ];
});

$factory->define(App\Products::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->words(1, true),
        'slug' => $faker->unique()->slug,
        'price' => $faker->numberBetween(100, 2000),
        'category_id' => $faker->numberBetween(1, 10),
        'discription' => $faker->sentence,
        'short' => $faker->sentence,
        'public' => $faker->boolean(80), // 80% chance of being public
        'erp_id' => $faker->unique()->randomNumber(6),
    ];
});

$factory->define(App\ProductCategory::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->words(1, true),
        'content' => $faker->paragraph,
    ];
});

$factory->define(App\Bill::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(App\User::class),
        'bill_id' => App\Bill::genMerchantTradeNo(),
        'user_name' => $faker->name,
        'item' => null,
        'bonus_use' => $faker->numberBetween(0, 50),
        'price' => $faker->numberBetween(500, 3000),
        'get_bonus' => $faker->numberBetween(10, 100),
        'ship_name' => $faker->name,
        'ship_gender' => $faker->numberBetween(0, 1), // 0 = male, 1 = female
        'ship_phone' => $faker->phoneNumber,
        'ship_county' => $faker->city,
        'ship_district' => $faker->streetName,
        'ship_address' => $faker->address,
        'ship_email' => $faker->email,
        'ship_arrive' => $faker->randomElement(['任何時間', '上午', '下午']),
        'ship_arriveDate' => $faker->date(),
        'ship_time' => $faker->randomElement(['13點前', '14-18點', '19點後']),
        'ship_receipt' => $faker->numberBetween(1, 3),
        'ship_three_id' => $faker->numerify('########'),
        'ship_three_company' => $faker->company,
        'ship_memo' => $faker->sentence,
        'pay_by' => $faker->randomElement(['CREDIT', 'ATM', '貨到付款']),
        'carrier_id' => $faker->numberBetween(0, 1),
        'status' => $faker->boolean(70), // 70% paid
        'shipment' => $faker->numberBetween(0, 3),
        'save_credit_card' => $faker->boolean(30),
        'used_credit_card_id' => null,
    ];
});

$factory->define(App\BillItem::class, function (Faker\Generator $faker) {
    return [
        'bill_id' => factory(App\Bill::class),
        'product_id' => factory(App\Products::class),
        'name' => $faker->words(3, true),
        'price' => $faker->numberBetween(100, 1000),
        'quantity' => $faker->numberBetween(1, 5),
        'short' => $faker->sentence,
        'description' => $faker->optional()->paragraph,
    ];
});

$factory->define(App\Kart::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(App\User::class),
        'product_id' => factory(App\Products::class),
    ];
});

$factory->define(App\UserCreditCard::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(App\User::class),
        'card_alias' => $faker->words(2, true),
        'masked_card_number' => $faker->numerify('****-****-****-####'),
        'card_holder_name' => $faker->name,
        'expiry_month' => $faker->numberBetween(1, 12),
        'expiry_year' => $faker->numberBetween(2025, 2030),
        'card_brand' => $faker->randomElement(['VISA', 'MASTERCARD', 'JCB']),
        'ecpay_member_id' => $faker->unique()->numerify('ECM########'),
        'is_default' => $faker->boolean(20),
        'is_active' => true,
    ];
});

$factory->define(App\Group::class, function (Faker\Generator $faker) {
    return [
        'dealer_id' => factory(App\User::class),
        'name' => $faker->words(3, true),
        'description' => $faker->paragraph,
        'min_amount' => $faker->numberBetween(5, 20),
        'deadline' => $faker->dateTimeBetween('now', '+30 days'),
        'isDone' => $faker->boolean(30),
    ];
});

$factory->define(App\GroupMember::class, function (Faker\Generator $faker) {
    return [
        'group_id' => factory(App\Group::class),
        'user_id' => factory(App\User::class),
        'name' => $faker->name,
        'phone' => $faker->phoneNumber,
        'email' => $faker->email,
    ];
});

$factory->define(App\Inventory::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->words(1, true),
        'slug' => $faker->unique()->slug(5),
        'category' => $faker->words(1, true)
    ];
});

$factory->define(App\FavoriteAddress::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(App\User::class),
        'name' => $faker->words(2, true),
        'recipient_name' => $faker->name,
        'phone' => $faker->phoneNumber,
        'county' => $faker->city,
        'district' => $faker->streetName,
        'address' => $faker->address,
        'isDefault' => $faker->boolean(20),
    ];
});

$factory->define(App\PaymentLog::class, function (Faker\Generator $faker) {
    return [
        'bill_id' => factory(App\Bill::class),
        'type' => $faker->randomElement([App\PaymentLog::TYPE_CREATE_PAYMENT, App\PaymentLog::TYPE_PAY_REQUEST]),
        'TransCode' => $faker->numberBetween(0, 1),
        'TransMsg' => $faker->sentence,
        'Data' => $faker->text,
    ];
});

$factory->define(App\FamilyStore::class, function (Faker\Generator $faker) {
    return [
        'bill_id' => factory(App\Bill::class),
        'number' => $faker->numerify('##########'),
        'name' => $faker->company,
        'address' => $faker->address,
    ];
});
