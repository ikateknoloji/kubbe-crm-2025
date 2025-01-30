<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderBasket;
use App\Models\OrderItem;
use App\Models\OrderLogo;
use App\Models\OrderPaymentReceipt;
use App\Models\InvoiceInfo;
use App\Models\User;
use App\Models\Role;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;


class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();


        $customerRole = Role::where('name', 'musteri')->first();
        if (!$customerRole) {
            $this->command->warn("Müşteri rolü bulunamadı. Lütfen 'musteri' rolünü oluşturun.");
            return;
        }

        $user = $customerRole->users()->inRandomOrder()->first();

        $orderCount = 5;

        collect(range(1, $orderCount))->each(function () use ($faker, $user) {

            $items = collect(range(1, rand(1, 3)))->map(function () use ($faker) {
                $quantity  = $faker->numberBetween(1, 5);
                $unitPrice = $faker->randomFloat(2, 900, max: 1000);
                $stockId   = rand(1, 10);
                
                $files = Storage::disk('public')->files('order_images');
                $images = [];
                
                if (!empty($files)) {
                    $selectedCount = rand(1, 2);
                    $images = [];
                    for ($i = 0; $i < $selectedCount; $i++) {
                        $randomPath = $faker->randomElement($files);
                        $fileName = basename($randomPath);
                        $url = config('app.url') . '/storage/order_images/' . $fileName;
                        $images[] = $url;
                    }
                } else {
                    $images = [];
                }
        
                return [
                    'stock_id'   => $stockId,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'image'      => $images,
                ];
            });
        
            $offerPrice = $items->reduce(function ($total, $item) {
                return $total + ($item['quantity'] * $item['unit_price']);
            }, 0);
        
            $orderCode = strtoupper(uniqid('SIPARIS_'));
        
            $order = Order::create([
                'order_name'  => $faker->sentence(3),
                'note'        => $faker->realText(100),
                'offer_price' => $offerPrice,
                'paid_amount' => $offerPrice,
                'customer_id' => $user->id,
                'order_code'  => $orderCode,
                'status'      => OrderStatus::OC,
            ]);

            $order->customerInfo()->create([
                'name'  => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
            ]);

            $items->each(function ($item) use ($order) {
        
                $orderBasket = $order->orderBaskets()->create();
        
                $orderBasket->orderItem()->create([
                    'stock_id'   => $item['stock_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
        
                $logos = collect($item['image'])->map(function ($logoUrl) {
                    return ['image' => $logoUrl];
                })->toArray();
        
                if (!empty($logos)) {
                    $orderBasket->orderLogos()->createMany($logos);
                }
            });
        
            $receiptFiles = Storage::disk('public')->files('dekont');
            if (!empty($receiptFiles)) {
                $randomReceiptPath = $faker->randomElement($receiptFiles);
                $fileName = basename($randomReceiptPath);
                $receiptUrl = config('app.url') . '/storage/dekont/' . $fileName;

                $order->paymentReceipt()->create([
                    'file_path' => $receiptUrl,
                ]);
            }
        
            if (rand(0, 1)) {
                $invoiceType = $faker->randomElement(['C', 'I']);
                $invoiceData = [
                    'invoice_type' => $invoiceType,
                    'company_name' => $invoiceType == 'I' ? $faker->company : null,
                    'name'         => $invoiceType == 'C' ? $faker->firstName : null,
                    'surname'      => $invoiceType == 'C' ? $faker->lastName : null,
                    'tc_number'    => $invoiceType == 'C' ? $faker->numerify('###########') : null,
                    'address'      => $faker->address,
                    'tax_office'   => $invoiceType == 'I' ? $faker->word : null,
                    'tax_number'   => $invoiceType == 'I' ? $faker->numerify('##########') : null,
                    'email'        => $faker->email,
                ];
                $order->invoiceInfo()->create($invoiceData);
            }

            $order->shippingAddress()->create([
                'full_name' => $faker->name,
                'address' => $faker->address,
                'city' => $faker->city,
                'district' => $faker->citySuffix,
                'country' => 'Türkiye',
                'phone' => $faker->phoneNumber,
            ]);
        });
    }
}
