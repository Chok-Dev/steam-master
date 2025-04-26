<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /* User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
        ]);

        // สร้างผู้ขาย
        $seller = User::create([
            'name' => 'Seller User',
            'email' => 'seller@example.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'bio' => 'ผู้ขายรหัสเกมและบัญชี Steam คุณภาพ รับประกันสินค้าทุกชิ้น',
            'is_verified' => true,
            'balance' => 5000.00,
        ]);

        // สร้างผู้ซื้อ
        $buyer = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'is_verified' => true,
            'balance' => 2000.00,
        ]); */

        // ข้อมูลหมวดหมู่
        $categories = [
            ['name' => 'Action', 'slug' => 'action', 'description' => 'เกมแอคชั่น'],
            ['name' => 'RPG', 'slug' => 'rpg', 'description' => 'เกมบทบาทสมมติ'],
            ['name' => 'FPS', 'slug' => 'fps', 'description' => 'เกมยิงมุมมองบุคคลที่หนึ่ง'],
            ['name' => 'Strategy', 'slug' => 'strategy', 'description' => 'เกมวางแผนและกลยุทธ์'],
            ['name' => 'Adventure', 'slug' => 'adventure', 'description' => 'เกมผจญภัย'],
            ['name' => 'Simulation', 'slug' => 'simulation', 'description' => 'เกมจำลองสถานการณ์'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'เกมกีฬา'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // สร้างข้อมูลสินค้า
        /* $products = [
            [
                'user_id' => $seller->id,
                'category_id' => 1, // Action
                'name' => 'Grand Theft Auto V',
                'slug' => 'grand-theft-auto-v',
                'description' => 'รหัสเกม GTA V สำหรับ Steam เล่นได้ทันทีหลังจากได้รับรหัส',
                'price' => 799.00,
                'type' => 'steam_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'Steam',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                ]),
                'views' => rand(10, 100),
            ],
            [
                'user_id' => $seller->id,
                'category_id' => 2, // RPG
                'name' => 'The Witcher 3: Wild Hunt',
                'slug' => 'the-witcher-3-wild-hunt',
                'description' => 'รหัสเกม The Witcher 3 สำหรับ Steam พร้อม DLC ทั้งหมด',
                'price' => 599.00,
                'type' => 'steam_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'Steam',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                    'dlc' => 'Includes all DLCs',
                ]),
                'views' => rand(10, 100),
            ],
            [
                'user_id' => $seller->id,
                'category_id' => 3, // FPS
                'name' => 'Counter-Strike 2',
                'slug' => 'counter-strike-2',
                'description' => 'รหัสเกม CS2 สำหรับ Steam พร้อมไอเทมพิเศษเริ่มต้น',
                'price' => 450.00,
                'type' => 'steam_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'Steam',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                ]),
                'views' => rand(10, 100),
            ],
            [
                'user_id' => $seller->id,
                'category_id' => 4, // Strategy
                'name' => 'Civilization VI',
                'slug' => 'civilization-vi',
                'description' => 'รหัสเกม Civilization VI สำหรับ Steam',
                'price' => 650.00,
                'type' => 'steam_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'Steam',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                ]),
                'views' => rand(10, 100),
            ],
            [
                'user_id' => $seller->id,
                'category_id' => 5, // Adventure
                'name' => 'Red Dead Redemption 2',
                'slug' => 'red-dead-redemption-2',
                'description' => 'รหัสเกม Red Dead Redemption 2 สำหรับ Steam',
                'price' => 1290.00,
                'type' => 'steam_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'Steam',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                ]),
                'views' => rand(10, 100),
            ],
            [
                'user_id' => $seller->id,
                'category_id' => 6, // Simulation
                'name' => 'The Sims 4',
                'slug' => 'the-sims-4',
                'description' => 'รหัสเกม The Sims 4 สำหรับ Origin พร้อม Expansion Pack',
                'price' => 890.00,
                'type' => 'origin_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'Origin',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                    'dlc' => 'Includes 2 Expansion Packs',
                ]),
                'views' => rand(10, 100),
            ],
            [
                'user_id' => $seller->id,
                'category_id' => 7, // Sports
                'name' => 'FIFA 24',
                'slug' => 'fifa-24',
                'description' => 'รหัสเกม FIFA 24 สำหรับ Origin',
                'price' => 1590.00,
                'type' => 'origin_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'Origin',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                ]),
                'views' => rand(10, 100),
            ],
            [
                'user_id' => $seller->id,
                'category_id' => 1, // Action
                'name' => 'Cyberpunk 2077',
                'slug' => 'cyberpunk-2077',
                'description' => 'รหัสเกม Cyberpunk 2077 สำหรับ GOG',
                'price' => 1290.00,
                'type' => 'gog_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'GOG',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                ]),
                'views' => rand(10, 100),
            ],
            [
                'user_id' => $seller->id,
                'category_id' => 2, // RPG
                'name' => 'Elden Ring',
                'slug' => 'elden-ring',
                'description' => 'รหัสเกม Elden Ring สำหรับ Steam',
                'price' => 1690.00,
                'type' => 'steam_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'Steam',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                ]),
                'views' => rand(10, 100),
            ],
            [
                'user_id' => $seller->id,
                'category_id' => 3, // FPS
                'name' => 'Call of Duty: Modern Warfare III',
                'slug' => 'call-of-duty-modern-warfare-3',
                'description' => 'รหัสเกม COD: MW3 สำหรับ Battle.net',
                'price' => 1990.00,
                'type' => 'battlenet_key',
                'status' => 'available',
                'attributes' => json_encode([
                    'platform' => 'Battle.net',
                    'region' => 'Global',
                    'language' => 'Multi-language',
                ]),
                'views' => rand(10, 100),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // สร้างออเดอร์และการซื้อสินค้า
        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-' . Str::random(10),
            'total_amount' => 599.00,
            'status' => 'completed',
        ]);

        // ซื้อ The Witcher 3
        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => 2, // The Witcher 3
            'price' => 599.00,
            'status' => 'delivered',
            'key_data' => 'XXXX-YYYY-ZZZZ-AAAA', // รหัสเกมที่ส่งมอบ (ในระบบจริงจะต้องเข้ารหัส)
            'delivered_at' => now()->subDays(5),
        ]);

        // บันทึกธุรกรรม
        Transaction::create([
            'order_id' => $order->id,
            'user_id' => $buyer->id,
            'transaction_id' => 'TRX-' . Str::random(10),
            'amount' => 599.00,
            'type' => 'payment',
            'status' => 'successful',
            'payment_details' => json_encode(['method' => 'credit_card']),
        ]);

        Transaction::create([
            'order_id' => $order->id,
            'user_id' => $seller->id,
            'transaction_id' => 'TRX-' . Str::random(10),
            'amount' => 569.05, // หลังหักค่าคอมมิชชั่น 5%
            'type' => 'payout',
            'status' => 'successful',
        ]);

        // บันทึกรีวิว
        Review::create([
            'user_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'ส่งของเร็วมาก รหัสใช้งานได้ปกติ ขอบคุณครับ',
        ]);

        // สร้างออเดอร์ที่รอส่งมอบ
        $pendingOrder = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-' . Str::random(10),
            'total_amount' => 799.00,
            'status' => 'processing',
        ]);

        // ซื้อ GTA V
        $pendingOrderItem = OrderItem::create([
            'order_id' => $pendingOrder->id,
            'product_id' => 1, // GTA V
            'price' => 799.00,
            'status' => 'pending',
        ]);

        // บันทึกธุรกรรม
        Transaction::create([
            'order_id' => $pendingOrder->id,
            'user_id' => $buyer->id,
            'transaction_id' => 'TRX-' . Str::random(10),
            'amount' => 799.00,
            'type' => 'payment',
            'status' => 'successful',
            'payment_details' => json_encode(['method' => 'wallet']),
        ]);

        // สร้างข้อมูลสำหรับกราฟและสถิติ
        // เพิ่มข้อมูลย้อนหลัง 7 วัน
        for ($i = 7; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            $randomOrder = Order::create([
                'user_id' => $buyer->id,
                'order_number' => 'ORD-' . Str::random(10),
                'total_amount' => rand(300, 2000),
                'status' => 'completed',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            
            // สุ่มสินค้าในออเดอร์
            $productId = rand(1, 10);
            if ($productId > 9) $productId = 9; // ป้องกัน index เกิน
            
            $product = Product::find($productId);
            
            $orderItem = OrderItem::create([
                'order_id' => $randomOrder->id,
                'product_id' => $productId,
                'price' => $product ? $product->price : rand(300, 2000),
                'status' => 'delivered',
                'key_data' => 'XXXX-YYYY-ZZZZ-' . Str::random(4),
                'delivered_at' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            
            // บันทึกธุรกรรม
            Transaction::create([
                'order_id' => $randomOrder->id,
                'user_id' => $buyer->id,
                'transaction_id' => 'TRX-' . Str::random(10),
                'amount' => $randomOrder->total_amount,
                'type' => 'payment',
                'status' => 'successful',
                'payment_details' => json_encode(['method' => rand(0, 1) ? 'credit_card' : 'wallet']),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            
            Transaction::create([
                'order_id' => $randomOrder->id,
                'user_id' => $seller->id,
                'transaction_id' => 'TRX-' . Str::random(10),
                'amount' => $randomOrder->total_amount * 0.95,
                'type' => 'payout',
                'status' => 'successful',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
            
            // สร้างรีวิว
            if (rand(0, 1)) {
                Review::create([
                    'user_id' => $buyer->id,
                    'seller_id' => $seller->id,
                    'order_id' => $randomOrder->id,
                    'rating' => rand(3, 5),
                    'comment' => 'รีวิวการสั่งซื้อ ให้ ' . rand(3, 5) . ' ดาว',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        } */
    }
}
