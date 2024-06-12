<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    //全てのサブドメインを信頼する
    //お金を払ってドメイン名を購入すると、そのドメイン名にはサブドメインが付与される
    public function hosts()
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
