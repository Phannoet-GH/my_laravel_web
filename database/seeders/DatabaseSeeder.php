<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Seed Users
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@eshop.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        $customerUser = User::updateOrCreate(
            ['email' => 'customer@eshop.com'],
            [
                'name' => 'David Kim',
                'password' => Hash::make('user123'),
                'role' => 'customer',
            ]
        );

        // 1. Categories
        $categories = [
            [
                'name' => 'Laptops & Workstations',
                'slug' => 'laptops-workstations',
                'icon' => 'laptop',
                'badge' => 'PRO TECH',
                'description' => 'High-performance rigs for software engineering, AI modeling, and dev ops.'
            ],
            [
                'name' => 'Smart Peripherals',
                'slug' => 'smart-peripherals',
                'icon' => 'keyboard',
                'badge' => 'CUSTOM MECH',
                'description' => 'Ergonomic mechanical keyboards, precision mice, and coding setups.'
            ],
            [
                'name' => 'Audio & Studio Tech',
                'slug' => 'audio-studio-tech',
                'icon' => 'headphones',
                'badge' => 'HI-FI',
                'description' => 'Active noise cancelling headphones, studio monitors, and podcasts mics.'
            ],
            [
                'name' => 'Displays & Vision',
                'slug' => 'displays-vision',
                'icon' => 'display',
                'badge' => '4K OLED',
                'description' => 'Ultra-wide productivity monitors and color-accurate display panels.'
            ],
            [
                'name' => 'Smart Wearables & IoT',
                'slug' => 'smart-wearables',
                'icon' => 'smartwatch',
                'badge' => 'NEURAL',
                'description' => 'Fitness trackers, smartwatches, and smart home automation hubs.'
            ],
        ];

        foreach ($categories as $catData) {
            Category::updateOrCreate(['slug' => $catData['slug']], $catData);
        }

        $laptopsCat = Category::where('slug', 'laptops-workstations')->first();
        $peripheralsCat = Category::where('slug', 'smart-peripherals')->first();
        $audioCat = Category::where('slug', 'audio-studio-tech')->first();
        $displaysCat = Category::where('slug', 'displays-vision')->first();
        $wearablesCat = Category::where('slug', 'smart-wearables')->first();

        // 2. Products
        $products = [
            [
                'category_id' => $laptopsCat->id,
                'name' => 'SE ProBook Cyber X 16"',
                'slug' => 'se-probook-cyber-x-16',
                'tagline' => 'Next-Gen M3 Ultra Architecture with 64GB Unified Memory',
                'description' => 'Engineered specifically for software engineers and full-stack developers. Features an ultra-bright Liquid Retina XDR display, 22-hour battery life, and silent vapor-chamber cooling.',
                'price' => 2499.99,
                'sale_price' => 2299.99,
                'stock' => 18,
                'rating' => 4.95,
                'review_count' => 38,
                'is_featured' => true,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Processor' => '16-Core Neural Engine CPU',
                    'RAM' => '64GB LPDDR5X',
                    'Storage' => '2TB NVMe PCIe 4.0 SSD',
                    'Display' => '16.2" Mini-LED 120Hz ProMotion',
                    'Weight' => '2.15 kg'
                ]
            ],
            [
                'category_id' => $peripheralsCat->id,
                'name' => 'CyberTactile 75% Wireless Mechanical Keyboard',
                'slug' => 'cybertactile-75-wireless-mechanical-keyboard',
                'tagline' => 'Gasket Mounted with Hot-Swappable Custom Lubricated Switches',
                'description' => 'The ultimate developer keyboard featuring custom PBT keycaps, programmable QMK/VIA key mappings, tri-mode connection (Bluetooth 5.2 / 2.4G / Type-C), and dynamic RGB backlighting.',
                'price' => 189.99,
                'sale_price' => 159.99,
                'stock' => 45,
                'rating' => 4.88,
                'review_count' => 64,
                'is_featured' => true,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Switch Type' => 'Linear Lubed Cream Switches',
                    'Keycaps' => 'Double-shot PBT Cherry Profile',
                    'Battery' => '4000mAh (Up to 200 hours)',
                    'Mounting' => 'Poron Gasket Mount Structure',
                    'Connectivity' => 'Bluetooth 5.2 / 2.4GHz Wireless / USB-C'
                ]
            ],
            [
                'category_id' => $audioCat->id,
                'name' => 'SE Quantum Acoustic ANC Studio Headphones',
                'slug' => 'se-quantum-acoustic-anc-headphones',
                'tagline' => 'Dual Hi-Res Drivers with Adaptive Neural Noise Cancellation',
                'description' => 'Block out all workplace distractions. Delivers spatial audio clarity for coding deep work, calls, and immersive music experience.',
                'price' => 349.99,
                'sale_price' => 299.99,
                'stock' => 25,
                'rating' => 4.91,
                'review_count' => 29,
                'is_featured' => true,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1484704849700-f032a568e944?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Driver' => '40mm Titanium Composite',
                    'ANC Level' => '-45dB Active Hybrid Noise Cancellation',
                    'Battery' => '50 Hours Playback',
                    'Microphone' => '6-Mic Beamforming Array',
                    'Codecs' => 'LDAC, AAC, SBC, aptX HD'
                ]
            ],
            [
                'category_id' => $displaysCat->id,
                'name' => 'Horizon Curve 49" Ultra-Wide OLED Monitor',
                'slug' => 'horizon-curve-49-ultrawide-oled-monitor',
                'tagline' => 'Dual-QHD 240Hz 0.03ms Curved Workspace Monster',
                'description' => 'Replace multi-monitor clutter with a single breathtaking 49-inch curved OLED canvas. Perfect for split-screen coding, IDE side-by-side view, and terminal logs.',
                'price' => 1299.99,
                'sale_price' => 1149.99,
                'stock' => 12,
                'rating' => 4.97,
                'review_count' => 42,
                'is_featured' => true,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1547082299-de196ea013d6?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Resolution' => '5120 x 1440 Dual QHD',
                    'Panel Type' => 'Quantum Dot OLED',
                    'Refresh Rate' => '240Hz',
                    'Curvature' => '1800R',
                    'Ports' => 'USB-C (90W PD), HDMI 2.1, DisplayPort 1.4'
                ]
            ],
            [
                'category_id' => $peripheralsCat->id,
                'name' => 'ErgoGlide Precision Master Mouse',
                'slug' => 'ergoglide-precision-master-mouse',
                'tagline' => '8K DPI Optical Sensor with MagSpeed Electromagnetic Scroll',
                'description' => 'Designed to protect wrists during long development sessions. Silent clicks, multi-device flow switching, and thumb gesture scroll wheel.',
                'price' => 119.99,
                'sale_price' => 99.99,
                'stock' => 60,
                'rating' => 4.79,
                'review_count' => 87,
                'is_featured' => false,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Sensor' => 'Darkfield 8000 DPI Laser Sensor',
                    'Buttons' => '7 Programmable Action Buttons',
                    'Battery' => '70 Days on full USB-C charge',
                    'Weight' => '141 grams'
                ]
            ],
            [
                'category_id' => $wearablesCat->id,
                'name' => 'SE Pulse Pro Smartwatch Ultra',
                'slug' => 'se-pulse-pro-smartwatch-ultra',
                'tagline' => 'Titanium Sapphire Glass with Standalone Cellular LTE',
                'description' => 'Tracks bio-metrics, heart rate variability (HRV), sleep recovery scores, and developer productivity focus metrics with ECG sensor.',
                'price' => 429.99,
                'sale_price' => 389.99,
                'stock' => 30,
                'rating' => 4.86,
                'review_count' => 19,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Case Material' => 'Aerospace Grade Titanium',
                    'Display' => '2.0" AMOLED 2000 nits Peak',
                    'Water Resistance' => '100 meters (10 ATM)',
                    'Sensors' => 'ECG, SpO2, Skin Temp, Dual GPS'
                ]
            ],
            [
                'category_id' => $laptopsCat->id,
                'name' => 'SE DevDeck Pocket AI Mini Workstation',
                'slug' => 'se-devdeck-pocket-ai-mini-workstation',
                'tagline' => 'Compact ARM64 AI Inference Rig with 32GB RAM',
                'description' => 'Host local LLMs, test Docker microservices, and run CI/CD pipelines locally in a whisper-quiet mini aluminum enclosure.',
                'price' => 799.99,
                'sale_price' => 699.99,
                'stock' => 15,
                'rating' => 4.90,
                'review_count' => 22,
                'is_featured' => false,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1593642632823-8f785ba67e45?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'NPU Accelerator' => '45 TOPS Neural Processor Unit',
                    'RAM' => '32GB LPDDR5',
                    'Storage' => '1TB M.2 PCIe Gen4 NVMe SSD',
                    'OS' => 'Ubuntu Developer Suite / macOS Compatible'
                ]
            ],
            [
                'category_id' => $audioCat->id,
                'name' => 'SE SoundPod Wireless Earbuds Pro',
                'slug' => 'se-soundpod-wireless-earbuds-pro',
                'tagline' => 'Active Noise Cancellation with Transparency Mode',
                'description' => 'Compact wireless earbuds with deep bass response, touch controls, and sweatproof IPX7 rating for work and gym.',
                'price' => 149.99,
                'sale_price' => 129.99,
                'stock' => 50,
                'rating' => 4.82,
                'review_count' => 51,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Playtime' => '36 Hours total with MagSafe charging case',
                    'Water Resistance' => 'IPX7 Certified',
                    'Microphone' => 'Quad AI Mic Clear Call technology'
                ]
            ],
            // 20 NEW PRODUCTS
            [
                'category_id' => $laptopsCat->id,
                'name' => 'SE TitanStation Max Studio 18"',
                'slug' => 'se-titanstation-max-studio-18',
                'tagline' => 'Dual RTX 4090 Mobile GPUs & 128GB DDR5 ECC RAM',
                'description' => 'Unrestricted extreme computational power for local AI LLM training, 3D rendering, and massive software compilation tasks.',
                'price' => 3899.99,
                'sale_price' => 3599.99,
                'stock' => 10,
                'rating' => 4.98,
                'review_count' => 14,
                'is_featured' => true,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'CPU' => 'Intel Core i9-14900HX 24-Core',
                    'GPU' => 'Dual NVIDIA RTX 4090 16GB',
                    'RAM' => '128GB DDR5 ECC',
                    'Storage' => '4TB RAID-0 NVMe SSD'
                ]
            ],
            [
                'category_id' => $laptopsCat->id,
                'name' => 'SE Blade UltraBook Slim 14"',
                'slug' => 'se-blade-ultrabook-slim-14',
                'tagline' => 'Featherweight 0.99kg Carbon Fiber Chassis with Intel Core Ultra 9',
                'description' => 'Designed for nomadic developers and software architects on the go. Superb battery efficiency and tactile keyboard.',
                'price' => 1799.99,
                'sale_price' => 1599.99,
                'stock' => 22,
                'rating' => 4.85,
                'review_count' => 27,
                'is_featured' => false,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Weight' => '0.99 kg',
                    'Processor' => 'Intel Core Ultra 9 185H',
                    'Battery' => '75Wh (Up to 18 hours)',
                    'Display' => '14" 2.8K 120Hz OLED'
                ]
            ],
            [
                'category_id' => $laptopsCat->id,
                'name' => 'SE CloudNode Rackmount Edge Server',
                'slug' => 'se-cloudnode-rackmount-edge-server',
                'tagline' => '64-Core AMD EPYC Processor for On-Premises K8s Clusters',
                'description' => 'Enterprise-grade micro edge server enclosure designed for localized homelab clusters and corporate test nodes.',
                'price' => 4999.99,
                'sale_price' => null,
                'stock' => 5,
                'rating' => 4.92,
                'review_count' => 8,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'CPU' => 'AMD EPYC 9554 64-Core',
                    'Networking' => 'Dual 10GbE SFP+ Ports',
                    'Form Factor' => '1U Compact Rackmount'
                ]
            ],
            [
                'category_id' => $laptopsCat->id,
                'name' => 'SE CodePad Foldable Dual-Screen Laptop 16"',
                'slug' => 'se-codepad-foldable-dualscreen-laptop',
                'tagline' => 'Dual 3K OLED Screens with Haptic Keyboard Surface',
                'description' => 'Revolutionary dual-screen setup allowing vertical IDE code view on upper panel and documentation/terminal on lower panel.',
                'price' => 2799.99,
                'sale_price' => 2499.99,
                'stock' => 14,
                'rating' => 4.89,
                'review_count' => 19,
                'is_featured' => true,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Displays' => '2x 16" 3K Touch OLEDs',
                    'RAM' => '32GB LPDDR5X',
                    'Stylus Support' => 'Active Pen Included'
                ]
            ],
            [
                'category_id' => $peripheralsCat->id,
                'name' => 'ApexType Split Ergonomic Alice Keyboard',
                'slug' => 'apextype-split-ergonomic-alice-keyboard',
                'tagline' => 'Split CNC Aluminum Body with Integrated Trackball & Palm Rests',
                'description' => 'Ergonomically engineered for posture alignment and zero RSI wrist strain. Features VIA support and hot-swappable switches.',
                'price' => 299.99,
                'sale_price' => 269.99,
                'stock' => 35,
                'rating' => 4.94,
                'review_count' => 48,
                'is_featured' => true,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Layout' => 'Split Alice 68-Key',
                    'Body' => 'Anodized CNC Aluminum',
                    'Plate' => 'Brass Gasket Mounted'
                ]
            ],
            [
                'category_id' => $peripheralsCat->id,
                'name' => 'MagDesk Ultra Leather Desk Mat with Qi2 Charging',
                'slug' => 'magdesk-ultra-leather-desk-mat',
                'tagline' => 'Premium Italian Leather with Dual 15W Wireless Charging Zones',
                'description' => 'Elevate your desk aesthetics with waterproof top-grain leather, magnetic cable management clips, and fast wireless phone charging.',
                'price' => 89.99,
                'sale_price' => 69.99,
                'stock' => 100,
                'rating' => 4.76,
                'review_count' => 73,
                'is_featured' => false,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Dimensions' => '900mm x 400mm x 4mm',
                    'Material' => 'Vegetable-Tanned Italian Leather',
                    'Charging' => 'Dual 15W Qi2 Fast Charge'
                ]
            ],
            [
                'category_id' => $peripheralsCat->id,
                'name' => 'CyberDial Programmable Productivity Console',
                'slug' => 'cyberdial-programmable-productivity-console',
                'tagline' => '12 Haptic Rotary Dials & OLED Keys for Figma, VSCode & Premiere',
                'description' => 'Speed up coding workflows, macro triggering, brush resizing, and volume mixing with dedicated tactile hardware dials.',
                'price' => 219.99,
                'sale_price' => 189.99,
                'stock' => 40,
                'rating' => 4.87,
                'review_count' => 31,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1629654297299-c8506221ca97?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1629654297299-c8506221ca97?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Dials' => '4 Rotary Encoders with Click Push',
                    'Keys' => '6 Customizable Color OLED Displays',
                    'Software' => 'Cross-platform macOS & Windows App'
                ]
            ],
            [
                'category_id' => $peripheralsCat->id,
                'name' => 'ZeroG Precision Trackball Master',
                'slug' => 'zerog-precision-trackball-master',
                'tagline' => '55mm Optical Glass Ball with Adjustable 0-20 Degree Tilt Angle',
                'description' => 'Smooth sub-millimeter cursor accuracy without moving your arm. Ideal for CAD designers, software engineers, and audio editors.',
                'price' => 139.99,
                'sale_price' => 119.99,
                'stock' => 28,
                'rating' => 4.81,
                'review_count' => 22,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Trackball' => '55mm Ruby Bearing Optical',
                    'Connectivity' => 'Bluetooth 5.3 + 2.4GHz Dongle',
                    'Battery' => 'Up to 120 Days'
                ]
            ],
            [
                'category_id' => $audioCat->id,
                'name' => 'SE StudioMic Pro XLR Condenser Microphone',
                'slug' => 'se-studiomic-pro-xlr-condenser',
                'tagline' => 'Broadcast-Grade 34mm Gold-Sputtered Capsule for Podcasting & Calls',
                'description' => 'Delivers warm, rich radio voice tone. Ultra-low self-noise (5dB-A) makes it perfect for voiceovers, streaming, and meetings.',
                'price' => 249.99,
                'sale_price' => 219.99,
                'stock' => 32,
                'rating' => 4.93,
                'review_count' => 54,
                'is_featured' => true,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1590602847861-f357a9332bbc?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Capsule' => '34mm True Condenser',
                    'Polar Pattern' => 'Cardioid',
                    'Frequency Range' => '20Hz - 20kHz',
                    'Output' => '3-Pin Balanced XLR'
                ]
            ],
            [
                'category_id' => $audioCat->id,
                'name' => 'SE WaveMonitor Active Nearfield Speakers',
                'slug' => 'se-wavemonitor-active-nearfield-speakers',
                'tagline' => 'Bi-Amplified Studio Monitors with DSP Room Correction',
                'description' => 'Flat frequency response studio monitors with Kevlar woofers and silk dome tweeters for pristine music listening and editing.',
                'price' => 499.99,
                'sale_price' => 449.99,
                'stock' => 18,
                'rating' => 4.88,
                'review_count' => 16,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1545454675-3531b543be5d?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Woofer' => '5" Woven Kevlar Driver',
                    'Power' => '140W Class-D Amplification',
                    'Inputs' => 'XLR, TRS 1/4", RCA, Bluetooth 5.0'
                ]
            ],
            [
                'category_id' => $audioCat->id,
                'name' => 'AudioDeck USB-C High-Res DAC & Headphone Amp',
                'slug' => 'audiodeck-usbc-highres-dac-amp',
                'tagline' => 'Dual ESS SABRE 9038 DAC Chips supporting 32-bit/768kHz PCM',
                'description' => 'Audiophile desktop DAC delivering crystalline sound clarity and driving high-impedance studio headphones with ease.',
                'price' => 199.99,
                'sale_price' => 169.99,
                'stock' => 45,
                'rating' => 4.90,
                'review_count' => 36,
                'is_featured' => false,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1558089687-f282ffcbc126?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1558089687-f282ffcbc126?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'DAC Chip' => 'Dual ESS SABRE ES9038Q2M',
                    'Outputs' => '4.4mm Balanced + 6.35mm Single-Ended',
                    'SNR' => '125dB'
                ]
            ],
            [
                'category_id' => $audioCat->id,
                'name' => 'SE VoiceShield Acoustic Isolation Booth Filter',
                'slug' => 'se-voiceshield-acoustic-isolation-filter',
                'tagline' => 'High-Density Studio Foam Mic Shield for Crisp Audio Recording',
                'description' => 'Eliminate room echo, reverb, and ambient fan noise during voice recordings, podcasting, and remote coding demos.',
                'price' => 79.99,
                'sale_price' => 59.99,
                'stock' => 55,
                'rating' => 4.70,
                'review_count' => 41,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Panels' => '5-Foldable Aluminum Panels',
                    'Foam' => 'High-Density Pyramidal Acoustic Foam'
                ]
            ],
            [
                'category_id' => $displaysCat->id,
                'name' => 'VisionStudio 32" 6K Color-Accurate Retina Display',
                'slug' => 'visionstudio-32-6k-color-accurate-display',
                'tagline' => '100% DCI-P3 & AdobeRGB Calibrated Panel with Thunderbolt 4 Hub',
                'description' => 'Razor-sharp 6016 x 3384 resolution with Nano-texture glass. Ideal for UI/UX designers, frontend developers, and video editors.',
                'price' => 1899.99,
                'sale_price' => 1699.99,
                'stock' => 8,
                'rating' => 4.96,
                'review_count' => 25,
                'is_featured' => true,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1585792180666-f7347c490ee2?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1585792180666-f7347c490ee2?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Resolution' => '6K 6016 x 3384 at 218 ppi',
                    'Brightness' => '1000 nits Sustained Full Screen',
                    'Hub' => 'Thunderbolt 4 (96W Host Charging) + 3x USB-C'
                ]
            ],
            [
                'category_id' => $displaysCat->id,
                'name' => 'LuminaLight Pro Monitor Light Bar with Auto-Dimming',
                'slug' => 'luminalight-pro-monitor-light-bar',
                'tagline' => 'Asymmetric Optical Illuminator with Wireless Desktop Control Dial',
                'description' => 'Zero screen glare desk lamp designed to reduce eye fatigue during late-night coding sessions. Built-in ambient light sensor.',
                'price' => 129.99,
                'sale_price' => 99.99,
                'stock' => 70,
                'rating' => 4.84,
                'review_count' => 89,
                'is_featured' => false,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1517059224940-d4af9eec41b7?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1517059224940-d4af9eec41b7?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'CRI' => 'Ra > 97 High Color Rendering',
                    'Color Temp' => '2700K - 6500K Adjustable',
                    'Controller' => '2.4G Wireless Rotary Knob'
                ]
            ],
            [
                'category_id' => $displaysCat->id,
                'name' => 'FlexiArm Heavy-Duty Dual Monitor Gas Spring Mount',
                'slug' => 'flexiarm-heavyduty-dual-monitor-mount',
                'tagline' => 'Supports up to 20kg per arm with Integrated Cable Channels',
                'description' => 'Heavy-duty steel monitor arm setup supporting dual 32" screens with full 360-degree rotation, tilt, and height adjustment.',
                'price' => 159.99,
                'sale_price' => 129.99,
                'stock' => 40,
                'rating' => 4.78,
                'review_count' => 34,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1593640408182-31c70c8268f5?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'VESA' => '75x75mm / 100x100mm Quick Release',
                    'Max Weight' => '20kg per arm',
                    'Desk Clamp' => 'C-Clamp & Grommet Base'
                ]
            ],
            [
                'category_id' => $displaysCat->id,
                'name' => 'SE PortView 16" 4K Portable Touchscreen Monitor',
                'slug' => 'se-portview-16-4k-portable-touchscreen',
                'tagline' => 'Lightweight Secondary Display for Laptops with 10-point Capacitive Touch',
                'description' => 'Plug-and-play USB-C portable display for multi-screen productivity anywhere. Includes magnetic kickstand case.',
                'price' => 399.99,
                'sale_price' => 349.99,
                'stock' => 25,
                'rating' => 4.83,
                'review_count' => 17,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1547082299-de196ea013d6?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1547082299-de196ea013d6?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Resolution' => '3840 x 2160 4K UHD',
                    'Weight' => '680 grams',
                    'Ports' => 'Dual Full-Featured USB-C + Mini HDMI'
                ]
            ],
            [
                'category_id' => $wearablesCat->id,
                'name' => 'NeuralRing Gen-3 Bio-Recovery Fitness Tracker',
                'slug' => 'neuralring-gen3-bio-recovery-tracker',
                'tagline' => 'Titanium Smart Ring for 24/7 HRV, Sleep Phase & Stress Tracking',
                'description' => 'Ultra-discrete titanium smart ring that measures body temperature, pulse oximetry, and focus scores without notification distractions.',
                'price' => 299.99,
                'sale_price' => 259.99,
                'stock' => 50,
                'rating' => 4.89,
                'review_count' => 42,
                'is_featured' => true,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1605100804763-247f67b3557e?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Material' => 'Aerospace Grade Titanium with PVD Coating',
                    'Battery' => '7 Days Battery Life',
                    'Waterproof' => '100m Depth Rated'
                ]
            ],
            [
                'category_id' => $wearablesCat->id,
                'name' => 'SE DeskStation Smart IoT Power Hub & Energy Meter',
                'slug' => 'se-deskstation-smart-iot-power-hub',
                'tagline' => '140W GaN Fast Charger with Digital Wattage Display & Wi-Fi Telemetry',
                'description' => 'Monitor real-time energy consumption of your laptop, monitor, and peripherals while charging up to 5 devices simultaneously.',
                'price' => 149.99,
                'sale_price' => 119.99,
                'stock' => 65,
                'rating' => 4.82,
                'review_count' => 60,
                'is_featured' => false,
                'is_trending' => true,
                'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Output' => '140W Total Power (PD 3.1 USB-C)',
                    'Display' => '1.4" Color IPS Screen (Watts, Volts, Amps)',
                    'Ports' => '3x USB-C + 2x USB-A'
                ]
            ],
            [
                'category_id' => $wearablesCat->id,
                'name' => 'CyberGlow RGB Ambient Light Bar Pair with Matter Standard',
                'slug' => 'cyberglow-rgb-ambient-light-bar-pair',
                'tagline' => 'Syncs with Screen Colors & Music Beats via Apple Home / Google Home',
                'description' => 'Transform your desk workspace into an immersive lighting haven with 16 million colors and smart voice assistant control.',
                'price' => 119.99,
                'sale_price' => 89.99,
                'stock' => 80,
                'rating' => 4.75,
                'review_count' => 52,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Protocol' => 'Matter over Wi-Fi / Thread',
                    'LEDs' => 'Addressable RGBIC Light Bars',
                    'Power' => 'USB 5V/3A'
                ]
            ],
            [
                'category_id' => $wearablesCat->id,
                'name' => 'SE AirSense Desktop Indoor Air Quality Monitor',
                'slug' => 'se-airsense-desktop-air-quality-monitor',
                'tagline' => 'Real-time CO2, PM2.5, VOCs & Temperature Sensor with OLED Screen',
                'description' => 'Know when to ventilate your home office room. High CO2 levels drop concentration—stay sharp with instant air alerts.',
                'price' => 169.99,
                'sale_price' => 139.99,
                'stock' => 30,
                'rating' => 4.86,
                'review_count' => 28,
                'is_featured' => false,
                'is_trending' => false,
                'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1000&q=80'
                ],
                'specs' => [
                    'Sensors' => 'NDIR CO2 Sensor, Laser PM2.5, TVOC, Humidity',
                    'Screen' => 'OLED High Contrast Display',
                    'App Sync' => 'iOS & Android Home Metrics App'
                ]
            ]
        ];

        foreach ($products as $pData) {
            $product = Product::updateOrCreate(['slug' => $pData['slug']], $pData);

            // Add reviews for each product
            Review::firstOrCreate([
                'product_id' => $product->id,
                'author_name' => 'Alex Rivera (Senior Tech Lead)',
            ], [
                'rating' => 5,
                'headline' => 'Absolutly essential for my daily workflow!',
                'comment' => 'Bought this for my engineering workstation setup at SE Shop. Build quality exceeds expectations and delivery was lightning fast.'
            ]);

            Review::firstOrCreate([
                'product_id' => $product->id,
                'author_name' => 'Samantha Lin (Full-Stack Engineer)',
            ], [
                'rating' => 5,
                'headline' => 'Best purchase of the year',
                'comment' => 'The build quality and thermal management are incredible. Highly recommend SE Shop for tech gear!'
            ]);
        }

        // 3. Sample Orders
        $order1 = Order::updateOrCreate(
            ['order_number' => 'SE-ORD-894102'],
            [
                'user_id'          => $customerUser->id,
                'customer_name'    => 'David Kim',
                'customer_email'   => 'customer@eshop.com',
                'customer_phone'   => '+1 (555) 234-5678',
                'shipping_address' => '742 Silicon Valley Ave, Suite 300',
                'city'             => 'San Francisco',
                'postal_code'      => '94107',
                'subtotal_amount'  => 2459.98,
                'discount_amount'  => 0.00,
                'coupon_code'      => null,
                'tax_amount'       => 122.99,
                'total_amount'     => 2582.97,
                'payment_method'   => 'card',
                'status'           => 'shipped',
                'tracking_code'    => 'TRK-SE-998234',
            ]);

        $p1 = Product::where('slug', 'se-probook-cyber-x-16')->first();
        $p2 = Product::where('slug', 'cybertactile-75-wireless-mechanical-keyboard')->first();

        if ($p1) {
            OrderItem::updateOrCreate(
                ['order_id' => $order1->id, 'product_id' => $p1->id],
                [
                    'product_name' => $p1->name,
                    'unit_price' => $p1->sale_price ?? $p1->price,
                    'quantity' => 1,
                    'subtotal' => $p1->sale_price ?? $p1->price,
                ]
            );
        }

        if ($p2) {
            OrderItem::updateOrCreate(
                ['order_id' => $order1->id, 'product_id' => $p2->id],
                [
                    'product_name' => $p2->name,
                    'unit_price' => $p2->sale_price ?? $p2->price,
                    'quantity' => 1,
                    'subtotal' => $p2->sale_price ?? $p2->price,
                ]
            );
        }
    }
}
