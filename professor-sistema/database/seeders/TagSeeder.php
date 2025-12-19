<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        
        if (!$user) {
            return;
        }

        $tagsDefault = [
            ['nome' => 'Iniciante', 'cor' => '#10b981'],
            ['nome' => 'ENEM', 'cor' => '#f59e0b'],
            ['nome' => 'Reforço', 'cor' => '#ef4444'],
            ['nome' => 'Online', 'cor' => '#3b82f6'],
            ['nome' => 'Presencial', 'cor' => '#8b5cf6'],
            ['nome' => 'Avançado', 'cor' => '#06b6d4'],
            ['nome' => 'Vestibular', 'cor' => '#ec4899'],
        ];

        foreach ($tagsDefault as $tag) {
            \App\Models\Tag::create([
                'user_id' => $user->id,
                'nome' => $tag['nome'],
                'cor' => $tag['cor'],
            ]);
        }
    }
}
