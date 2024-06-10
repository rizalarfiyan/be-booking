<?php

declare(strict_types=1);

namespace App\Controllers\Contact;

use App\Controllers\Controller;
use App\Services\ContactService;

class BaseContactController extends Controller
{
    /** @var ContactService */
    protected ContactService $contact;

    /**
     * Inject the service in the base controller.
     *
     * @param ContactService $contact
     */
    public function __construct(ContactService $contact)
    {
        $this->contact = $contact;
    }
}
