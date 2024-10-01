<?php

function getUserInitials(string $name): string
{
    return collect(explode(' ', $name))
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->join('');
}
