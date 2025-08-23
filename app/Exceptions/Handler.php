<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * アプリケーションの全ての例外をハンドリングするクラス
 */
class Handler extends ExceptionHandler
{
    /**
     * 例外をHTTPレスポンスに変換します。
     *
     * - APIリクエスト（expectsJson=true）の場合はJSON形式で返します。
     * - Webリクエストの場合は親クラスの標準処理を行います。
     *
     * @param  \Illuminate\Http\Request  $request  リクエストインスタンス
     * @param  \Throwable  $e  捕捉された例外
     * @return \Symfony\Component\HttpFoundation\Response  レスポンス
     */
    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessageForException($e),
                'type' => class_basename($e),
            ], $this->getStatusCode($e));
        }

        return parent::render($request, $e);
    }

    /**
     * 例外の種類に応じたHTTPステータスコードを返します。
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
            $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 302 => 302, // リダイレクト
            default => 500,                                     // その他（サーバーエラー）
        };
    }

    /**
     * 例外の種類に応じた日本語メッセージを返します。
     *
     * @param  \Throwable  $e  例外インスタンス
     * @return string  日本語のエラーメッセージ
     */
    protected function getMessageForException(Throwable $e): string
    {
        $baseMessage = match (true) {
            $e instanceof AuthenticationException => '認証が必要です。',
            $e instanceof AuthorizationException => 'この操作は許可されていません。',
            $e instanceof ValidationException => '入力内容に不備があります。',
            $e instanceof NotFoundHttpException => 'リソースが見つかりません。',
            default => 'システムエラーが発生しました。',
        };

        return $baseMessage . "\n" . ($e->getMessage() ?: '');
    }
}
