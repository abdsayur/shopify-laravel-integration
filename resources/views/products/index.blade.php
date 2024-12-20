<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .product-card {
            border: 1px solid #ddd;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            width: 300px;
            position: relative;
        }

        .product-grid {
            display: flex;
            flex-wrap: wrap;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1>Product List</h1>

    <div class="product-grid">
        @foreach ($products as $product)
        <div class="product-card" data-id="{{ $product->id }}">
            <h2>{{ $product->name }}</h2>
            <p><strong>Price:</strong> ${{ $product->price }}</p>
            <p><strong>Description:</strong> {{ $product->description ?? 'No description' }}</p>
            <button class="push-button">Push to Shopify</button>
        </div>
        @endforeach
    </div>

    <!-- Modal Structure -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <h3 id="modal-title">Confirm Push</h3>
            <p id="modal-description"></p>
            <button id="confirm-push">Push to Shopify</button>
            <button id="cancel">Cancel</button>
        </div>
    </div>

    <script>
        const modal = document.getElementById('product-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalDescription = document.getElementById('modal-description');
        const confirmPush = document.getElementById('confirm-push');
        const cancel = document.getElementById('cancel');

        let selectedProductId = null;

        document.querySelectorAll('.push-button').forEach(button => {
            button.addEventListener('click', (e) => {
                const productCard = e.target.closest('.product-card');
                selectedProductId = productCard.dataset.id;

                modalTitle.innerText = `Push ${productCard.querySelector('h2').innerText} to Shopify?`;
                modalDescription.innerText = productCard.querySelector('p').innerText;

                modal.classList.add('active');
            });
        });

        confirmPush.addEventListener('click', async () => {
            const response = await fetch('/push-to-shopify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: selectedProductId })
            });

            const result = await response.json();
            alert(result.success ? "Product pushed successfully!" : "Failed to push product.");
            modal.classList.remove('active');
        });

        cancel.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    </script>
</body>

</html>
