<?php

use Dom\CharacterData;

session_start();
require_once 'db.php';

function isLoggedIn(): bool
{
      return isset($_SESSION['user_id']);
}

function requireLogin()
{
      if (!isLoggedIn()) {
            header('Location: login.php');
            exit;
      }
}

function currentUserName(): string
{
      return $_SESSION['ad_soyad'] ?? '';
}

function currentUserRole(): string
{
      return $_SESSION['role'] ?? '';
}

function currentUserId(): int
{
      return (int)($_SESSION['user_id'] ?? 0);
}
