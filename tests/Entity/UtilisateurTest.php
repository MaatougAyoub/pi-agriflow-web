<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Utilisateur;
use App\Enum\Role;
use PHPUnit\Framework\TestCase;

final class UtilisateurTest extends TestCase
{
    public function testGetRolesNormalizesDomainRole(): void
    {
        $user = (new Utilisateur())->setRole('AGRICULTEUR');

        self::assertSame([Role::AGRICULTEUR->value], $user->getRoles());
    }

    public function testGetRolesKeepsPrefixedRole(): void
    {
        $user = (new Utilisateur())->setRole(Role::EXPERT->value);

        self::assertSame([Role::EXPERT->value], $user->getRoles());
    }

    public function testGetRolesDefaultsToAgriculteurWhenRoleIsMissing(): void
    {
        $user = new Utilisateur();

        self::assertSame([Role::AGRICULTEUR->value], $user->getRoles());
    }

    public function testGetUserIdentifierReturnsEmail(): void
    {
        $user = (new Utilisateur())->setEmail('user@example.com');

        self::assertSame('user@example.com', $user->getUserIdentifier());
    }

    public function testGetPasswordReturnsStoredHash(): void
    {
        $user = (new Utilisateur())->setMotDePasse('hashed-password');

        self::assertSame('hashed-password', $user->getPassword());
    }

    public function testGetUserIdentifierReturnsEmptyStringWhenEmailMissing(): void
    {
        $user = new Utilisateur();

        self::assertSame('', $user->getUserIdentifier());
    }

    public function testGetRolesDefaultsWhenRoleIsUnknown(): void
    {
        $user = (new Utilisateur())->setRole('superuser');

        self::assertSame([Role::AGRICULTEUR->value], $user->getRoles());
    }
}
