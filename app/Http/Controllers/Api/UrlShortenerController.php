<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Class UrlShortenerController
 *
 * Controller for shortening URLs.
 *
 * @package App\Http\Controllers\Api
 */
class UrlShortenerController extends Controller
{

    /**
     * Retrieve list of URLs
     *
     * @param Request $request The HTTP request instance.
     * @return array<string, mixed> Details of the incident for editing.
     */
    public function index(Request $request): array
    {
        $startDate = date('Y-m-d', strtotime('-1 month'));
        /** @var Collection|ShortUrl[] $items */
        $items = ShortUrl::where('created_at', '>', $startDate)
            ->orderByDesc('created_at')
            ->take(30) //equivalent of limit(30), get last 30 records
            ->get();

        $result = [];
        /** @var ShortUrl $item */
        foreach ($items as $item) {
            $result[] = [
                'id' => $item->id,
                'short_url' => $item->getShortUrl(),
                'original_url' => $item->original_url,
                'created_at' => date('Y-m-d', strtotime($item->created_at)),
                'usage_counter' => $item->usage_counter,
                'last_usage_date' => date('Y-m-d', strtotime($item->updated_at)),
            ];
        }

        return [
            'success' => true,
            'items' => $result,
        ];
    }

    /**
     * Create a new URL
     *
     * @param Request $request The HTTP request instance.
     * @return array<string, mixed> The result of the save operation.
     */
    public function create(Request $request): array
    {
        $url = $request->post('url', '');

        $errorCode = ShortUrl::validateUrl($url);
        if ($errorCode) {
            $messages = [
                ShortUrl::ERROR_TOO_SHORT => 'URL is too short',
                ShortUrl::ERROR_TOO_LONG => 'URL is too long',
                ShortUrl::ERROR_WRONG_PROTOCOL => 'URL has wrong protocol. Allowed only http or https',
                ShortUrl::ERROR_WRONG_HOST_NAME => 'URL has no server name',
                ShortUrl::ERROR_EMAIL_FORMAT_NOT_ALLOWED => '@ is not allowed',
            ];
            $errorMessage = $messages[$errorCode] ?? 'internal server error';
            return [
                'error' => [
                    'code' => $errorCode,
                    'message' => $errorMessage,
                ],
            ];
        }

        $record = ShortUrl::createShortUrl($url);
        return [
            'success' => true,
            'id' => $record->id,
            'url' => $record->getShortUrl(),
        ];
    }

    /**
     * Get full URL by ID (URL hash)
     *
     * @param Request $request The HTTP request instance.
     * @param string $id URL hash to remove
     * @return array<string, mixed> The result of the save operation.
     */
    public function getUrlById(Request $request, string $id): array
    {
        $errorCode = ShortUrl::validateId($id);
        if ($errorCode) {
            $messages = [
                ShortUrl::ERROR_TOO_SHORT => 'ID is too short',
                ShortUrl::ERROR_TOO_LONG => 'ID is too long',
            ];
            $errorMessage = $messages[$errorCode] ?? 'internal server error';
            return [
                'error' => [
                    'code' => $errorCode,
                    'message' => $errorMessage,
                ],
            ];
        }

        $url = ShortUrl::getUrlByHash($id);

        if ($url === null) {
            return [
                'error' => [
                    'code' => ShortUrl::ERROR_NOT_FOUND,
                    'message' => 'Record not found',
                ],
            ];
        }

        return [
            'success' => true,
            'url' => $url,
        ];
    }

    /**
     * Remove existing URL by id (URL hash)
     *
     * @param Request $request The HTTP request instance.
     * @param string $id URL hash to remove
     * @return array<string, mixed> The result of the save operation.
     */
    public function remove(Request $request, string $id): array
    {
        $errorCode = ShortUrl::validateId($id);
        if ($errorCode) {
            $messages = [
                ShortUrl::ERROR_TOO_SHORT => 'ID is too short',
                ShortUrl::ERROR_TOO_LONG => 'ID is too long',
            ];
            $errorMessage = $messages[$errorCode] ?? 'internal server error';
            return [
                'error' => [
                    'code' => $errorCode,
                    'message' => $errorMessage,
                ],
            ];
        }

        //the purpose is to make record disappear so it's ok if it wasn't found
        ShortUrl::find($id)?->delete();
        return [
            'success' => true,
        ];
    }
}
