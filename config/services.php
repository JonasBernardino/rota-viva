<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services
    | such as Mailgun, Postmark, AWS and others.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env(
            'AWS_DEFAULT_REGION',
            'us-east-1'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Inteligência Artificial
    |--------------------------------------------------------------------------
    |
    | O provider ativo é escolhido pelo .env:
    |
    | AI_PROVIDER=deepseek
    | AI_PROVIDER=gemini
    | AI_PROVIDER=ollama
    |
    */

    'ai' => [

        'provider' => env(
            'AI_PROVIDER',
            'deepseek'
        ),

        /*
        |--------------------------------------------------------------------------
        | Gemini
        |--------------------------------------------------------------------------
        */
        'gemini' => [
            'api_key' => env(
                'GEMINI_API_KEY'
            ),

            'model' => env(
                'GEMINI_MODEL',
                'gemini-3-flash-preview'
            ),
        ],

        /*
        |--------------------------------------------------------------------------
        | DeepSeek
        |--------------------------------------------------------------------------
        */
        'deepseek' => [
            'api_key' => env(
                'DEEPSEEK_API_KEY'
            ),

            'model' => env(
                'DEEPSEEK_MODEL',
                'deepseek-v4-flash'
            ),

            'base_url' => env(
                'DEEPSEEK_BASE_URL',
                'https://api.deepseek.com'
            ),
        ],

        /*
        |--------------------------------------------------------------------------
        | Ollama
        |--------------------------------------------------------------------------
        */
        'ollama' => [
            'base_url' => env(
                'OLLAMA_BASE_URL',
                'http://127.0.0.1:11434'
            ),

            'model' => env(
                'OLLAMA_MODEL',
                'qwen2.5-coder'
            ),

            'timeout' => env(
                'OLLAMA_TIMEOUT',
                8
            ),
        ],
    ],

];