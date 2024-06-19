<!DOCTYPE html>
<html>

<head>
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 20px;
        }

        .details th,
        .details td {
            padding: 8px;
            text-align: left;
        }

        .details th {
            background-color: #f8f8f8;
        }

        .tickets th,
        .tickets td {
            padding: 8px;
            text-align: left;
        }

        .tickets th {
            background-color: #f8f8f8;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
            <p>Thank you for your purchase, {{ $order->user->name }}!</p>
        </div>

        <div class="details">
            <h2>Order Details</h2>
            <table>
                <tr>
                    <th>Order ID</th>
                    <td>{{ $order->id }}</td>
                </tr>
                <tr>
                    <th>Transaction ID</th>
                    <td>{{ $order->payments[0]->payment_intent_id }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>${{ number_format($order->getAmount(), 2) }}</td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td>{{ $order->payment_method == \App\Enums\PaymentMethod::STRIPE ? 'Credit Card' : 'Other' }}</td>
                </tr>
                <tr>
                    <th>Showtime</th>
                    <td>
                        {{ \Carbon\Carbon::parse($order->showtime->start_time)->format('F j, Y, g:i a') }} -
                        {{ \Carbon\Carbon::parse($order->showtime->end_time)->format('g:i a') }} -
                        {{ $order->showtime->room->name }}
                    </td>
                </tr>
                <tr>
                    <th>Movie</th>
                    <td>{{ $order->showtime->movie->name }}</td>
                </tr>
            </table>
        </div>

        <div class="tickets">
            <h2>Tickets</h2>
            <table>
                <tr>
                    <th>Seat</th>
                    <th>Price</th>
                </tr>
                @foreach ($order->tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->seat->name }}</td>
                        <td>${{ number_format($ticket->price, 2) }}</td>
                    </tr>
                @endforeach
            </table>
        </div>

        <div class="footer">
            <p>If you have any questions, feel free to contact us at support@example.com.</p>
            <p>Thank you for choosing our service!</p>
        </div>
    </div>
</body>

</html>