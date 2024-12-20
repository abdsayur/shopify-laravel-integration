<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopify Integration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            text-align: center;
            margin-top: 50px;
        }

        .btn-primary,
        .btn-secondary {
            padding: 10px 20px;
            color: white;
            background-color: #007bff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .product-list {
            margin-top: 20px;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <button id="connect-shopify" class="btn-primary">Connect to Shopify</button>

        <div id="product-list" class="product-list" style="display:none;">
            {{-- --}}
        </div>
    </div>

    <!-- Modal -->
    <div id="product-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" id="close-modal">&times;</span>
            <h2 id="product-name"></h2>
            <p id="product-description"></p>
            <button id="push-to-shopify" class="btn-secondary">Push to Shopify</button>
        </div>
    </div>

    <script>
        document.getElementById('connect-shopify').addEventListener('click', async () => {
    window.location.href = '/connect';
    });

    const products = [
    { id: 1, name: 'Product A', price: '$10', description: 'Description A' },
    { id: 2, name: 'Product B', price: '$20', description: 'Description B' },
    ];

    const productListEl = document.getElementById('product-list');

    function displayProducts() {
    productListEl.style.display = 'block';
    productListEl.innerHTML = products.map(product => `
    <div class="product-card" data-id="${product.id}">
        <h3>${product.name}</h3>
        <p>${product.price}</p>
        <button class="view-product" data-id="${product.id}">View</button>
    </div>
    `).join('');

    document.querySelectorAll('.view-product').forEach(btn => {
    btn.addEventListener('click', event => {
    const productId = event.target.dataset.id;
    openModal(products.find(p => p.id == productId));
    });
    });
    }

    function openModal(product) {
    const modal = document.getElementById('product-modal');
    modal.style.display = 'block';
    document.getElementById('product-name').innerText = product.name;
    document.getElementById('product-description').innerText = product.description;

    document.getElementById('push-to-shopify').onclick = async () => {
    await fetch('/api/push-to-shopify', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(product)
    });
    alert('Product pushed to Shopify!');
    modal.style.display = 'none';
    };
    }

    document.getElementById('close-modal').addEventListener('click', () => {
    document.getElementById('product-modal').style.display = 'none';
    });
    </script>
</body>

</html>
