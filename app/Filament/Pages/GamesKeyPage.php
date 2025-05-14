<?php

namespace App\Filament\Pages;

use App\Models\GamesKey;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class GamesKeyPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.games-key-page';

    protected static ?string $title = 'Chaves dos Jogos';

    protected static ?string $slug = 'chaves-dos-jogos';


    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }


    public ?array $data = [];
    public ?GamesKey $setting;

    /**
     * @return void
     */
    public function mount(): void
    {
        $gamesKey = GamesKey::first();
        if(!empty($gamesKey)) {
            $this->setting = $gamesKey;
            $this->form->fill($this->setting->toArray());
        }else{
            $this->form->fill();
        }
    }

    /**
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('ViperBET7K')
                    ->description('script 100% atualizado')
                    ->schema([])
                    ->columns(3),

                    Section::make('Play Fiver – Aproveite um GRR de 2% com Recarga Via Pix!')
                    ->description('Visite agora o site oficial em playfiver.com. A recarga mínima é de apenas R$100. Descubra uma seleção emocionante de jogos, incluindo Aviator, Mines, Fortune Tiger, Fortune Dragon, e muito mais! COMPRE A AQUI AQUI -> https://playfiver.com/ '),
                

                Section::make('Play Fiver')
                    ->description('Ajustes de credenciais para a Play Fiver | Seu WebHook > https://seusite.com/playfiver/webhook')
                    ->schema([
                        TextInput::make('playfiver_url')
                            ->label('URL')
                            ->placeholder('Digite aqui a URL')
                            ->maxLength(191),
                        TextInput::make('playfiver_secret')
                            ->label('Agent Secret')
                            ->placeholder('Digite aqui o código secreto do agente')
                            ->maxLength(191),
                        TextInput::make('playfiver_code')
                            ->label('Agent Code')
                            ->placeholder('Digite aqui o código do agente')
                            ->maxLength(191),
                        TextInput::make('playfiver_token')
                            ->label('Agent Token')
                            ->placeholder('Digite aqui o token do agente')
                            ->maxLength(191),
                    ])
                    ->columns(3),

                Section::make('World Slot API')
                    ->description('Ajustes de credenciais para a World Slot | Seu WebHook > https://seusite.com')
                    ->schema([
                        TextInput::make('worldslot_agent_code')
                            ->label('Agent Code')
                            ->placeholder('Digite aqui o Agent Code')
                            ->maxLength(191),
                        TextInput::make('worldslot_agent_token')
                            ->label('Agent Token')
                            ->placeholder('Digite aqui o Agent Token')
                            ->maxLength(191),
                        TextInput::make('worldslot_agent_secret_key')
                            ->label('Agent Secret Key')
                            ->placeholder('Digite aqui o Agent Secret Key')
                            ->maxLength(191),
                        TextInput::make('worldslot_api_endpoint')
                            ->label('Api Endpoint')
                            ->placeholder('Digite aqui a API Endpoint')
                            ->maxLength(191)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Fivers API')
                    ->description('Ajustes de credenciais para a Fivers | Seu WebHook > https://seusite.com')
                    ->schema([
                        TextInput::make('agent_code')
                            ->label('Agent Code')
                            ->placeholder('Digite aqui o Agent Code')
                            ->maxLength(191),
                        TextInput::make('agent_token')
                            ->label('Agent Token')
                            ->placeholder('Digite aqui o Agent Token')
                            ->maxLength(191),
                        TextInput::make('agent_secret_key')
                            ->label('Agent Secret Key')
                            ->placeholder('Digite aqui o Agent Secret Key')
                            ->maxLength(191),
                        TextInput::make('api_endpoint')
                            ->label('Api Endpoint')
                            ->placeholder('Digite aqui a API Endpoint')
                            ->maxLength(191)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

            ])
            ->statePath('data');
    }


    /**
     * @return void
     */
    public function submit(): void
    {
        try {
            if(env('APP_DEMO')) {
                Notification::make()
                    ->title('Atenção')
                    ->body('Você não pode realizar está alteração na versão demo')
                    ->danger()
                    ->send();
                return;
            }

            $setting = GamesKey::first();
            if(!empty($setting)) {
                if($setting->update($this->data)) {
                    Notification::make()
                        ->title('Chaves Alteradas')
                        ->body('Suas chaves foram alteradas com sucesso!')
                        ->success()
                        ->send();
                }
            }else{
                if(GamesKey::create($this->data)) {
                    Notification::make()
                        ->title('Chaves Criadas')
                        ->body('Suas chaves foram criadas com sucesso!')
                        ->success()
                        ->send();
                }
            }


        } catch (Halt $exception) {
            Notification::make()
                ->title('Erro ao alterar dados!')
                ->body('Erro ao alterar dados!')
                ->danger()
                ->send();
        }
    }
}
