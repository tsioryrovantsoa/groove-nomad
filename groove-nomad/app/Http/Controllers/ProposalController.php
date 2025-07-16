<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateProposalJob;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class ProposalController extends Controller
{
    public function acceptAndRedirectToStripe(Request $request, Proposal $proposal)
    {
        // Vérification que la proposition est bien en état 'generated'
        abort_unless($proposal->status === 'generated', 403);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Paiement proposition #' . $proposal->id,
                    ],
                    'unit_amount' => intval($proposal->total_price * 100), // en centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('proposals.payment.success', $proposal),
            'cancel_url' => route('request.index'),
            'metadata' => [
                'proposal_id' => $proposal->id,
            ],
        ]);

        return redirect($session->url);
    }
    public function handleStripeSuccess(Proposal $proposal)
    {
        // if ($proposal->status !== 'generated') {
        //     return redirect()->route('proposals.show', $proposal)->with('info', 'Déjà traité.');
        // }

        $proposal->update([
            'status' => 'accepted',
        ]);

        // ... Tu peux aussi envoyer un email ici.

        return redirect()->route('request.index')->with('success', 'Proposition acceptée et payée !');
    }

    public function reject(Request $request, Proposal $proposal)
    {
        // Vérification que la proposition est bien en état 'generated'
        abort_unless($proposal->status === 'generated', 403);

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000'
        ]);

        $proposal->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        GenerateProposalJob::dispatch($proposal->request);

        return redirect()->route('request.index')->with('success', 'Proposition refusée ! Nouvelle proposition en cours de génération...');
    }
}
