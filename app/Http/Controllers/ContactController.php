<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Mail\ContactMail;
use Illuminate\Http\{JsonResponse};
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendEmail(SendEmailRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $data = [
            'firstName' => $validatedData['first-name'],
            'lastName' => $validatedData['last-name'],
            'email' => $validatedData['email'],
            'message' => $validatedData['message'],
        ];

        Mail::to('handpickd.shop@gmail.com')->send(new ContactMail($data));

        return response()->json(['message' => 'Email sent successfully']);
    }
}
