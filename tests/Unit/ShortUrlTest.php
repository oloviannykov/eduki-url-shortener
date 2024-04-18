<?php

namespace Tests\Unit;

use App\Models\ShortUrl;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{

    #[TestWith([''])]
    #[TestWith(['  '])]
    #[TestWith(["\t\n"])]
    public function testGetHashReturnsEmptyString(string $url): void
    {
        $hash = ShortUrl::getHash($url);
        $this->assertEquals('', $hash);
    }

    #[TestWith(['ftp://library.com/qwerty.pdf'])]
    #[TestWith(['http://x.com/qwerty/edit?zxcvbn=877676&aaa=jhgjg'])]
    #[TestWith(['https://x.com/qwerty.php'])]
    #[TestWith(['a'])]
    #[TestWith(['long value uytutumnbmb kgkkjhjhjhk mnbmbmbnmbnb 87686767676 nnvvnvbnv 432525409899'])]
    public function testGetHashReturnsValue(string $url): void
    {
        $hash = ShortUrl::getHash($url);
        $this->assertNotEmpty($hash, "result is empty");
        $this->assertTrue(strlen($hash) >= 16, "result is too short: $hash");
        $this->assertTrue(strlen($hash) <= 20, "result is too long: $hash");
    }

    #[TestWith(['', ShortUrl::ERROR_TOO_SHORT])]
    #[TestWith(['  ', ShortUrl::ERROR_TOO_SHORT])]
    #[TestWith(["\t\n", ShortUrl::ERROR_TOO_SHORT])]
    #[TestWith(["http://g.com/abc", ShortUrl::ERROR_TOO_SHORT])]
    #[TestWith(['qwerty@example.com', ShortUrl::ERROR_TOO_SHORT])]
    #[TestWith(['qwerty12345678-jhgjjhhhjg@example.com', ShortUrl::ERROR_EMAIL_FORMAT_NOT_ALLOWED])]
    #[TestWith(['ftp://library.com/qwerty12345678.pdf', ShortUrl::ERROR_WRONG_PROTOCOL])]
    #[TestWith(['ftp://user:pass@library.com/qwerty.pdf', ShortUrl::ERROR_EMAIL_FORMAT_NOT_ALLOWED])]
    #[TestWith(['//library.com/qwerty.pdf', ShortUrl::ERROR_WRONG_PROTOCOL])]
    #[TestWith(['http://../qwerty/asdf/1234567890asdf', ShortUrl::ERROR_WRONG_HOST_NAME])]
    public function testValidateUrlReturnsErrorCode(string $value, string $matchErrCode): void
    {
        $errorCode = ShortUrl::validateUrl($value);
        $this->assertNotEmpty($errorCode, "should return error code");
        $this->assertEquals($errorCode, $matchErrCode, "should return error code $matchErrCode");
    }

    #[TestWith(['http://x.com/qwerty/edit?zxcvbn=877676&aaa=jhgjg'])]
    #[TestWith(['https://x.com/qwerty123456789.php'])]
    #[TestWith(['HTTP://X.COM/qwerty123456789.php'])]
    #[TestWith(['http://localhost/qwerty123456789.php'])]
    public function testValidateUrlReturnsNoError(string $value): void
    {
        $this->assertNull(ShortUrl::validateUrl($value));
    }

    public function testCreateGetAndRemove(): void
    {
        $urlParam = 'http://g.com/?a=123&ts=' . time();

        $shortUrl = ShortUrl::createShortUrl($urlParam);
        $this->assertNotEmpty($shortUrl, "first createShortUrl result is empty");
        $this->assertTrue(strlen($shortUrl) > 20, "result is too short: $shortUrl");
        $this->assertTrue(strlen($shortUrl) < 50, "result is too long: $shortUrl");
        $hash = array_reverse(explode('/', $shortUrl))[0];
        $this->assertNotEmpty($hash, "hash is empty");

        $urlResult = ShortUrl::getUrlByHash($hash);
        $this->assertNotEmpty($urlResult, "getUrlByHash result is empty");
        $this->assertEquals($urlParam, $urlResult);

        $shortUrl2 = ShortUrl::createShortUrl($urlParam);
        $this->assertNotEmpty($shortUrl2, "second createShortUrl result is empty");
        $this->assertEquals($shortUrl, $shortUrl2, "short URL should be same for same full URL");

        ShortUrl::find($hash)?->delete();
        $urlResult = ShortUrl::getUrlByHash($hash);
        $this->assertEmpty($urlResult, "getUrlByHash result should be empty after removing hash");
    }

    #[TestWith([''])]
    #[TestWith(['  '])]
    #[TestWith(["\t\n"])]
    #[TestWith(['qwerty@example.com'])]
    #[TestWith(['wrong-hash'])]
    #[TestWith(['ftp://user:pass@library.com/qwerty.pdf'])]
    public function testGetUrlByHashReturnsNull(string $hash): void
    {
        $url = ShortUrl::getUrlByHash($hash);
        $this->assertNull($url, "result not null: $url");
    }

    #[TestWith(['', ShortUrl::ERROR_TOO_SHORT])]
    #[TestWith(['  ', ShortUrl::ERROR_TOO_SHORT])]
    #[TestWith(["\t\n", ShortUrl::ERROR_TOO_SHORT])]
    #[TestWith(["abc123", ShortUrl::ERROR_TOO_SHORT])]
    #[TestWith(["abcdefgh1235678901234567890", ShortUrl::ERROR_TOO_LONG])]
    public function testValidateIdReturnsErrorCode(string $id, string $matchErrCode): void
    {
        $errorCode = ShortUrl::validateId($id);
        $this->assertNotEmpty($errorCode, "should return error code");
        $this->assertEquals($errorCode, $matchErrCode, "should return error code $matchErrCode");
    }

    #[TestWith(['1234567890abcdef'])]
    #[TestWith(['1234567890abcdefgh'])]
    #[TestWith(['abcdef1234567890'])]
    public function testValidateIdReturnsNoError(string $value): void
    {
        $this->assertNull(ShortUrl::validateId($value));
    }
}