<?php

namespace App\Services\RefererHash;

use Illuminate\Http\Request;

interface RefererHashCreator {
    public function createRefererHash(Request $request, int $timestamp): string;
}