<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ShortUrl
 *
 * Represents an URL record.
 *
 * @package App\Models
 *
 * @property string $id
 * @property string $original_url
 * @property int $usage_counter
 * @property string $created_at
 * @property string|null $updated_at
 */

class ShortUrl extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    const ID_MAX_LENGTH = 20;
    const URL_MAX_LENGTH = 1000;
    const ERROR_NOT_FOUND = 'record_not_found';
    const ERROR_TOO_SHORT = 'value_is_too_short';
    const ERROR_TOO_LONG = 'value_is_too_long';
    const ERROR_WRONG_PROTOCOL = 'wrong_url_protocol';
    const ERROR_WRONG_FORMAT = 'wrong_format';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'original_url',
    ];

    /**
     * The attributes that should be treated as dates.
     *
     * @var array<int, string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public static function validateUrl(string $url): ?string
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return self::ERROR_WRONG_FORMAT;
        }
        if (mb_strlen($url) < 20) {
            return self::ERROR_TOO_SHORT;
        }
        if (mb_strlen($url) > self::URL_MAX_LENGTH) {
            return self::ERROR_TOO_LONG;
        }
        $info = parse_url($url);
        $scheme = empty($info['scheme']) ? '' : strtolower($info['scheme']);
        if (!in_array($scheme, ['http', 'https'])) {
            return self::ERROR_WRONG_PROTOCOL;
        }
        return null;
    }

    public static function validateId(string $id): ?string
    {
        $id = trim($id);
        if (mb_strlen($id) < 10) {
            return self::ERROR_TOO_SHORT;
        }
        if (mb_strlen($id) > self::ID_MAX_LENGTH) {
            return self::ERROR_TOO_LONG;
        }
        return null;
    }

    public static function getHash($value): string
    {
        $value = trim($value);
        if (empty($value)) {
            return '';
        }
        $abc = 'abcdefghijklmnopqrstuvwxyz';
        $number2char = str_split('0123456789' . $abc . strtoupper($abc), 1);
        $char2number = array_flip($number2char);
        $hex = md5($value); //32 chars [0-9a-z] - HEX string of 16 bytes
        $result = '';
        for ($i = 0; $i < 32; $i += 2) {
            $v1 = $char2number[$hex[$i]];
            $v2 = $char2number[$hex[$i+1]];
            $sum = $v1*2 + $v2;
            $result .= $number2char[$sum];
        }
        return $result;
    }

    public static function getNewId(): string
    {
        return substr(
            base64_encode(uniqid() . time()),
            0,
            self::ID_MAX_LENGTH
        );
    }

    public static function createShortUrl(string $url): self
    {
        //validation was done in controller

        //hashing is preferred to prevent URLs duplicates
        $id = self::getHash($url);
        $record = ShortUrl::find($id);
        if ($record) {
            if ($record->original_url === $url) {
                return $record;
            }
            //When we get same hash for different urls we use id generation.
            //In this case same url can be stored under different ids.
            //Hashing is not perfect :)
            $id = self::getNewId();
        }
        $record = new ShortUrl();
        $record->id = $id;
        $record->original_url = $url;
        $record->save();
        return $record;
    }

    public function getShortUrl(): string
    {
        return config('app.url') . '/go/' . $this->id;
    }

    public static function getUrlByHash($urlHash): ?string
    {
        $record = ShortUrl::find($urlHash);
        if (empty($record)) {
            return null; //not found
        }
        $record->usage_counter += 1;
        $record->save();
        return $record->original_url;
    }
}
