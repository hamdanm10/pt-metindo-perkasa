<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Actions\Articles\RecordArticleView;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleViewController extends Controller
{
    private const COOKIE = 'viewed_articles';

    public function store(Request $request, RecordArticleView $record, string $article): Response
    {
        $raw = $request->cookies->get(self::COOKIE);
        $cookie = is_string($raw) ? json_decode($raw, true) : null;
        $user = $request->user();

        $remembered = $record->execute(
            app()->getLocale(),
            $article,
            $request->userAgent(),
            $user !== null && $user->role->usesStaffArea(),
            is_array($cookie) ? $cookie : [],
        );

        $response = response()->noContent();

        if ($remembered !== null) {
            $response->withCookie(cookie(self::COOKIE, (string) json_encode($remembered), 60 * 24, '/', null, $request->isSecure(), true, false, 'lax'));
        }

        return $response;
    }
}
