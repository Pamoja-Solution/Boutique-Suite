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
                displayBarcode(barcode); // Affiche directement le code
                this.value = ''; // Réinitialise le champ
            }
        }
    });
    
    function displayBarcode(barcode) {
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = `
            <div class="alert alert-info">
                <strong>Code scanné :</strong> ${barcode}
            </div>
        `;
    }
});
</script>