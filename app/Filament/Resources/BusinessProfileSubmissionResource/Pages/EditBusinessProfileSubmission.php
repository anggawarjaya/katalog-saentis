<?php

namespace App\Filament\Resources\BusinessProfileSubmissionResource\Pages;

use App\Filament\Resources\BusinessProfileResource;
use App\Filament\Resources\BusinessProfileSubmissionResource;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Actions\Action as ActionsAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBusinessProfileSubmission extends EditRecord
{
    protected static string $resource = BusinessProfileSubmissionResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('Setujui')->action('approved'),
        ];
    }

    public function approved(): void
    {
        $this->record->approved = 1;
        $this->record->save();

        $recipient = auth()->user();

        Notification::make()
            ->success()
            ->title('Data UMKM '.$this->record->name.' berhasil disetujui')
            ->body('Segera hubungi '.User::find($this->record->user_id)->name.' untuk dapat melihat kondisi terkini dari usahanya.')
            ->actions([
                ActionsAction::make('TandaiSudahDiBaca')
                    ->button()
                    ->markAsRead(),
                ActionsAction::make('Lihat')
                    ->button()
                    ->url(fn() => BusinessProfileResource::getUrl('edit', ['record' => $this->record->id]), shouldOpenInNewTab:true),
            ])
            ->sendToDatabase($recipient);

        $recipients = User::whereHas('roles', function($query) {
            $query->where('name', '!=', 'umkm');
        })->where('id', '!=', $recipient->id)->get();

        Notification::make()
            ->success()
            ->title('Data UMKM '.$this->record->name.' berhasil disetujui')
            ->body('Data disetujui oleh ' . $recipient->name)
            ->actions([
                ActionsAction::make('TandaiSudahDiBaca')
                    ->button()
                    ->markAsRead(),
                ActionsAction::make('Lihat')
                    ->button()
                    ->url(fn() => BusinessProfileResource::getUrl('edit', ['record' => $this->record->id]), shouldOpenInNewTab:true),
            ])
            ->sendToDatabase($recipients);

        $owner = User::where('id', '===', $this->record->id)->get();

        Notification::make()
            ->success()
            ->title('Data UMKM kamu '.$this->record->name.' berhasil disetujui')
            ->body('Data disetujui oleh ' . $recipient->name)
            ->actions([
                ActionsAction::make('TandaiSudahDiBaca')
                    ->button()
                    ->markAsRead(),
                ActionsAction::make('Lihat')
                    ->button()
                    ->url(fn() => BusinessProfileResource::getUrl('edit', ['record' => $this->record->id]), shouldOpenInNewTab:true),
            ])
            ->sendToDatabase($owner);

            $this->redirect(BusinessProfileSubmissionResource::getUrl('index'));
    }
}
