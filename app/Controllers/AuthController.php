<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AuthService;
use App\Services\CsrfService;

final class AuthController extends Controller
{
    public function __construct(array $config, private User $users, private AuthService $auth, private ActivityLogService $activity)
    {
        parent::__construct($config);
    }

    public function showLogin(): void
    {
        $this->view('auth/login', ['csrf' => CsrfService::token()], 'layouts/guest');
    }

    public function login(): void
    {
        if (!CsrfService::validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            $this->redirect('/login');
        }

        if ($this->auth->attempt(trim($_POST['email'] ?? ''), $_POST['password'] ?? '')) {
            $this->activity->log((int)$_SESSION['user']['id'], 'login', 'user', (int)$_SESSION['user']['id']);
            $this->redirect('/dashboard');
        }

        flash('error', 'Nieprawidłowy email lub hasło.');
        $this->redirect('/login');
    }

    public function showRegister(): void
    {
        $this->view('auth/register', ['csrf' => CsrfService::token()], 'layouts/guest');
    }

    public function register(): void
    {
        if (!CsrfService::validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            $this->redirect('/register');
        }

        $username = trim($_POST['username'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || !$email || strlen($password) < 8) {
            flash('error', 'Wypełnij poprawnie formularz (hasło min. 8 znaków).');
            $this->redirect('/register');
        }

        $id = $this->users->create($username, $email, $password);
        $this->activity->log($id, 'register', 'user', $id);
        flash('success', 'Konto utworzone. Możesz się zalogować.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        if (isset($_SESSION['user']['id'])) {
            $this->activity->log((int)$_SESSION['user']['id'], 'logout', 'user', (int)$_SESSION['user']['id']);
        }
        AuthService::logout();
        $this->redirect('/login');
    }

    public function showReset(): void
    {
        $this->view('auth/reset', ['csrf' => CsrfService::token()], 'layouts/guest');
    }
}
