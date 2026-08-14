<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_presents_the_route_creation_experience(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Como você quer')
            ->assertSee('Criar minha rota')
            ->assertSee('Descubra no seu ritmo');
    }
}
