<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;

/**
 * Class UrlShortenerController
 *
 * Controller for shortening URLs.
 *
 * @package App\Http\Controllers\Web
 */
class UrlShortenerController extends Controller
{

    public function useUrlHash(string $id): RedirectResponse
    {
        $url = ShortUrl::getUrlByHash($id);
        if ($url === null) {
            return redirect()->route('home', [
                'error' => 'The URL ID was not found'
            ]);
        }
        return redirect($url);
    }
}