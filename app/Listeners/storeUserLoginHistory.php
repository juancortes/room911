<?php

namespace App\Listeners;

use App\Events\LoginHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\RecordsAccesses;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class storeUserLoginHistory
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LoginHistory  $event
     * @return void
     */
    public function handle(LoginHistory $event)
    {
        $current_timestamp = Carbon::now()->toDateTimeString();

        $userinfo = $event->user;
        $raccess = RecordsAccesses::where('user_id', '=', $userinfo->id)->get();
        $count = $raccess->count();
        $count++;

        $saveHistory = DB::table('records_accesses')->insert(
            [
                'cantidad' => $count, 
                'user_id' => $userinfo->id, 
                'created_at' => $current_timestamp, 
                'updated_at' => $current_timestamp
            ]
        );
        return $saveHistory;
    }
}
