<div class="container">
    <h2>Scanner de codes-barres</h2>
    
    <div class="form-group">
        <input type="text" id="barcode-input" class="form-control" 
               placeholder="Scannez un code-barres..." autofocus>
    </div>
    
    <div id="result" class="mt-3"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcode-input');
    
    barcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const barcode = this.value.trim();
            if (barcode) {
                processScan(barcode);
                this.value = '';
            }
        }
    });
    
    function processScan(barcode) {
        fetch('{{ route("scan.barcode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ barcode: barcode })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('result').innerHTML = 
                data.success ? `<div class="alert alert-success">Produit: ${data.product.name}</div>` 
                            : `<div class="alert alert-danger">${data.message}</div>`;
        });
    }
});
</script>