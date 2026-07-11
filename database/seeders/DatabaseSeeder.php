<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Option;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'admin',
            'display_name' => 'Administrador',
            'email' => 'admin@wordmaster.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'editor',
            'display_name' => 'Editor',
            'email' => 'editor@wordmaster.test',
            'password' => Hash::make('password'),
            'role' => 'editor',
        ]);

        $cat1 = Category::create(['name' => 'Sin categoría', 'slug' => 'sin-categoria', 'description' => 'Entradas sin categoría asignada.']);
        $cat2 = Category::create(['name' => 'Noticias', 'slug' => 'noticias', 'description' => 'Noticias y novedades.']);
        $cat3 = Category::create(['name' => 'Tutoriales', 'slug' => 'tutoriales', 'description' => 'Tutoriales y guías.']);

        $tag1 = Tag::create(['name' => 'laravel', 'slug' => 'laravel']);
        $tag2 = Tag::create(['name' => 'php', 'slug' => 'php']);
        $tag3 = Tag::create(['name' => 'web', 'slug' => 'web']);

        $post = Post::create([
            'author_id' => $admin->id,
            'title' => '¡Bienvenido a WordMaster!',
            'slug' => 'bienvenido-a-wordmaster',
            'content' => '<h2>Tu CMS en Laravel</h2><p>WordMaster es un sistema de gestión de contenido construido con Laravel, inspirado en WordPress.</p><p>Este es un ejemplo de una entrada. Puedes editarla o eliminarla desde el panel de administración.</p>',
            'excerpt' => 'WordMaster es un sistema de gestión de contenido construido con Laravel.',
            'status' => 'published',
            'type' => 'post',
        ]);
        $post->categories()->attach([$cat2->id]);
        $post->tags()->attach([$tag1->id, $tag2->id]);

        $post2 = Post::create([
            'author_id' => $admin->id,
            'title' => 'Cómo crear contenido',
            'slug' => 'como-crear-contenido',
            'content' => '<p>Para crear contenido nuevo, ve al panel de administración y selecciona "Entradas" o "Páginas".</p><p>Desde allí podrás crear, editar y publicar tu contenido fácilmente.</p>',
            'excerpt' => 'Guía básica para crear contenido en WordMaster.',
            'status' => 'published',
            'type' => 'post',
        ]);
        $post2->categories()->attach([$cat3->id]);
        $post2->tags()->attach([$tag3->id]);

        Post::create([
            'author_id' => $admin->id,
            'title' => 'Acerca de',
            'slug' => 'acerca-de',
            'content' => '<h2>Sobre WordMaster</h2><p>WordMaster es un clon de WordPress construido con Laravel que ofrece las funcionalidades básicas de gestión de contenido y templates.</p><p>Incluye:</p><ul><li>Gestión de entradas y páginas</li><li>Categorías y tags</li><li>Biblioteca de medios</li><li>Menús de navegación</li><li>Sistema de templates personalizables</li><li>Gestión de usuarios con roles</li></ul>',
            'status' => 'published',
            'type' => 'page',
        ]);

        Option::create(['key' => 'site_title', 'value' => 'WordMaster']);
        Option::create(['key' => 'site_description', 'value' => 'Un CMS construido con Laravel']);
        Option::create(['key' => 'posts_per_page', 'value' => '10']);
        Option::create(['key' => 'site_keywords', 'value' => '']);

        Template::create([
            'name' => 'Página por defecto',
            'slug' => 'default-page',
            'description' => 'Template por defecto para páginas.',
            'content' => '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? \App\Models\Option::getValue(\'site_title\', \'WordMaster\') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-6">{{ $title }}</h1>
        <div class="prose">
            {!! $content !!}
        </div>
    </div>
</body>
</html>',
        ]);

        $this->call(ConvoyDeMexicoSeeder::class);
    }
}
