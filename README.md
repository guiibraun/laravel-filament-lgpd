# Laravel Filament LGPD

Pacote privado para Laravel e Filament 5 com recursos para política de privacidade, catálogo de cookies, versões do banner e registro das preferências do visitante.

O pacote também expõe os dados necessários para uma aplicação Inertia. A interface Vue pronta, incluindo o carregamento condicionado de scripts, fica no pacote [`@guiibraun/inertia-lgpd`](https://github.com/guiibraun/inertia-lgpd).

## Requisitos

- PHP 8.2 ou superior;
- uma aplicação Laravel com Filament 5;
- `inertiajs/inertia-laravel` 3 ou superior;
- acesso SSH de leitura ao GitHub para `git@github.com:guiibraun/laravel-filament-lgpd.git`.

## Instalação

Este é um repositório privado e não está disponível no Packagist. Configure o repositório VCS e instale uma versão marcada:

```bash
ssh -T git@github.com
composer config repositories.guiibraun-filament-lgpd vcs git@github.com:guiibraun/laravel-filament-lgpd.git
composer require guiibraun/laravel-filament-lgpd:^0.1 --prefer-source
```

Em CI ou produção, o ambiente precisa ter uma chave SSH com permissão de leitura no repositório. Para instalações reproduzíveis, mantenha o lock file versionado e use:

```bash
composer install --prefer-source --no-interaction
```

Se o projeto usa `dist` para as demais dependências, configure este pacote como `source` no `composer.json`:

```json
{
  "config": {
    "preferred-install": {
      "*": "dist",
      "guiibraun/laravel-filament-lgpd": "source"
    }
  }
}
```

O Laravel registra o service provider automaticamente. Publique a configuração e as migrações usando o instalador do pacote:

```bash
php artisan filament-lgpd:install
```

O comando publica `config/filament-lgpd.php` e as migrações. Se você não confirmar a execução durante o instalador, execute as migrações manualmente:

```bash
php artisan migrate
```

## Registrar o plugin no Filament

Adicione o plugin ao `PanelProvider` do painel que deve administrar privacidade e cookies:

```php
<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Guiibraun\FilamentLgpd\FilamentLgpdPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->plugin(FilamentLgpdPlugin::make());
    }
}
```

O plugin descobre os resources e a página de gerenciamento da política de privacidade. Para desabilitar algum grupo, altere `filament.register_resources` ou `filament.register_pages` em `config/filament-lgpd.php`.

## Criar o catálogo inicial

Depois de migrar o banco, crie as categorias, definições e a primeira versão publicada do banner:

```bash
php artisan filament-lgpd:seed-catalog
```

O comando é idempotente para categorias e definições. Uma nova versão do banner só é criada quando ainda não existe uma versão publicada.

## Rotas e props Inertia

Por padrão, o pacote registra:

| Método | Caminho            | Nome              | Finalidade                    |
| ------ | ------------------ | ----------------- | ----------------------------- |
| `GET`  | `/privacidade`     | `privacy`         | Política de privacidade       |
| `GET`  | `/cookies`         | `cookies`         | Catálogo de cookies           |
| `POST` | `/cookies/consent` | `cookies.consent` | Salvar a escolha do visitante |

O provider também compartilha os props `cookieBanner` e `cookieConsent` com o Inertia. O banner publicado inclui as categorias e os scripts ativos no snapshot. O pacote npm usa esses nomes por padrão.

Se a aplicação já possui suas próprias rotas ou controllers, desative as rotas do pacote para evitar colisões:

```php
// config/filament-lgpd.php

return [
    'routes' => [
        'enabled' => false,
    ],
];
```

Nesse caso, a aplicação continua podendo usar os models, resources e o compartilhamento Inertia do pacote, mas deve manter suas próprias rotas e endpoints.

## Configuração principal

Edite `config/filament-lgpd.php` após publicar a configuração. Os pontos mais comuns são:

```php
return [
    'cookie' => [
        'name' => 'lgpd_consent',
        'minutes' => 60 * 24 * 365,
        'http_only' => true,
        'same_site' => 'lax',
    ],

    'retention_years' => 5,
    'schedule_pruning' => true,
];
```

Também é possível substituir os models por classes que estendam os models do pacote usando a seção `models` da configuração.

Quando `schedule_pruning` está habilitado, o pacote agenda diariamente o comando `cookies:prune-consents`, que remove registros mais antigos que `retention_years`. O scheduler do Laravel precisa estar configurado no ambiente.

## Pacote Vue para Inertia

Para usar os componentes Vue prontos, instale o pacote separado:

```bash
pnpm add "git+ssh://git@github.com/guiibraun/inertia-lgpd.git#v0.1.2"
```

Consulte o [README do pacote Inertia](https://github.com/guiibraun/inertia-lgpd) para a montagem do banner, o catálogo e a personalização da interface.

## Scripts de terceiros

O resource `Cookies > Scripts` centraliza os scripts que podem ser usados pelo site. Cada registro possui:

- categoria de consentimento;
- nome, provedor e finalidade;
- posição `Head`, `Body — início` ou `Body — final`;
- fonte por URL externa ou código inline;
- status ativo e ordem de carregamento.

Depois de criar ou alterar scripts, publique uma nova versão em `Cookies > Versões`. Somente scripts ativos da versão publicada são enviados ao frontend; isso mantém o registro de consentimento vinculado à configuração que estava vigente naquele momento.

O campo de código aceita somente o conteúdo JavaScript, sem tags `<script>`. A administração desses registros deve ficar protegida pelo acesso normal do painel Filament, pois código inline e URLs externas são conteúdo privilegiado.

Para efetivar o carregamento no frontend, monte o `CookieScriptLoader` do pacote Inertia no mesmo layout persistente do banner. Ele cria os elementos no `head`, no início do `body` ou no final do `body` somente quando a categoria estiver autorizada. Scripts não essenciais não são executados, requisitados ou transformados em elementos `<script>` antes do consentimento; eles não devem ser colocados diretamente no Blade.

## LGPD

Este pacote fornece infraestrutura técnica para transparência, registro de preferências e carregamento condicionado dos scripts cadastrados. A aplicação ainda precisa definir as finalidades, bases legais, prazos de retenção, textos, controles de acesso, canal do encarregado e validar cada integração com a legislação vigente.

O pacote não constitui aconselhamento jurídico nem garante conformidade por si só. Valide a implementação com a legislação vigente, orientações da ANPD e assessoria especializada.

## Licença

MIT.
