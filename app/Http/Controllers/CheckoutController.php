<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;
use MercadoPago\Payer;

class CheckoutController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the shipping information form
     */
    public function shipping()
    {
        // Verificar si hay items en el carrito
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('product')
            ->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                           ->with('error', 'Tu carrito está vacío. Agrega algunos productos antes de proceder al checkout.');
        }

        // Obtener datos de envío guardados en sesión
        $shippingData = session('checkout.shipping', []);

        return view('checkout.shipping', compact('shippingData'));
    }

    /**
     * Process shipping information and redirect to payment
     */
    public function processShipping(Request $request)
    {
        // Validar los datos del formulario
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:2',
            'postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        // Guardar información de envío en sesión con nombres de ciudad y estado
        $shippingData = $validated;
        
        // Obtener nombres de ciudad y estado desde la API
        try {
            // Obtener nombre del departamento
            $departmentResponse = file_get_contents("https://api-colombia.com/api/v1/Department/{$validated['state']}");
            $department = json_decode($departmentResponse, true);
            $shippingData['state_name'] = $department['name'] ?? $validated['state'];
            
            // Obtener nombre de la ciudad
            $cityResponse = file_get_contents("https://api-colombia.com/api/v1/City/{$validated['city']}");
            $city = json_decode($cityResponse, true);
            $shippingData['city_name'] = $city['name'] ?? $validated['city'];
        } catch (Exception $e) {
            // Si falla la API, usar los valores originales
            $shippingData['state_name'] = $validated['state'];
            $shippingData['city_name'] = $validated['city'];
        }
        
        session(['checkout.shipping' => $shippingData]);

        return redirect()->route('checkout.confirm');
    }

    /**
     * Show the confirmation page
     */
    public function showConfirm()
    {
        if (!session('checkout.shipping')) {
            return redirect()->route('checkout.shipping')
                           ->with('error', 'Por favor, completa la información de envío primero.');
        }

        $cartItems = Cart::where('user_id', Auth::id())
            ->with('product.offers')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                           ->with('error', 'Tu carrito está vacío.');
        }

        // Calcular totales
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->final_price * $item->quantity;
        });

        // Determinar costo de envío basado en ubicación
        $shippingData = session('checkout.shipping');
        $cityName = strtolower(trim($shippingData['city_name'] ?? $shippingData['city']));
        $stateName = strtolower(trim($shippingData['state_name'] ?? $shippingData['state']));
        
        $shippingCost = 17000; // Costo base de envío

        // Envio gratis para La Paz o San Diego (Cesar), o cualquier municipio de La Guajira
        if ((($cityName === 'la paz' || $cityName === 'san diego') && $stateName === 'cesar') ||
            $stateName === 'la guajira') {
            $shippingCost = 0;
        // Envio de $7,000 para Valledupar o Manaure
        } elseif ($cityName === 'valledupar' || $cityName === 'manaure') {
            $shippingCost = 7000;
        }

        $total = $subtotal + $shippingCost;

        return view('checkout.confirm', compact('cartItems', 'subtotal', 'shippingCost', 'total'));
    }

    public function process(Request $request)
    {
        // Verificar si existe información de envío
        if (!session('checkout.shipping')) {
            return redirect()->route('checkout.shipping')
                           ->with('error', 'Por favor, completa la información de envío primero.');
        }

        $validated = session('checkout.shipping');

        try {
            // Iniciar transacción
            DB::beginTransaction();

            // Obtener items del carrito
            $cartItems = Cart::where('user_id', Auth::id())
                ->with('product.offers')
                ->get();

            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')
                               ->with('error', 'Tu carrito está vacío.');
            }

            // Calcular totales
            $subtotal = $cartItems->sum(function($item) {
                return $item->product->final_price * $item->quantity;
            });

            // Determinar costo de envío basado en ubicación
            $shipping = 17000; // Costo base de envío
            // Usar los nombres resueltos de ciudad y estado, no los IDs numericos
            $cityName = strtolower(trim($validated['city_name'] ?? $validated['city']));
            $stateName = strtolower(trim($validated['state_name'] ?? $validated['state']));

            // Envio gratis para La Paz o San Diego (Cesar), o cualquier municipio de La Guajira
            if ((($cityName === 'la paz' || $cityName === 'san diego') && $stateName === 'cesar') ||
                $stateName === 'la guajira') {
                $shipping = 0;
            // Envio de $7,000 para Valledupar o Manaure
            } elseif ($cityName === 'valledupar' || $cityName === 'manaure') {
                $shipping = 7000;
            }

            $total = $subtotal + $shipping;

            // Crear la orden
            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'first_name' => $validated['first_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'],
                'notes' => $validated['notes'],
            ]);

            // Crear los items de la orden
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'final_price' => $item->product->final_price,
                ]);
            }

            // Guardar la orden en la sesión para el checkout
            Session::put('checkout_order_id', $order->id);

            // Commit la transacción
            DB::commit();

            // Redirigir a la pasarela de pago de Wompi
            return redirect()->route('checkout.payment');

        } catch (\Exception $e) {
            // Rollback en caso de error
            DB::rollBack();
            
            \Log::error('Error al procesar orden: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => Auth::id(),
                'cart_items' => $cartItems->pluck('product_id')->toArray() ?? []
            ]);

            return redirect()->back()
                           ->with('error', 'Hubo un error al procesar tu orden. Por favor, intenta nuevamente.')
                           ->withInput();
        }
    }

    /**
     * Crear preferencia de Mercado Pago y redirigir al enlace de pago
     */
    public function payment()
    {
        // Verificar si hay una orden en proceso
        if (!Session::has('checkout_order_id')) {
            return redirect()->route('cart.index')
                           ->with('error', 'No hay una orden en proceso. Por favor, comienza el checkout nuevamente.');
        }

        $order = Order::with('items.product')->findOrFail(Session::get('checkout_order_id'));

        try {
            // Configurar SDK de Mercado Pago
            SDK::setAccessToken(config('services.mercadopago.access_token'));

            // Crear preferencia de pago
            $preference = new Preference();

            // Agregar items
            $items = [];
            foreach ($order->items as $orderItem) {
                $item = new Item();
                $item->title = $orderItem->product->name;
                $item->quantity = $orderItem->quantity;
                $item->unit_price = floatval($orderItem->final_price ?? $orderItem->price);
                $item->currency_id = 'COP';
                $items[] = $item;
            }

            // Agregar envío como un item si tiene costo
            if ($order->shipping > 0) {
                $shippingItem = new Item();
                $shippingItem->title = 'Costo de envío';
                $shippingItem->quantity = 1;
                $shippingItem->unit_price = floatval($order->shipping);
                $shippingItem->currency_id = 'COP';
                $items[] = $shippingItem;
            }

            $preference->items = $items;

            // Configurar URLs de retorno
            $preference->back_urls = (object) array(
                "success" => route('checkout.success'),
                "failure" => route('cart.index'),
                "pending" => route('checkout.success')
            );
            $preference->auto_return = "approved";

            // Configurar referencia externa (ID de la orden)
            $preference->external_reference = strval($order->id);

            // Información del pagador (Debe ser un objeto en este wrapper)
            $payer = new Payer();
            $payer->name = $order->first_name;
            $payer->email = $order->email;
            $preference->payer = $payer;

            // Guardar preferencia
            $preference->save();

            // Guardar el ID de preferencia en la orden
            $order->payment_id = $preference->id;
            $order->save();

            \Log::info('MERCADOPAGO PREFERENCE INFO:', [
                'id' => $preference->id,
                'init_point' => $preference->init_point,
                'sandbox_init_point' => $preference->sandbox_init_point,
            ]);

            // Determinar URL de pago según modo (sandbox o producción)
            $esSandbox = config('services.mercadopago.sandbox', true);
            $urlPago = $esSandbox
                ? $preference->sandbox_init_point
                : $preference->init_point;

            if (empty($urlPago)) {
                throw new \Exception('No se pudo generar la URL de pago de Mercado Pago. La preferencia se guardó sin init_point.');
            }

            // Redirigir directamente a la pasarela de pago de Mercado Pago
            return redirect()->away($urlPago);

        } catch (\Exception $e) {
            \Log::error('MERCADOPAGO EXCEPTION: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return redirect()->route('cart.index')
                           ->with('error', 'Error al crear la preferencia de pago: ' . $e->getMessage());
        }
    }


    /**
     * Show the success page after payment
     */
    public function success()
    {
        // Verificar si hay una orden en proceso en la sesión
        $orderId = Session::get('checkout_order_id');
        
        // Si no hay orden en sesión, buscar por collection_id o external_reference de MP
        if (!$orderId) {
            $orderId = request()->query('external_reference');
        }

        if (!$orderId) {
            return redirect()->route('home')
                           ->with('error', 'No se encontró información de la orden.');
        }

        $order = Order::findOrFail($orderId);

        // Verificar parámetros de retorno de MercadoPago
        $collectionStatus = request()->query('collection_status');
        $paymentId = request()->query('collection_id');

        // Si MercadoPago confirma el pago aprobado por URL, marcar como pagado
        if ($collectionStatus === 'approved' && $order->status === 'pending') {
            $order->status = 'paid';
            $order->payment_method = request()->query('payment_type', 'mercadopago');
            if ($paymentId) {
                $order->payment_id = $paymentId;
            }
            $order->save();
        } 
        // Si no vienen parámetros pero la orden sigue pendiente, intentar consultar la API de MP
        elseif ($order->status === 'pending' && $order->payment_id) {
            try {
                SDK::setAccessToken(config('services.mercadopago.access_token'));
                // Si el payment_id en la orden es una preferencia, esto podría fallar si MP espera un payment_id real
                // Sin embargo, si el usuario llega aquí es porque probablemente ya pagó
                // Intentamos buscar el pago por la orden externa si es posible
                
                // Por ahora, si no hay confirmación explícita, dejamos que el webhook haga su trabajo
                // Pero notificamos al usuario que su pedido se está procesando
                \Log::info("Pedido #{$order->id} en success pero sin confirmación inmediata de MP.");
            } catch (\Exception $e) {
                \Log::error("Error consultando estado en success: " . $e->getMessage());
            }
        }
        
        // Si la orden ya está pagada (por webhook o por URL), procedemos
        if ($order->status === 'paid') {
            // Limpiar el carrito del usuario
            Cart::where('user_id', $order->user_id)->delete();
            // Limpiar la sesión
            Session::forget('checkout_order_id');
        }
        
        return view('checkout.success', compact('order'));
    }

    /**
     * Handle the Mercado Pago webhook
     */
    public function webhook(Request $request)
    {
        // Mercado Pago envía notificaciones de tipo "payment" o "merchant_order"
        $type = $request->input('type');
        $data = $request->input('data');

        if ($type === 'payment') {
            try {
                SDK::setAccessToken(config('services.mercadopago.access_token'));
                
                $payment = \MercadoPago\Payment::find_by_id($data['id']);
                
                // Obtener la orden usando external_reference
                $orderId = $payment->external_reference;
                $order = Order::find($orderId);

                if ($order) {
                    // Actualizar estado de la orden según el estado del pago
                    switch ($payment->status) {
                        case 'approved':
                            $order->status = 'paid';
                            $order->payment_method = $payment->payment_method_id;
                            // TODO: Enviar email de confirmación
                            // TODO: Actualizar inventario
                            break;
                        case 'pending':
                        case 'in_process':
                            $order->status = 'pending';
                            break;
                        case 'rejected':
                        case 'cancelled':
                            $order->status = 'cancelled';
                            break;
                    }
                    
                    $order->save();
                }
            } catch (\Exception $e) {
                \Log::error('Error procesando webhook de Mercado Pago: ' . $e->getMessage());
                return response()->json(['error' => 'Error processing webhook'], 500);
            }
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
