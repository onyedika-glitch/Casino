<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class DetailSetting extends Page
{
    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.detail-setting';

    /**
     * @dev @𝓗𝓐𝓡𝓚𝓩𝓘𝓜 / by OndaGames.com < - Esse sistema e Gratuito - Entre no nosso Grupo  https://t.me/+dFr8-1AmUz5hZDc5
     * @param Model $record
     * @return bool
     */
    public static function canView(Model $record): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
