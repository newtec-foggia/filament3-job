<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Jobs\UsersCsvExportJob;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('email'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at'),

                Tables\Columns\TextColumn::make('updated_at'),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                Tables\Actions\BulkAction::make('export-jobs')
                    ->label('Background Export')
                    ->icon('heroicon-o-cog')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        UsersCsvExportJob::dispatch($records, 'users.csv');
                        Notification::make()
                            ->title('Lavoro disponibile..')
                            ->body('Il lavoro richiesto è in elaborazione. Puoi controllare lo stato nella gestione download.')
                            ->success()
                            ->seconds(5)
                            ->icon('heroicon-o-inbox-arrow-down')
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('export-jobs2')
                    ->label('Background Export 2')
                    ->icon('heroicon-o-cog')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->chunk(100)->each(function ($chunk) {
                            UsersCsvExportJob::dispatch($chunk, 'users.csv');
                        });
                        Notification::make()
                            ->title('Lavoro disponibile..a pezzi')
                            ->body('Il lavoro richiesto è in elaborazione. Puoi controllare lo stato nella gestione download.')
                            ->success()
                            ->seconds(5)
                            ->icon('heroicon-o-inbox-arrow-down')
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
                
                Tables\Actions\BulkAction::make('export-jobs3')
                    ->label('Background Export 3')
                    ->icon('heroicon-o-cog')
                    ->requiresConfirmation()
                    ->action(function ($livewire) {
                        //$records = $livewire->selectedTableRecords; // contiene gli id dei record selezionati
                        $records = collect($livewire->selectedTableRecords);
                        //dd($records);
                        //
                        //$jsonRecords = $records->toJson();
                        //Log::info("Records: {$jsonRecords}");
                        //
                        $records->chunk(250)->each(function ($chunk) {
                            UsersCsvExportJob::dispatch($chunk, 'users.csv');
                        });
                        Notification::make()
                            ->title('Lavoro disponibile...')
                            ->body('Il lavoro richiesto è stato elaborato.')
                            ->success()
                            ->seconds(5)
                            ->icon('heroicon-o-inbox-arrow-down')
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('export-jobs-BATCH')
                    ->label('Background Export Batch')
                    ->icon('heroicon-o-cog')
                    //->requiresConfirmation()
                    ->action(function () {
                        //UsersCsvExportJob::dispatch($livewire->selectedTableRecords, 'users.csv');
                        //UsersCsvExportJob::dispatch(serialize($livewire->selectedTableRecords), 'users.csv');
                        //dd($livewire->selectedTableRecords);

                        Notification::make()
                            ->title('Lavoro disponibile..')
                            ->body('Il lavoro richiesto è in elaborazione. Puoi controllare lo stato nella gestione download.')
                            ->success()
                            ->seconds(5)
                            ->icon('heroicon-o-inbox-arrow-down')
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),

                Tables\Actions\BulkAction::make('delete ***')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete()),
            ]),

                 
                    
              
                      
                 
              
                
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
