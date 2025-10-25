<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserEmailRepository;
use App\Notifications\VerifyNewEmail;
use Illuminate\Support\Facades\URL;

class EmailService
{
    public function __construct(
        protected UserEmailRepository $emails
    ) {}

    public function requestEmailChange(User $user, string $newEmail, int $ttlSeconds = 86400): void
    {
        $pending = $this->emails->createPending($user, $newEmail, $ttlSeconds);

        $verifyUrl = URL::temporarySignedRoute(
            'email.verify',
            now()->addSeconds($ttlSeconds),
            ['token' => $pending->verify_token]
        );

        $user->notify(new VerifyNewEmail());
    }

    public function confirmEmailChange(User $user, string $token): void
    {
        $record = $this->emails->findPendingByToken($token);
        if (!$record || $record->user_id !== $user->id) {
            abort(403, '不正なリクエストです。');
        }

        $this->emails->markVerified($record);
        $this->emails->setPrimary($user, $record);
    }
}
