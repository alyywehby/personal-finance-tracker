<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $request->validate(['locale' => 'required|in:en,ar']);
        auth()->user()->update(['locale' => $request->locale]);

        return $this->apiResponse(['locale' => $request->locale], 'Locale updated successfully');
    }
}
