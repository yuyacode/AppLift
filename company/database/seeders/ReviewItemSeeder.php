<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReviewItem;

class ReviewItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $items = [
            [
                'name' => '給与、年収',
                'created_at' => $now
            ],
            [
                'name' => '会社や職場の雰囲気',
                'created_at' => $now
            ],
            [
                'name' => '仕事の楽しさ、やりがい',
                'created_at' => $now
            ],
            [
                'name' => '会社や事業の成長性、将来性',
                'created_at' => $now
            ],
            [
                'name' => '待遇、福利厚生',
                'created_at' => $now
            ],
            [
                'name' => '研修,教育制度、成長環境',
                'created_at' => $now
            ],
            [
                'name' => '職場環境、法令遵守意識',
                'created_at' => $now
            ],
            [
                'name' => 'ワークライフバランス',
                'created_at' => $now
            ],
            [
                'name' => '風通しの良さ',
                'created_at' => $now
            ],
            [
                'name' => '入社理由',
                'created_at' => $now
            ],
            [
                'name' => '入社後に感じたギャップ',
                'created_at' => $now
            ],
            [
                'name' => '社員の相互尊重',
                'created_at' => $now
            ],
            [
                'name' => '評価について（制度、適性感）',
                'created_at' => $now
            ],
            [
                'name' => '課題、改善点',
                'created_at' => $now
            ],
            [
                'name' => 'その他',
                'created_at' => $now
            ],
        ];
        
        ReviewItem::insert($items);        
    }
}
