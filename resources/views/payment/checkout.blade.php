@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Buy Questions</h1>
        <button onclick="startCheckout('basic')" class="btn btn-primary">Buy Basic (20 questions) - 250 RSD</button>
        <button onclick="startCheckout('pro')" class="btn btn-success">Buy Pro (500 questions) - 990 RSD</button>
        <iframe id="checkout-frame" src="" width="100%" height="700px" frameborder="0" style="display:none;"></iframe>
    </div>

    <script>
        function startCheckout(product) {
            fetch("{{ route('payment.create') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ product: product })
            })
            .then(response => response.json())
            .then(data => {
                if (data.checkout_url) {
                    document.getElementById("checkout-frame").src = data.checkout_url;
                    document.getElementById("checkout-frame").style.display = "block";
                } else {
                    alert("Error: Could not load checkout.");
                }
            });
        }
    </script>
@endsection
