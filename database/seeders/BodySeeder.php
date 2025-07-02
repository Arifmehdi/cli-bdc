<?php

namespace Database\Seeders;

use App\Models\VehicleBody;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BodySeeder extends Seeder
{
    /**`, ``, ``, `
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = Carbon::now();
        VehicleBody::insert([
            [
                'name' => 'Truck',
                'slug' => 'truck',
                'image' => 'body_images/ECENX124AeyKzy6pJtSb9AFQvVOzI1N9klL30jJ0.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Sedan',
                'slug' => 'sedan',
                'image' => 'body_images/4mdzoEF1Bad9UPKZYvq4Bnkbxt1bXNQV3DkmbpwB.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'SUV',
                'slug' => 'suv',
                'image' => 'body_images/5H9FuxNQpO5WfJNCKxTIN7U1OVi23LX1DVkAnPnK.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Convertible',
                'slug' => 'convertible',
                'image' => 'body_images/XmyRXan8mtU9EOySjLPWff5ElqzV9OuFG7VF0E1C.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Coupe',
                'slug' => 'coupe',
                'image' => 'body_images/nVxiDhMshQYqEGu1mXBaOm1kcdqBzfwY3S02uMwQ.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Van',
                'slug' => 'van',
                'image' => 'body_images/Egoh7RtMdOl9lDD4C27eMQq1GXh9vLlgpkU7SpF8.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Wagon',
                'slug' => 'wagon',
                'image' => 'body_images/qU0uM32mVKHdXuKTMCxA9Yjk2WXk70eI0gB8mxX5.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Hatchback',
                'slug' => 'hatchback',
                'image' => 'body_images/Xqv8W8wl36t7lNfZE21bj1d2CqnaWRRbYf6Qx9UQ.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Hybrid',
                'slug' => 'hybrid',
                'image' => 'body_images/JhtkHZ4G3CP54htsZ0aqOxPAgnCRoYCaCS80Thil.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'name' => 'Electric',
                'slug' => 'electric',
                'image' => 'body_images/oEGKuE7NHgDXxTCQRgjCdbAaq9k40elmBVUEJ9LX.svg',
                'status' => '1',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }
}