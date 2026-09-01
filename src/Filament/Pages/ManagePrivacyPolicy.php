<?php

namespace Guiibraun\FilamentLgpd\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Guiibraun\FilamentLgpd\Models\PrivacyPolicy;

/**
 * @property-read Schema $form
 */
class ManagePrivacyPolicy extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $navigationLabel = 'Política de Privacidade';

    protected static ?string $title = 'Política de Privacidade';

    protected static ?string $slug = 'privacidade';

    protected string $view = 'filament-lgpd::filament.pages.manage-privacy-policy';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()?->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    Section::make('Documento')
                        ->description('Publicado na página de privacidade. Use títulos H2 para as seções numeradas.')
                        ->icon(Heroicon::OutlinedDocumentText)
                        ->schema([
                            RichEditor::make('body')
                                ->label('Corpo')
                                ->json()
                                ->required()
                                ->columnSpanFull()
                                ->toolbarButtons([
                                    ['bold', 'italic', 'underline', 'strike', 'link'],
                                    ['h2', 'h3'],
                                    ['blockquote', 'bulletList', 'orderedList'],
                                    ['undo', 'redo'],
                                ]),
                        ]),
                ])
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Salvar')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ]),
                    ]),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $record = $this->getRecord();

        if (! $record instanceof PrivacyPolicy) {
            /** @var class-string<PrivacyPolicy> $modelClass */
            $modelClass = (string) config('filament-lgpd.models.privacy_policy', PrivacyPolicy::class);
            $record = new $modelClass;
        }

        $record->fill($data);
        $record->save();

        $this->form->record($record)->fill($record->attributesToArray());

        Notification::make()
            ->success()
            ->title('Política de privacidade salva')
            ->send();
    }

    public function getRecord(): ?PrivacyPolicy
    {
        /** @var class-string<PrivacyPolicy> $modelClass */
        $modelClass = (string) config('filament-lgpd.models.privacy_policy', PrivacyPolicy::class);

        return $modelClass::current();
    }
}
