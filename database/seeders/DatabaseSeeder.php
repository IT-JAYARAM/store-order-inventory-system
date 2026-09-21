<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            ['name'=>'Wireless Mouse','code'=>'SKU-1001','price'=>799.00,'tax_percentage'=>18,'stock_on_hand'=>25,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Mechanical Keyboard','code'=>'SKU-1002','price'=>2499.00,'tax_percentage'=>18,'stock_on_hand'=>8,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'USB-C Cable','code'=>'SKU-1003','price'=>399.00,'tax_percentage'=>12,'stock_on_hand'=>3,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Laptop Stand','code'=>'SKU-1004','price'=>1499.00,'tax_percentage'=>18,'stock_on_hand'=>12,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Webcam','code'=>'SKU-1005','price'=>3299.00,'tax_percentage'=>18,'stock_on_hand'=>2,'created_at'=>now(),'updated_at'=>now()],
        ]);
        Customer::insert([
            ['name'=>'Arun Kumar','email'=>'arun@example.com','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Priya S','email'=>'priya@example.com','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Karthik R','email'=>'karthik@example.com','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}
