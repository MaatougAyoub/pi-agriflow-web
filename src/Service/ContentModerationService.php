<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Simple deterministic content moderation service.
 *
 * Acts as an extension point: override isFlagged() or inject an AI adapter
 * to replace the local keyword-based fallback with a real API call
 * (e.g., Gemini, Perspective API, etc.).
 */
class ContentModerationService
{
    /**
     * Words / patterns that flag content as inappropriate.
     * Replace or extend this list, or override isFlagged() with an API call.
     */
    private const BANNED_PATTERNS = [
        'spam', 'arnaque', 'escroquerie', 'fraude', 'scam',
        'fuck', 'merde', 'putain', 'connard', 'idiot',
        'gratuit argent', 'cliquez ici', 'click here',
    ];

    /**
     * Returns true when the text is considered inappropriate.
     *
     * Override this method (or decorate this service) to plug in a real AI API.
     */
    public function isFlagged(string $text): bool
    {
        $lower = mb_strtolower($text);

        foreach (self::BANNED_PATTERNS as $pattern) {
            if (str_contains($lower, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a human-readable moderation reason if flagged, null otherwise.
     */
    public function moderationReason(string $text): ?string
    {
        $lower = mb_strtolower($text);

        foreach (self::BANNED_PATTERNS as $pattern) {
            if (str_contains($lower, $pattern)) {
                return sprintf('Contenu suspect détecté : "%s"', $pattern);
            }
        }

        return null;
    }
}
