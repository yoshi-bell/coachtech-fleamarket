<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\SoldItem; // SoldItemモデルを使用
use App\Models\Item; // Itemモデルを使用
use Illuminate\Support\Facades\DB; // トランザクション用

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET'); // .envからシークレットを取得

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            Log::warning('Stripe Webhook Signature Verification Failed.', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Webhook signature verification failed.'], 403);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::warning('Stripe Webhook Invalid Payload.', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload.'], 400);
        } catch (\Exception $e) {
            // Other errors
            Log::error('Stripe Webhook Error.', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'Webhook processing error.'], 500);
        }

        // イベントタイプごとの処理
        switch ($event->type) {
            case 'checkout.session.async_payment_succeeded':
                return $this->handleCheckoutSessionAsyncPaymentSucceeded($event);
            case 'payment_intent.payment_failed':
                // 支払い失敗時の処理
                Log::warning('Stripe Webhook: PaymentIntent failed.', ['payment_intent_id' => $event->data->object->id]);
                break;
            // その他のイベントタイプも必要に応じて追加
            default:
                Log::info('Stripe Webhook: Unhandled event type.', ['event_type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    private function handleCheckoutSessionAsyncPaymentSucceeded($event)
    {
        $session = $event->data->object;
        $metadata = $session->metadata; // Checkout Session作成時に保存したメタデータ

        // メタデータから必要な情報を取得
        $item_id = $metadata->item_id ?? null;
        $buyer_id = $metadata->buyer_id ?? null;
        $postcode = $metadata->postcode ?? null;
        $address = $metadata->address ?? null;
        $building = $metadata->building ?? '';

        if (!$item_id || !$buyer_id || !$postcode || !$address) {
            Log::error('Webhook: Missing metadata for async payment succeeded.', ['session_id' => $session->id, 'metadata' => $metadata]);
            return response()->json(['error' => 'Missing metadata.'], 400);
        }

        DB::beginTransaction();
        try {
            // 既に購入済みでないか確認
            $item = Item::find($item_id);
            if (!$item || $item->soldItem) {
                Log::warning('Webhook: Item already sold or not found.', ['item_id' => $item_id, 'session_id' => $session->id]);
                DB::rollback();
                return response()->json(['status' => 'ignored', 'message' => 'Item already sold or not found.'], 200);
            }

            SoldItem::create([
                'item_id' => $item_id,
                'buyer_id' => $buyer_id,
                'postcode' => $postcode,
                'address' => $address,
                'building' => $building,
            ]);

            DB::commit();
            Log::info('Webhook: SoldItem created successfully for async payment.', ['session_id' => $session->id, 'item_id' => $item_id]);
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Webhook: Error creating SoldItem for async payment.', ['exception' => $e->getMessage(), 'session_id' => $session->id]);
            return response()->json(['error' => 'Database error.'], 500);
        }
    }
}