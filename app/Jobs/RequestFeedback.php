<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\RequestFeedback as NotificationsRequestFeedback;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RequestFeedback implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Fetch users with completed appointments from the previous day
        $users = User::whereHas('patient.appointments', function ($query) {
            $query->where('status', 'completed')
                  ->whereDate('appointment_date', now()->subDays(1)->toDateString());
        })
        ->get()
        ->unique('id'); 


        foreach ($users as $user) {
            try {
                // Send feedback request notification
                $user->notify(new NotificationsRequestFeedback());
            } catch (\Exception $e) {
                // Log notification failure
                Log::error("Failed to send feedback request to User ID {$user->id}: " . $e->getMessage());
            }
        }
    }
}
