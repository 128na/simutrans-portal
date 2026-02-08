<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        version: '2.0.0',
        title: 'Simutrans Portal API',
        description: 'Simutrans アドオン投稿サイトのAPI仕様',
        contact: new OA\Contact(email: 'support@128-bit.net'),
        license: new OA\License(name: 'MIT', url: 'https://opensource.org/licenses/MIT')
    ),
    servers: [
        new OA\Server(url: 'http://localhost:8000', description: 'ローカル開発環境'),
        new OA\Server(url: 'https://simutrans-portal.128-bit.net', description: '本番環境'),
    ]
)]
class OpenApiSpec {}
