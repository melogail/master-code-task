<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Models\Vendor;
use App\Http\Resources\VendorResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class VendorController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return response()->json(
            ["vendors" => VendorResource::collection(Vendor::all())],
            200
        );
    }

    public function store(StoreVendorRequest $request)
    {
        $this->authorize('create', Vendor::class);

        $vendor = Vendor::create($request->validated());
        return response()->json([
            'vendor' => VendorResource::make($vendor),
        ], 201);
    }

    public function show(Vendor $vendor)
    {
        return response()->json([
            'vendor' => VendorResource::make($vendor),
        ], 200);
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor)
    {

        $this->authorize('update', $vendor);

        $vendor->update($request->validated());
        return response()->json([
            'vendor' => VendorResource::make($vendor->refresh()),
        ], 200);
    }

    public function destroy(Vendor $vendor)
    {
        $this->authorize('delete', $vendor);

        $vendor->delete();
        return response()->json(
            ['message' => 'Vendor deleted successfully'],
            200
        );
    }

    public function trashed()
    {
        $this->authorize('viewAllTrashed', Vendor::class);

        return response()->json([
            'vendors' => VendorResource::collection(Vendor::onlyTrashed()->get()),
        ], 200);
    }

    public function showTrashed($id)
    {
        $vendor = Vendor::withTrashed()->findOrFail($id);

        $this->authorize('viewTrashed', $vendor);

        return response()->json([
            'vendor' => VendorResource::make($vendor),
        ], 200);
    }

    public function restore($id)
    {
        $vendor = Vendor::withTrashed()->findOrFail($id);

        $this->authorize('restore', $vendor);

        $vendor->restore();
        return response()->json([
            'message' => 'Vendor restored successfully',
        ], 200);
    }

    public function forceDelete($id)
    {
        $vendor = Vendor::withTrashed()->findOrFail($id);

        $this->authorize('forceDelete', $vendor);

        $vendor->forceDelete();
        return response()->json([
            'message' => 'Vendor deleted permanently successfully',
        ], 200);
    }
}
