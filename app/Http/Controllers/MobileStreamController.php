<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MobileStreamController extends Controller
{
    /**
     * Menampilkan halaman iframe untuk mobile.
     */
    public function showIframe(Request $request): View
    {
        $type = $request->query('type');
        $url = $request->query('url');

        // Validasi parameter
        if (!$type || !$url) {
            abort(400, 'Parameter "type" dan "url" wajib diisi.');
        }
        
        // Mengarahkan ke view 'mobile-stream.blade.php'
        return view('mobile-stream', [
            'type' => $type,
            'url' => $url,
        ]);
    }
}