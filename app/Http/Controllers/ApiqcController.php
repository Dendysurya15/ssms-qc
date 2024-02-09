<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiqcController extends Controller
{
    public function getHistoryedit(Request $request)
    {
        // Get the latest ID received from the client
        $latestId = (int) $request->input('latest_id');

        // Query to retrieve new data since the latest ID
        $query = DB::connection('mysql2')
            ->table('history_edit')
            ->select('id', 'nama_user', 'tanggal', 'menu')
            ->where('id', '>', $latestId)
            ->get();

        // Convert the query result to an array
        $result = $query->toArray();

        // Return the result as JSON response
        return response()->json($result);
    }
}
