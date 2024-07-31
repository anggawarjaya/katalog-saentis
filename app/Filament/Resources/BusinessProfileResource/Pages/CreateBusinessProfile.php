<?php

namespace App\Filament\Resources\BusinessProfileResource\Pages;

use App\Filament\Resources\BusinessProfileResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBusinessProfile extends CreateRecord
{
    protected static string $resource = BusinessProfileResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['approved'] = true;
        return $data;
    }

    protected function afterCreate(): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->success()
            ->title('Data UMKM '.$this->record->name.' berhasil ditambahkan')
            ->body('Berikan akses pada '.User::find($this->record->user_id)->name.' untuk dapat memperbarui informasi terkini dari usahanya.')
            ->actions([
                Action::make('TandaiSudahDiBaca')
                    ->button()
                    ->markAsRead(),
                Action::make('Lihat')
                    ->button()
                    ->url(fn() => BusinessProfileResource::getUrl('edit', ['record' => $this->record->id]), shouldOpenInNewTab:true),
            ])
            ->sendToDatabase($recipient);

        $recipients = User::whereHas('roles', function($query) {
            $query->where('name', '!=', 'umkm');
        })->where('id', '!=', $recipient->id)->get();

        Notification::make()
            ->success()
            ->title('Data UMKM '.$this->record->name.' berhasil ditambahkan')
            ->body('Data dibuat dan diverifikasi oleh ' . $recipient->name)
            ->actions([
                Action::make('TandaiSudahDiBaca')
                    ->button()
                    ->markAsRead(),
                Action::make('Lihat')
                    ->button()
                    ->url(fn() => BusinessProfileResource::getUrl('edit', ['record' => $this->record->id]), shouldOpenInNewTab:true),
            ])
            ->sendToDatabase($recipients);
    }
}
