<?php

namespace App\Jobs;

use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;

//use Illuminate\Database\Eloquent\Collection; // vecchia definizione
use Illuminate\Support\Collection;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UsersCsvExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     /**
     * The data to be exported as CSV.
     *
     * @var Collection
     */
    protected $data;

    /**
     * The name of the output file.
     *
     * @var string
     */
    protected $filename;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $data, string $filename)
    {
        if ($data->isNotEmpty() && is_numeric($data->first())) {
            // Supponiamo che $data contenga ID e tu debba caricare i modelli User
            $this->data = User::findMany($data)->pluck('email', 'name');
        } else {
            // Se $data non contiene ID, usa direttamente pluck
            $this->data = $data->pluck('email', 'name');
        }

        $this->filename = $filename;
        /*
        Log::info('UsersCsvExportJob Constructed', [
            'data' => $this->data,
            'filename' => $this->filename
        ]);
        */
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Log::info('UsersCsvExportJob started');

       try {
            Log::info('Handling UsersCsvExportJob', [
                'data' => $this->data
            ]);
            // lavoro terminato
            //Log::info('UsersCsvExportJob completato con successo.');
            

        // Il resto della tua logica di esportazione

        } catch (\Exception $e) {
            Log::error('Error in UsersCsvExportJob: ' . $e->getMessage());
        }
    }
}
