<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * 例外をHTTPレスポンスに変換します。
     *
     * @param  \Illuminate\Http\Request  $request  リクエストインスタンス
     * @param  \Throwable  $e  投げられた例外
     * @return \Symfony\Component\HttpFoundation\Response  レスポンス
     */
    public function render($request, Throwable $e)
    {
        // 認証エラー（未認証）
        if ($e instanceof AuthenticationException) {
            return response()->json(['message' => '認証が必要です。'], 401);
        }

        // 認可エラー（権限不足）
        if ($e instanceof AuthorizationException) {
            return response()->json(['message' => 'この操作は許可されていません。'], 403);
        }

        // APIリクエスト時の共通エラーレスポンス
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => class_basename($e),
            ], $this->getStatusCode($e));
        }

        // 通常（Web）の例外ハンドリング
        return parent::render($request, $e);
    }

    /**
     * 例外の種類に応じたHTTPステータスコードを取得します。
     *
     * @param  \Throwable  $e  例外インスタンス
     * @return int  HTTPステータスコード
     */
    protected function getStatusCode(Throwable $e): int
    {
        return match (true) {
            $e instanceof AuthenticationException => 401,      // 未認証
            $e instanceof AuthorizationException => 403,       // 権限エラー
            $e instanceof ValidationException => 422,          // バリデーションエラー
            $e instanceof NotFoundHttpException => 404,        // リソース未発見
            default => 500,                                     // その他（サーバーエラー）
        };
    }
}
