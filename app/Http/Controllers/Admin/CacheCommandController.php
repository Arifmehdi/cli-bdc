<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CacheCommand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class CacheCommandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $data = CacheCommand::query();
        if($request->dealer_state != null){
            $data->where('state',$request->dealer_state);
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('DT_RowIndex', function ($data) {
                    return $data->id;
                })
                ->addColumn('date', function ($row) {
                    return $row->updated_at->format('m-d-Y');
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return "<span class='badge bg-success'>Active</span>";
                    } else {
                        return "<span class='badge bg-warning'>Inactive</span>";
                    }
                })
                ->addColumn('action', function ($row) {
                    $html = '<a
                    data-name="' . $row->name . '"
                    data-id="' . $row->id . '"
                    data-command="' . $row->command . '"
                    data-description="' . $row->description . '"
                    style="margin-right:3px"
                    href="javascript:void(0);"
                    class="btn btn-info btn-sm run-command"
                    title="Create ' . htmlspecialchars($row->name) .'">
                    <i class="fas fa-sync-alt"></i>
                    </a>' .
                    '<a href="javascript:void(0);" data-id="' . $row->id . '" style="margin-right:3px" href="" class="btn btn-primary btn-sm single-news-show"><i class="fa fa-eye"></i></a>' .
                    '<a data-id="' . $row->id . '" style="margin-right:3px" href="javascript:void(0);" class="btn btn-danger btn-sm delete-cache" title="Delete ' . htmlspecialchars($row->name) .'" ><i class="fa fa-trash"></i></a>';
                    return $html;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'dealer');
        })
        ->whereNotNull('city')
        ->where('city', '!=', '')
        ->whereNotNull('state')
        ->where('state', '!=', '')
        ->get(['id', 'name', 'city', 'state']);
        $inventory_dealer_state= $users->pluck('id', 'state');
        return view('backend.admin.cache-commands.index', compact('inventory_dealer_state'));
    }

    public function getCommands()
    {
        $commands = CacheCommand::all();
        return response()->json(['data' => $commands]);
    }


    public function runAll()
    {
        $commands = CacheCommand::all();

        foreach ($commands as $command) {
            Artisan::call($command->command);

            // After successful deletion, update status to 0
            $command->status = 1;
            $command->save();
        }

        return response()->json(['success' => true, 'message' => 'All cache commands executed successfully']);
    }


    public function deleteAll()
    {
        $commands = CacheCommand::all();

        foreach ($commands as $command) {
            // // 1. Run the Artisan command
            // Artisan::call($command->command);

            // 2. Delete the cache file if it exists
            if ($command->cache_file && file_exists($command->cache_file)) {
                try {
                    unlink($command->cache_file);
                    // After successful deletion, update status to 0
                    $command->status = 0;
                    $command->save();
                } catch (\Exception $e) {
                    // \Log::error("Failed to delete cache file: {$command->cache_file}. Error: {$e->getMessage()}");
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All cache files deleted successfully.'
        ]);
    }

    public function runSingle($id)
    {
        $command = CacheCommand::findOrFail($id);
        Artisan::call($command->command);
        // After successful deletion, update status to 0
        $command->status = 1;
        $command->save();

        return response()->json(['success' => true, 'message' => 'Command executed successfully']);
    }

    public function deleteCache($id)
    {
        $command = CacheCommand::findOrFail($id);
        if ($command->cache_file && file_exists($command->cache_file)) {
            try {
                unlink($command->cache_file);

                // After successful deletion, update status to 0
                $command->status = 0;
                $command->save();
            } catch (\Exception $e) {
                // \Log::error("Failed to delete cache file: {$command->cache_file}. Error: {$e->getMessage()}");
            }
        }

        return response()->json(['success' => true, 'message' => 'Cache deleted successfully']);
    }

    public function singleCacheCommands(Request $request)
    {
        $data = CacheCommand::find($request->id);
        return response()->json(['data' => $data]);
    }

    // public function index()
    // {
    //     //
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
