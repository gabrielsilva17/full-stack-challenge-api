<?php

namespace App\Notifications;

class Mesage
{
    public string $para;
    public ?string $nome       = null;
    public string $template;
    public string $assunto;
    public array  $dados        = [];
    public ?string $link        = null;
}
