// Admin Product Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabHeaders = document.querySelectorAll('.tab-header');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all headers and panes
            tabHeaders.forEach(h => h.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            // Add active class to current header and pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Variation management
    const variationsContainer = document.getElementById('variations-container');
    const addVariationBtn = document.getElementById('add-variation');
    let variationCount = document.querySelectorAll('.variation-item').length;
    
    if (addVariationBtn) {
        addVariationBtn.addEventListener('click', function() {
            variationCount++;
            const newVariation = `
                <div class="variation-item">
                    <h4>Variation ${variationCount}</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>SKU *</label>
                            <input type="text" name="variations[new_${variationCount}][sku]" required>
                        </div>
                        <div class="form-group">
                            <label>Price *</label>
                            <input type="number" name="variations[new_${variationCount}][price]" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Sale Price</label>
                            <input type="number" name="variations[new_${variationCount}][sale_price]" step="0.01">
                        </div>
                        <div class="form-group">
                            <label>Stock *</label>
                            <input type="number" name="variations[new_${variationCount}][stock]" required>
                        </div>
                        <div class="form-group">
                            <label>Weight (g)</label>
                            <input type="number" name="variations[new_${variationCount}][weight]" step="0.01">
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="variations[new_${variationCount}][is_default]" value="1">
                                <span>Default Variation</span>
                            </label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger remove-variation">Remove</button>
                </div>
            `;
            
            variationsContainer.insertAdjacentHTML('beforeend', newVariation);
            
            // Add event listener to remove button
            const removeButtons = variationsContainer.querySelectorAll('.remove-variation');
            removeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.variation-item').remove();
                });
            });
        });
    }
    
    // Image upload management
    const imageUploads = document.getElementById('image-uploads');
    const addImageBtn = document.getElementById('add-image');
    
    if (addImageBtn) {
        addImageBtn.addEventListener('click', function() {
            const newImageUpload = `
                <div class="image-upload-item">
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="images[]" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Alt Text</label>
                        <input type="text" name="image_alt[]">
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" name="image_order[]" value="0">
                    </div>
                    <button type="button" class="btn btn-danger remove-image">Remove</button>
                </div>
            `;
            
            imageUploads.insertAdjacentHTML('beforeend', newImageUpload);
            
            // Add event listener to remove button
            const removeButtons = imageUploads.querySelectorAll('.remove-image');
            removeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.image-upload-item').remove();
                });
            });
        });
    }
    
    // Auto-generate slug from product name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    if (nameInput && slugInput) {
        nameInput.addEventListener('blur', function() {
            if (!slugInput.value) {
                const slug = this.value
                    .toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
                slugInput.value = slug;
            }
        });
    }
    
    // Form validation
    const productForm = document.querySelector('.product-form');
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }
});