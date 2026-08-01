<?php 

namespace App\Http\Controllers\Web;

use Inertia\Inertia;
use Inertia\Response;

class VerifyErrorPageController
{
    public function index(): Response
    {
        return Inertia::render(component: 'VerifyError/VerifyError');
    }
}
